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


    protected $new;
    protected $needsSave = false;


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

    protected $flagship=false;


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
            $this->needsSave = true;
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
    public function addItem(item $item)
    {
        switch (strtolower($item->getType()))
        {
            case 'module':
                $this->slots[$item->getSlotType()][] = $item;
                break;
            case 'drone':
                $this->slots[fit::DRONES][] = $item;
                break;
            case 'implant':
                $this->slots[fit::IMPLANTS][] = $item;
                break;
            case 'subsystem':
                $this->slots[fit::SUBSYSTEM][] = $item;
                break;
        }
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

    public function getUpdateDate()
    {
        return $this->updateDate;
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
                $modules[] = $module->getName();

        return $modules;
    }

    public function getWarnings()
    {
        return $this->warnings;
    }

    public function hasWarning()
    {
        return (count($this->warnings)>0);
    }

    public function setWarning($tournament, $warning)
    {
        $this->warnings[] = array('tournament' => $tournament, 'text' => $warning);
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
     * @param boolean $needsSave
     */
    public function setNeedsSave($needsSave)
    {
        $this->needsSave = $needsSave;
    }

    /**
     * @return boolean
     */
    public function getNeedsSave()
    {
        var_dump(array('name' => $this->getName(), 'needsSave' => $this->needsSave));
        return $this->needsSave;
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

    /**
     * sets ship as flagship
     */
    public function setFlagship()
    {
        $this->flagship = true;
    }

    /**
     * @return boolean
     */
    public function getFlagship()
    {
        return $this->flagship;
    }

    public function getDNA()
    {
        $modules[] = array();
        $modules['charge'] = array();
        $slots = $this->getSlots();
        foreach ($slots as $slotType => $slotModules)
        {
            if (!isset($modules[$slotType]))
                $modules[$slotType] = array();

            /** @var item $slotModule */
            foreach ($slotModules as $slotModule)
            {
                if (!isset($modules[$slotType][$slotModule->getTypeId()]))
                    $modules[$slotType][$slotModule->getTypeId()] = 0;

                switch (strtolower($slotModule->getType()))
                {
                    case 'drone':
                        $modules[$slotType][$slotModule->getTypeId()] = $slotModule->getValue('qty');
                        break;
                    case 'module':
                        $modules[$slotType][$slotModule->getTypeId()] += 1;
                        if ($slotModule->hasValue('charge'))
                        {
                            $charge = $slotModule->getValue('charge');
                            if (!isset($modules['charge'][$charge->getTypeId()]))
                                $modules['charge'][$charge->getTypeId()] = 0;
                            $modules['charge'][$charge->getTypeId()] += 1;
                        }
                        break;
                    default:
                        $modules[$slotType][$slotModule->getTypeId()] += 1;
                        break;
                }
            }
        }

        $slotOrder = array(self::SUBSYSTEM, self::HIGHSLOT, self::MIDSLOT, self::LOWSLOT, self::RIGSLOT, self::DRONES, 'charge');
        $dna = $this->getTypeId() . ':';
        foreach ($slotOrder as $slotType)
        {
            if (!isset($modules[$slotType]))
                continue;

            foreach ($modules[$slotType] as $modTypeId => $qty)
            {
                $dna .= $modTypeId . ";" . $qty . ':';
            }
        }
        return $dna . ':';
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
                $EFT .= $module->getEFT().PHP_EOL;
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

                    if (preg_match('/^\[([a-zA-Z ]+), (.*)]/',$fitline))
                    {
                        break;
                    }

                    // Module Name, Charge type
                    preg_match('/([^,]*)(,(.*))?/', $fitline, $matches);

                    $charge = null;
                    $moduleName = $matches[1];

                    // If white line just skip.
                    if (trim($moduleName)!=='')
                    {
                        $module = item::getInstance();

                        $module->hydrate($model->getModel('item')->getItem($moduleName));
                        if ($module->isModule())
                            $module->hydrate($model->getModel('item')->getModule($moduleName));

                        if (!$module->isModule())
                        {

                            if ($module->getType()=='Subsystem')
                            {
                                $this->addItem($module);
                            }

                            // Drones, becouse they have a " x#" behind their name
                            // this is really stupid, but meh.
                            if (preg_match('/(^.*) x([0-9]+)$/', $moduleName, $match))
                            {
                                $module->hydrate($model->getModel('item')->getItem($match[1]));
                                $module->setValue('qty', $match[2]);
                                $this->addItem($module);
                            }

                            $module->hydrate($model->getModel('item')->getItem($moduleName));
                            if ($module->getValue('categoryName') == 'Implant')
                            {
                                $this->addItem($module);
                            }
                        }
                        else
                        {
                            // If we have a charge
                            if (isset($matches[3]) && trim($matches[3])!='')
                            {
                                $chargeItem = item::getInstance();
                                $chargeItem->hydrate($model->getModel('item')->getItem(trim($matches[3])));

                                $module->setValue('charge', $chargeItem);
                            }

                            $this->addItem($module);
                        }
                    }

                    $fitEFT->next();
                }
            }
        }
    }
}