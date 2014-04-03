<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 4/3/14
 * Time: 1:18 PM
 */

namespace eveATcheck\lib\rulechecker\rules;
use eveATcheck\lib\evefit\lib\fit;
use eveATcheck\lib\evefit\lib\setup;

/**
 * Class flagShip
 *
 * fits may not have faction/officer mods unless it's the flagship.
 * So we will just check that there is only 1 fit with such mods in a given setup
 *
 * @todo It's very likely that this will change frequently. Will have to wait for the AT, but will probably end up
 * having multiple versions
 * @package eveATcheck\lib\rulechecker\rules
 */
class flagship extends rule
{
    protected $warning = "Setups may only have one ship with faction modules, this would be the flagship";

    protected function _runSetup(setup $setup)
    {
        $factionFits = 0;

        /** @var fit $fit */
        foreach ($setup->getFits() as $fit)
        {
            $modules = $fit->getModuleNames();


            if ($fit->getPointCategoryName()=='Marauder')
            {
                $fit->setFlagship();
                $factionFits += $fit->getQuantity();
                break;
            }

            foreach ($modules as $module)
            {
                $row = $this->model->getModel('item')->getItemMetaGroupByName($module);
                if (in_array($row['metaGroupName'], array('Deadspace','Officer','Faction','Storyline')))
                {
                    $fit->setFlagship();
                    $factionFits += $fit->getQuantity();
                    break;
                }
            }


        }

        return ($factionFits<=1);
    }
}