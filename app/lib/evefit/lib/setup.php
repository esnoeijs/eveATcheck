<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/22/14
 * Time: 1:08 PM
 */

namespace eveATcheck\lib\evefit\lib;


class setup
{
    protected $id;
    protected $name;
    protected $desc;

    protected $points = array();

    /**
     * @var fit[]
     */
    protected $fits = array();

    public function __construct($name, $desc)
    {
        $this->id   = str_replace(' ','_', uniqid($name));
        $this->name = $name;
        $this->desc = $desc;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDesc()
    {
        return $this->desc;
    }

    public function getFit($fitId)
    {
        foreach ($this->fits as $fit)
        {
            if ($fit->getId() == $fitId)
                return $fit;
        }
        return false;
    }

    public function getFits()
    {
        return $this->fits;
    }

    public function setFits($fits)
    {
        $this->fits = $fits;
    }

    public function deleteFit($fitId)
    {
        foreach ($this->fits as $key => $fit)
        {
            if ($fit->getId() == $fitId)
            {
                unset($this->fits[$key]);
                return true;
            }
        }
        return false;
    }

    public function addFit($fit)
    {
        $this->fits[] = $fit;
    }

    public function replaceFit($fitId, $newFit)
    {
        foreach ($this->fits as $key => $fit)
        {
            print $fit->getId()." - " . $fitId."<br/>";
            if ($fit->getId() == $fitId)
            {
                $this->fits[$key] = $newFit;
                return true;
            }
        }
        return false;
    }

    public function setPoints($points)
    {
        $this->points = $points;
    }

    public function getPoints()
    {
        return $this->points;
    }

} 