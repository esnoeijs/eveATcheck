<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 24-5-2015
 * Time: 1:55
 */

namespace eveATcheck\lib\rulechecker\rules;


use eveATcheck\lib\evefit\lib\setup;

class twoShipsPerType extends rule
{
    protected $warning = "Only 2 of each ship type allowed";
    
    public function runSetup(setup $setup)
    {
        $ships = [];
        foreach ($setup->getFits() as $ship) {
            $ships[$ship->getType()] = (isset($ships[$ship->getType()]) ? $ships[$ship->getType()] + $ship->getQuantity() : $ship->getQuantity());

            if ($ships[$ship->getType()] > 2) return false;
        }

        return true;
    }

} 
