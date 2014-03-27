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
    const DRONES    = 'Drones';
    const IMPLANTS  = 'Implants';

    public $slotTypes = array(self::LOWSLOT, self::MIDSLOT, self::HIGHSLOT, self::RIGSLOT, self::SUBSYSTEM, self::DRONES, self::IMPLANTS);

    protected $typeId;
    protected $type;
    protected $name;
    protected $group;
    protected $description;

    protected $slots = array();

    protected $warnings = array();

    /**
     * Point category of ship type.
     *
     * @var array
     */
    protected $points;

    /**
     * instantiate new ship fit with name and type
     *
     * @param String $type
     * @param String $name
     */
    public function __construct($typeId, $type, $name, $group)
    {
        $this->typeId = $typeId;
        $this->type   = $type;
        $this->name   = $name;
        $this->group  = $group;
    }

    public function setDescription($desc)
    {
        $this->description = $desc;
    }

    public function getDescription()
    {
        return $this->description;
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
        if (!in_array($slotType, $this->slotTypes)) throw new \Exception('Invalid slot type given');

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

    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @return String
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return String
     */
    public function getGroup()
    {
        return $this->group;
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

    public function setPointCategory($pointCategory)
    {
        $this->points = $pointCategory;
    }

    public function getPointCategoryName()
    {
        if (!is_array($this->points)) return 'Unknown';
        return $this->points['name'];
    }

    public function getPoints()
    {
        if (!is_array($this->points)) return 0;
        return $this->points['points'];
    }
}