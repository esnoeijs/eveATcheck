<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/11/14
 * Time: 5:10 PM
 */

namespace eveATcheck\lib\evefit\lib;
use eveATcheck\lib\database\database;

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

    protected $slots = array();

    protected $warnings = array();

    protected $db;

    /**
     * instantiate new ship fit with name and type
     *
     * @param String $type
     * @param String $name
     */
    public function __construct(database $db, $type, $name)
    {
        $this->type = $type;
        $this->name = $name;

        $this->groupName = $this->getGroupName($db, $type);
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


    protected function getGroupName($db, $type)
    {
        $conn = $db->getConnection();
        $sth  = $conn->prepare('SELECT ig.groupName FROM invTypes it LEFT JOIN invGroups ig ON it.groupID = ig.groupID WHERE it.typeName = :moduleName');
        $sth->bindValue(':moduleName', $type, \PDO::PARAM_STR);
        $sth->execute();
        $row = $sth->fetch(\PDO::FETCH_ASSOC);

        return $row['groupName'];
    }
} 