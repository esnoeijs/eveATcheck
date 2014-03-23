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
     * @todo rewrite fit parsing to use the DB to find out which slot an item belongs too and not try to guess it
     * @param $fit
     */
    protected function parseEFT($fitEFT)
    {
        $fitEFT = new \ArrayIterator(explode(PHP_EOL, $fitEFT));

        while ($fitEFT->valid())
        {
            $fits    = array();
            $fitline = $fitEFT->current();

            // Look for EFT format header
            preg_match('/^\[([a-zA-Z ]+), (.*)]/', $fitline, $matches);
            $fitEFT->next();

            if (count($matches)!==0)
            {
                $fit = $this->getNewFit($matches[1], $matches[2]);

                // EFT will have listed modules separated by a double linebreak to separate between slot types
                foreach (array(fit::LOWSLOT, fit::MIDSLOT, fit::HIGHSLOT, fit::RIGSLOT, fit::SUBSYSTEM) as $slotType)
                {
                    while ($fitEFT->valid())
                    {
                        $fitline = $fitEFT->current();

                        // Module Name, Charge type
                        preg_match('/([^,]*)(,(.*))?/', $fitline, $matches);

                        // Empty line signifies end of block of modules for this slot type.
                        if (trim($matches[0])=="")
                        {
                            $fitEFT->next();
                            break;
                        }
                        else
                        {
                            $charge = null;
                            $module = $matches[1];
                            if (isset($matches[3]))
                            {
                                $charge = $matches[3];
                            }

                            $fit->addModule($module, $charge, $slotType);
                        }

                        $fitEFT->next();
                    }
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
    protected function getNewFit($name, $shipName)
    {
        $groupName = $this->model->getModel('ship')->getGroupName($shipName);
        return new fit($name, $shipName, $groupName);
    }


} 