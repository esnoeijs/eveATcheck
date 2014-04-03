<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 4/3/14
 * Time: 10:36 AM
 */

namespace eveATcheck\lib\rulechecker\rules;


use eveATcheck\lib\evefit\lib\fit;
use eveATcheck\lib\evefit\lib\setup;

/**
 * Class oneLogi
 *
 *  Teams may field no more than 1 logistics ship, or 1 tech one support cruiser, or up to 2 support frigates.
 *
 * @package eveATcheck\lib\rulechecker\rules
 */
class oneLogi extends rule
{
    protected $warning = "Setups may contain no more than 1 logistics ship, or 1 tech one support cruiser, or up to 2 support frigates.";

    protected function _runSetup(setup $setup)
    {
        $fits = $setup->getFits();
        $types = array();

        foreach ($fits as $fit)
        {
            $types[$fit->getPointCategoryName()] = $fit->getQuantity();
        }

        // Check qty
        if (isset($types['Tech 1 Support Cruiser']) && $types['Tech 1 Support Cruiser'] > 1) return false;
        if (isset($types['Logistics Cruiser'])      && $types['Logistics Cruiser'] > 1) return false;
        if (isset($types['Tech 1 support frigate']) && $types['Tech 1 support frigate'] > 2) return false;


        // check only 1 of the 3 types
        $typeNames = array_keys($types);
        $logiCount = 0;

        if (in_array('Tech 1 Support Cruiser', $typeNames)) $logiCount++;
        if (in_array('Logistics Cruiser', $typeNames)) $logiCount++;
        if (in_array('Tech 1 support frigate', $typeNames)) $logiCount++;

        if ($logiCount>1) return false;

        return true;
    }

    protected function _runFit(fit $fit)
    {
        if (in_array($fit->getPointCategoryName(), array('Tech 1 Support Cruiser','Logistics Cruiser')))
            return ($fit->getQuantity()<=1);

        if ($fit->getPointCategoryName() == 'Tech 1 support frigate')
            return ($fit->getQuantity()<=2);

        return true;
    }
} 