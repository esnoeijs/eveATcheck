<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/11/14
 * Time: 5:10 PM
 */

namespace eveATcheck\lib\evefit\lib;
use eveATcheck\lib\evemodel\evemodel;

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

    protected $id;
    protected $typeId;
    protected $type;
    protected $name;
    protected $shipGroup;
    protected $description;

    protected $publishDate;
    protected $updateDate;
    protected $ownerId;

    /**
     * Module slots
     * @var array
     */
    protected $slots = array();

    protected $warnings = array();

    /**
     * Amount of this fit in a setup.
     * @var int
     */
    protected $quantity =1;

    /**
     * Point category of ship type.
     *
     * @var array
     */
    protected $points;

    /**
     * instantiate new ship fit with name and type
     *
     * @param String $shipType
     * @param String $fitName
     */
    public function __construct($shipType, $fitName, $typeId, $shipGroup, $ownerId, $description=null, $id=null, $publishDate=null, $updateDate=null)
    {
        $this->typeId  = $typeId;
        $this->type    = $shipType;
        $this->name    = $fitName;
        $this->shipGroup   = $shipGroup;
        $this->ownerId = $ownerId;
        $this->description = $description;

        if (!is_numeric($id))
        {
            $this->id  = str_replace(' ','_', uniqid($fitName));
            $this->new = true;
            $this->publishDate = new \DateTime('now');
            $this->updateDate = new \DateTime('now');
        }else{
            $this->id  = $id;
            $this->new = false;
            $this->publishDate = new \DateTime($publishDate);
            $this->updateDate = new \DateTime($updateDate);
        }
    }

    public function setDescription($desc)
    {
        $this->description = $desc;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getId()
    {
        return $this->id;
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
    public function getShipGroup()
    {
        return $this->shipGroup;
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

    /**
     * Get amount of versions of this fit are in a setup
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    public function isNew()
    {
        return $this->new;
    }

    /**
     * Set amount of version of this fit are in a setup.
     *
     * @param int $amount
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }


    public function getEFT()
    {
        $EFT = "[{$this->type}, {$this->name}]".PHP_EOL;
        foreach ($this->slots as $slotName => $slots)
        {
            $EFT .= PHP_EOL;
            // drone and implant section have 2 new lines
            if (in_array($slotName, array(self::DRONES, self::IMPLANTS)))
                $EFT .= PHP_EOL;

            foreach ($slots as $module)
                $EFT .= $module['moduleName'].PHP_EOL;
        }

        return $EFT;
    }

    public function parseEFT($fitEFT, evemodel $model)
    {
        $fitEFT = new \ArrayIterator(explode(PHP_EOL, $fitEFT));

        while ($fitEFT->valid())
        {
            $fitline = $fitEFT->current();

            // Look for EFT format header
            preg_match('/^\[([a-zA-Z ]+), (.*)]/', $fitline, $matches);
            $fitEFT->next();

            if (count($matches)!==0)
            {
                $type = $matches[1];
                $name = $matches[2];
                $this->type   = $type;
                $this->name   = $name;


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
                        $module = $model->getModel('item')->getModule($moduleName);
                        if (!$module)
                        {
                            // Check if drone or implant
                            // Check for implant code AA-000
                            if (preg_match('/[A-Z]{2}-[0-9]{3}$/', $moduleName))
                            {
                                $this->addModule($moduleName, null, fit::IMPLANTS );
                            }
                            else
                            {
                                $this->addModule($moduleName, null, fit::DRONES);
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

                            $this->addModule($moduleName, $charge, $slot);
                        }
                    }

                    $fitEFT->next();
                }
            }
        }
    }
}