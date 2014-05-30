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
 * This is the XII version,
 * Any standard T1 or Faction battleship hull may be designated as a flagship.
 *
 *
 * @package eveATcheck\lib\rulechecker\rules
 */
class flagshipXII extends rule
{
    protected $warning = "Setups may only have one ship with faction modules, this would be the flagship";

    protected function _runSetup(setup $setup)
    {
        $factionFits = 0;

        /** @var fit $fit */
        foreach ($setup->getFits() as $fit)
        {
            $modules = $fit->getModuleNames();

            if (!in_array($fit->getPointCategoryName(), array('Battleship','Battleship, Navy Faction','Battleship, Pirate Faction','Dominix')))
            {
                continue;
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