<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/11/14
 * Time: 4:34 PM
 */

namespace eveATcheck\lib\evefit;


use eveATcheck\lib\evefit\lib\fit;
use eveATcheck\lib\evefit\lib\setup;
use eveATcheck\lib\evemodel\evemodel;
use eveATcheck\lib\user\user;

class evefit
{
    protected $fits   = array();
    protected $setups = array();

    /**
     * @var evemodel
     */
    protected $model;

    /**
     * @var user
     */
    protected $user;

    public function __construct(evemodel $model,user $user)
    {
        $this->model = $model;
        $this->user  = $user;

        $this->loadSetups();
    }

    /**
     * Takes EFT format fit, parses it and adds it to the list of fits.
     *
     * @param string $fit
     */
    public function addFit($fit, $setupId)
    {
        $fits = $this->parseEFT($fit);

        foreach ($fits as $fit)
            $this->getSetup($setupId)->addFit($fit);

        $this->save();
    }

    /**
     * Add new setup to the list
     * @param setup $setup
     */
    public function addSetup($setup)
    {
        $this->setups[] = $setup;
        $this->save();
    }

    /**
     * Removes a given setup from the user session
     *
     * @param String $setupId
     */
    public function deleteSetup($setupId)
    {
        foreach ($this->setups as $key => $setup)
        {
            if ($setup->getId() == $setupId)
                unset($this->setups[$key]);
        }
        $this->save();
    }

    /**
     * Returns array of setups
     *
     * @return setup[]
     */
    public function getSetups()
    {
        return $this->setups;
    }

    public function setSetups($setups)
    {
        $this->setups = $setups;
    }

    /**
     * Return setup by setupId
     *
     * @param String $setupId
     * @return bool|setup
     */
    public function getSetup($setupId)
    {
        foreach ($this->setups as $setup)
        {
            if ($setup->getId() == $setupId)
                return $setup;
        }
        return false;
    }

    /**
     * Loads setups from user session
     */
    protected function loadSetups()
    {
        $this->setups = $this->user->getSetups();
    }

    /**
     * Saves fits
     */
    protected function save()
    {
        $this->user->saveSetups($this->setups);
    }


    /**
     * Parses EFT format text to extract eve fits
     *
     * Example EFT fit:
     * [Nemesis, hank's Nemesis]
     * Ballistic Control System I
     * Micro Auxiliary Power Core I
     *
     * Experimental 1MN Afterburner I
     * J5 Prototype Warp Disruptor I
     * Medium Azeotropic Ward Salubrity I
     * Small Electrochemical Capacitor Booster I,Cap Booster 150
     *
     * Covert Ops Cloaking Device II
     * Prototype 'Arbalest' Torpedo Launcher,Inferno Torpedo
     * Prototype 'Arbalest' Torpedo Launcher,Inferno Torpedo
     * Prototype 'Arbalest' Torpedo Launcher,Inferno Torpedo
     *
     * Small Ancillary Current Router I
     * Small Ancillary Current Router I
     *
     *
     * @param $fit
     */
    protected function parseEFT($fitEFT)
    {
        $fitEFT = new \ArrayIterator(explode(PHP_EOL, $fitEFT));
        $fits    = array();

        while ($fitEFT->valid())
        {
            $fitline = $fitEFT->current();

            // Look for EFT format header
            preg_match('/^\[([a-zA-Z ]+), (.*)]/', $fitline, $matches);
            $fitEFT->next();

            if (count($matches)!==0)
            {
                $fit = $this->getNewFit($matches[1], $matches[2]);
                // @todo add some kind of error message back about not finding a good fit
                if (!$fit) continue;

                while ($fitEFT->valid())
                {
                    $fitline = $fitEFT->current();

                    // Module Name, Charge type
                    preg_match('/([^,]*)(,(.*))?/', $fitline, $matches);

                    // If we stumbled on a new fit. (new fits start with "[")
                    if (preg_match('/^\[/',$fitline))
                    {
                        break;
                    }

                    $charge = null;
                    $moduleName = $matches[1];

                    // If white line just skip.
                    if (trim($moduleName)!=='')
                    {
                        /**
                         * @todo this is not really the best solution. But I'm trying to avoid creating half a fitting
                         * application in this site.
                         */
                        $module = $this->getModule($moduleName);
                        if (!$module)
                        {
                            // Check if drone or implant
                            // Check for implant code AA-000
                            if (preg_match('/[A-Z]{2}-[0-9]{3}$/', $moduleName))
                            {
                                $fit->addModule($moduleName, null, fit::IMPLANTS );
                            }
                            else
                            {
                                $fit->addModule($moduleName, null, fit::DRONES);
                            }
                        }
                        else
                        {
                            if (isset($matches[3]))
                            {
                                $charge = $matches[3];
                            }

                            $slot = null;
                            switch (strtolower($module['displayName']))
                            {
                                case 'low power': $slot = fit::LOWSLOT; break;
                                case 'medium power': $slot = fit::MIDSLOT; break;
                                case 'high power': $slot = fit::HIGHSLOT; break;
                                case 'rig slot': $slot = fit::RIGSLOT; break;
                                case 'sub system': $slot = fit::SUBSYSTEM; break;
                                default:
                                    throw new \Exception("Don't know slot type: '{$module['displayName']}''");
                                    break;
                            }

                            $fit->addModule($moduleName, $charge, $slot);
                        }
                    }

                    $fitEFT->next();
                }
                $fits[] = $fit;
            }
        }

        return $fits;
    }

    /**
     * Creates a new fit object.
     *
     * @param String $name
     * @param String $shipName
     * @return fit
     */
    protected function getNewFit($shipType, $shipName)
    {
        $result = $this->model->getModel('ship')->getShipsByType($shipType);
        if (count($result)==0) return false;
        $ship = array_shift($result);

        return new fit($ship['typeID'], $ship['typeName'], $shipName, $ship['groupName']);
    }

    /**
     * Returns a data row with some information about which slot the module is in.
     *
     * @param String $name
     * @return Array|null
     */
    protected function getModule($name)
    {
        return $this->model->getModel('item')->getModule($name);
    }




} 