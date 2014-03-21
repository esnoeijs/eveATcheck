<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/11/14
 * Time: 5:10 PM
 */

namespace eveATcheck\lib\evefit\lib;

/**
 * Class fit
 *
 * Symbolises an eve fit containing all fit details
 *
 * @package eveATcheck\lib\evefit\lib
 */
class fit
{
    const LOWSLOT  = 'Low';
    const MIDSLOT  = 'Mid';
    const HIGHSLOT = 'High';
    const RIGSLOT  = 'Rig';
    const SUBSYSTEM = 'Subsystem';

    protected $type;
    protected $name;
    protected $group;

    protected $slots = array();

    protected $warnings = array();


    /**
     * instantiate new ship fit with name and type
     *
     * @param String $type
     * @param String $name
     */
    public function __construct($type, $name, $group)
    {
        $this->type  = $type;
        $this->name  = $name;
        $this->group = $group;
    }

    /**
     * Adds a module to a slot.
     *
     * @param String      $moduleName
     * @param String|null $chargeName
     * @param String      $slotType
     */
    public function addModule($moduleName, $chargeName, $slotType)
    {
        if (!isset($this->slots[$slotType])) $this->slots[$slotType] = array();
        $this->slots[$slotType][] = array('moduleName' => trim($moduleName), 'chargeName' => trim($chargeName));
    }

    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return String
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns array of slots which contain a array of modules.
     * @return array
     */
    public function getSlots()
    {
        return $this->slots;
    }

    /**
     * Returns modules without slot information.
     *
     * @return array
     */
    public function getModuleNames()
    {
        $modules = array();
        foreach ($this->slots as $slots)
            foreach ($slots as $module)
                $modules[] = $module['moduleName'];

        return $modules;
    }

    public function getWarnings()
    {
        $warnings = array();
        foreach ($this->warnings as $tournament => $warning)
        {
            $warnings[] = array('tournament' => $tournament, 'text' => $warning);
        }

        return $warnings;
    }

    public function hasWarning()
    {
        return (count($this->warnings)>0);
    }

    public function setWarning($tournament, $warning)
    {
        $this->warnings[$tournament] = $warning;
    }



} 