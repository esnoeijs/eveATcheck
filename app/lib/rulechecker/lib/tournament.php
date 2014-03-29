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
use eveATcheck\lib\evemodel\evemodel;
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
    protected $maxPoints = 0;
    protected $maxPilots = 0;

    /**
     * @var rule[]
     */
    protected $rules = array();

    /**
     * @param \SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml, evemodel $model)
    {
        $this->parseXML($xml, $model);
    }

    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getMaxPoints()
    {
        return $this->maxPoints;
    }

    /**
     * @return int
     */
    public function getMaxPilots()
    {
        return $this->maxPilots;
    }

    /**
     * Parse tournament XML to form full tournament object.
     *
     * @param \SimpleXMLElement $xml
     */
    protected function parseXML(\SimpleXMLElement $xml, evemodel $model)
    {
        $this->name   = (string)$xml->event->name;
        $this->maxPoints = (int)$xml->event->points;
        $this->maxPilots = (int)$xml->event->pilots;
        $this->points = $this->loadShipPoints($xml->points->ship, $model);

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
     * Loop trough the different ship categories as defined in the XML and extract the points and ships from
     * each category.
     *
     * Ships are defined by either groups of ships or specific ships, groups of ships are pulled from their associated
     * group name from the database. Ships are taken by name from the database.
     *
     * Specifically named ships will then be removed from other categories where they where defined via the group method.
     * A ship can only be defined in one category.
     *
     * @param \SimpleXMLElement $xml
     * @param evemodel $model
     * @return array
     * @throws \Exception
     */
    protected function loadShipPoints(\SimpleXMLElement $xml, evemodel $model)
    {
        $points = array();
        foreach ($xml as $rule)
        {
            $shipCat = array();
            $shipCat['name'] = (string)$rule->type;
            $shipCat['points'] = (int)$rule->points;
            $shipCat['ships']  = array();


            // ship groups can be defined by either groups or specific ships.
            // If specific ships are defined they will be removed from those listings that defined those ships via groups

            // Get ships by groups
            if (isset($rule->define->group))
            {
                foreach ($rule->define->group as $definedGroup)
                {
                    $shipsRows = $model->getModel('ship')->getShipsByGroup($definedGroup);
                    $ships = array();
                    foreach ($shipsRows as $row)
                    {
                        $ships[$row['typeID']] = $row['typeName'];
                    }

                    // Check trough previous assigned points and remove ships from the list that are already defined.
                    // As a rule, specifically assigned ships get precedence over blanked shipType assigned lists
                    foreach ($points as $prevSetShip)
                    {
                        foreach ($prevSetShip['ships'] as $prevTypeID => $prevShipName)
                        {
                            if (isset($ships[$prevTypeID])) unset($ships[$prevTypeID]);
                        }
                    }
                    $shipCat['ships'] = $ships + $shipCat['ships'];
                }
            }

            //  If specific ships are defined we'll take those
            // If not we fetch them from the database by the given type
            if (isset($rule->define->ship))
            {
                $shipNames = (array)$rule->define->ship;
                $shipsRows = $model->getModel('ship')->getShipsByType($shipNames);
                $ships = array();
                foreach ($shipsRows as $row)
                {
                    $ships[$row['typeID']] = $row['typeName'];
                }

                if (count($shipNames) !== count($ships))
                {
                    $foundShips = array_map("strtolower", $ships);

                    $notFound = array();
                    foreach ($shipNames as $shipName)
                    {
                        if (!in_array(strtolower($shipName), $foundShips))
                        {
                            throw new \Exception("Rule XML error ship name '$shipName' not found.");
                        }
                    }
                }


                // Check trough previously assigned points and remove ships from their lists that have been found here
                // As a rule, specifically assigned ships get precedence over blanked shipType assigned lists
                foreach ($points as $key => $prevSetShip)
                {
                    foreach ($prevSetShip['ships'] as $prevTypeID => $prevShipName)
                    {
                        if (isset($ships[$prevTypeID]))
                        {
                            unset($points[$key]['ships'][$prevTypeID]);
                        }
                    }
                }
                unset($prevSetShip);

                $shipCat['ships'] = $ships;
            }


            $points[] = $shipCat;
        }

        return $points;
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
            $points += $fit->getPoints() * $fit->getQuantity();
        }

        $setup->setPoints($points);
        return $setup;
    }

    public function checkFit(fit $fit)
    {
        $fit->setPointCategory($this->getPointCategory($fit));

        foreach ($this->rules as $rule)
        {
            if (!$rule->run($fit))
            {
                $fit->setWarning($this->getName(), $rule->getWarning());
            }
        }
        return $fit;
    }

    protected function getPointCategory(fit $fit)
    {
        foreach ($this->points as $pointGroup)
        {
            if (isset($pointGroup['ships'][$fit->getTypeId()]))
                return $pointGroup;
        }

        return -1;
    }

} 