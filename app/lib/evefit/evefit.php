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
    public function addFit($fit, $desc, $quantity, $setupId, $save=true)
    {
        $fits = $this->parseEFT($fit);

        foreach ($fits as $fit)
        {
            $fit->setDescription($desc);
            $fit->setQuantity($quantity);
            $this->getSetup($setupId)->addFit($fit);
        }

        if ($save) $this->save();
    }

    public function updateFit($EFTFit, $desc, $quantity, $setupId, $fitId)
    {
        $fitModel = $this->model->getModel('fit');
        $fitRow  = $fitModel->getFit($fitId);

        $fit = new fit($fitRow['typeName'], $fitRow['name'], $fitRow['shiptypeId'], $fitRow['groupName'], $this->user->getId(), $desc, $fitRow['id'], $fitRow['publishDate'], $fitRow['updateDate']);
        $fit->setQuantity($quantity);
        $fit->parseEFT($EFTFit, $this->model);
        $fit->setNeedsSave(true);

        $this->getSetup($setupId)->replaceFit($fitId, $fit);

        $this->save();
    }

    /**
     * Add new setup to the list
     * @param setup $setup
     */
    public function addSetup($setup, $save=true)
    {
        $this->setups[] = $setup;
        if ($save) $this->save();
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
            {
                unset($this->setups[$key]);
                $this->model->getModel('setup')->deleteSetup($setupId);
            }
        }
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
    public function save()
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
        $fitEFTarray = new \ArrayIterator(explode(PHP_EOL, $fitEFT));
        $fits    = array();

        while ($fitEFTarray->valid())
        {
            $fitline = $fitEFTarray->current();

            // Look for EFT format header
            preg_match('/^\[([a-zA-Z ]+), (.*)]/', $fitline, $matches);
            $fitEFTarray->next();

            if (count($matches)!==0)
            {
                $fit = $this->getNewFit($matches[1], $matches[2]);
                $fit->parseEFT($fitEFT, $this->model);
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

        return new fit($ship['typeName'], $shipName, $ship['typeID'], $ship['groupName'], $this->user->getId());
    }
}