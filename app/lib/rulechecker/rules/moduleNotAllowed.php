<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 4/3/14
 * Time: 11:46 AM
 */

namespace eveATcheck\lib\rulechecker\rules;


use eveATcheck\lib\evefit\lib\fit;

class moduleNotAllowed extends rule
{
    protected function _runFit(fit $fit)
    {
        $fitModules = $fit->getModuleNames();

        $items = array();
        if (isset($this->options['moduleGroup']))
        {
            $moduleGroup = $this->options['moduleGroup'];
            $rows = $this->model->getModel('item')->getItemsByGroup($moduleGroup);
            foreach ($rows as $row)
                $items[] = $row['typeName'];
        }

        foreach ($fitModules as $module)
           if (in_array($module, $items)) return false;

        return true;
    }

} 