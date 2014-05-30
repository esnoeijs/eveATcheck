<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/14/14
 * Time: 1:04 AM
 */

namespace eveATcheck\lib\rulechecker\rules;

use eveATcheck\lib\evefit\lib\fit;


class onlyOneModuleAllowed extends rule
{
    protected $warning = "Only one of [] allowed";

    protected function _runFit(fit $fit)
    {
        $found = array();
        $fitModules = $fit->getModuleNames();


        if (isset($this->options['moduleGroup']))
        {
            $moduleGroup = $this->options['moduleGroup'];
            $rows = $this->model->getModel('item')->getItemsByGroup($moduleGroup);
            foreach ($rows as $row)
                $items[] = $row['typeName'];

            foreach ($fitModules as $moduleName)
            {
                if (in_array($moduleName, $items))
                {
                    $found[$moduleName]++;
                }
            }

            foreach ($found as $qty)
            {
                if ($qty > 1) return false;
            }
        }

        if (isset($this->options['modules']))
        {
            foreach ($this->options['modules']['module'] as $module)
            {
                $found[$module] =0;
                foreach ($fitModules as $moduleName)
                {
                    if ($moduleName == $module)
                        $found[$module]++;
                }
                if ($found[$module]>1) return false;
            }
        }

        return true;
    }


} 