<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/13/14
 * Time: 3:10 PM
 */

namespace eveATcheck\lib\rulechecker\lib;
use eveATcheck\lib\evefit\lib\fit;
use eveATcheck\lib\evefit\lib\setup;
use eveATcheck\lib\rulechecker\rules\rule;


/**
 * Class tournament
 *
 * Tournament object against which fits and setups can be checked
 * against the rules.
 *
 * @package eveATcheck\lib\rulechecker\lib
 */
class tournament
{
    protected $name;
    protected $points = array();

    /**
     * @var rule[]
     */
    protected $rules = array();

    /**
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->parseXML($xml);
    }

    /**
     * Parse tournament XML to form full tournament object.
     *
     * @param \SimpleXMLElement $xml
     */
    protected function parseXML(\SimpleXMLElement $xml)
    {
        $this->name = (string)$xml->event->name;

        // Load up the points for ships
        foreach ($xml->points->ship as $ship)
        {
            $this->points[(string)$ship->type] = (int)$ship->points;
        }

        // Load complex rules
        foreach ($xml->restrictions->restriction as $rule)
        {
            $type = (string)$rule->type;
            $class = 'eveATcheck\lib\rulechecker\rules\\'. $type;

            if (class_exists($class))
            {
                $this->rules[] = new $class($rule);
            }else{ die("class $type not found"); }
        }
    }


    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }


    public function checkSetup(setup $setup)
    {
        $points = 0;

        // check fits
        $fits = $setup->getFits();
        /** @var fit $fit  */
        foreach ($fits as &$fit)
        {
            $fit = $this->checkFit($fit);
            $points += $fit->getPoints($this->getName());
        }

        $setup->setPoints($this->getName(), $points);
        return $setup;
    }

    public function checkFit(fit $fit)
    {
        if (!isset($this->points[strtolower($fit->getGroup())])) throw new \Exception("Unknown ship group '{$fit->getGroup()}' for fit '{$fit->getName()}'");
        $fit->setPoints($this->getName(), $this->points[strtolower($fit->getGroup())]);

        foreach ($this->rules as $rule)
        {
            if (!$rule->run($fit))
            {
                $fit->setWarning($this->getName(), $rule->getWarning());
            }
        }
        return $fit;
    }

} 