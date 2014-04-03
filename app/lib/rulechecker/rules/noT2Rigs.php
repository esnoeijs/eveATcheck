<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/14/14
 * Time: 1:43 AM
 */

namespace eveATcheck\lib\rulechecker\rules;
use eveATcheck\lib\evefit\lib\fit;


class noT2Rigs extends rule
{
    protected $warning = 'T2 rigs are not allowed';

    protected function _runFit(fit $fit)
    {
        $fitSlots = $fit->getSlots();
        if (!isset($fitSlots['Rig'])) return true;
        foreach ($fitSlots['Rig'] as $rig)
        {
            if (strstr($rig['moduleName'], ' II')) return false;
        }
        return true;
    }
} 