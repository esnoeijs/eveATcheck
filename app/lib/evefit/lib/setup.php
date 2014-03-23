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

    protected $fits;

    public function __construct($name, $desc)
    {
        $this->id   = uniqid($name);
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

    public function getFits()
    {
        return $this->fits;
    }

    public function addFit($fit)
    {
        $this->fits[] = $fit;
    }

} 