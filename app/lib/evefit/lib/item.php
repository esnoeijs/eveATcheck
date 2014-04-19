<?php

namespace eveATcheck\lib\evefit\lib;
use eveATcheck\lib\evemodel\evemodel;

class item
{
    protected $data;

    public static function getInstance()
    {
        return new self;
    }

    public function isModule()
    {
        return ($this->getValue('categoryName')=='Module');
    }

    public function getName()
    {
        return $this->getValue('typeName');
    }

    public function getModuleName()
    {
        return $this->getName();
    }

    public function getType()
    {
        return $this->getValue('categoryName');
    }

    public function getTypeId()
    {
        return $this->getValue('typeID');
    }

    public function getSlotType()
    {
        $slot = false;
        switch (strtolower($this->getValue('displayName')))
        {
            case 'low power': $slot = fit::LOWSLOT; break;
            case 'medium power': $slot = fit::MIDSLOT; break;
            case 'high power': $slot = fit::HIGHSLOT; break;
            case 'rig slot': $slot = fit::RIGSLOT; break;
            case 'sub system': $slot = fit::SUBSYSTEM; break;
        }
        return $slot;
    }

    public function getValue($field)
    {
        if (!isset($this->data[$field])) return false;
        return $this->data[$field];
    }

    public function setValue($field, $value)
    {
        $this->data[$field] = $value;
    }

    public function hasValue($field)
    {
        return (isset($this->data[$field]));
    }

    public function hydrate($data)
    {
        if (!is_array($data)) return;
        foreach ($data as $field => $value)
        {
            $this->setValue($field, $value);
        }
    }

    public function getEFT()
    {
        switch (strtolower($this->getType()))
        {
            case 'drone':
                return $this->getName() . ' x' . $this->getValue('qty');
                break;
            case 'module':

                if ($this->hasValue('charge'))
                    return $this->getName() . ', ' . $this->getValue('charge')->getEFT();
                else
                    return $this->getName();
                break;
            default:
                return $this->getName();
                break;

        }
    }
}