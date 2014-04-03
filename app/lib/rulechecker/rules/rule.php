<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/14/14
 * Time: 1:03 AM
 */

namespace eveATcheck\lib\rulechecker\rules;

use eveATcheck\lib\evefit\lib\fit;
use eveATcheck\lib\evefit\lib\setup;

abstract class rule
{
    protected $options = array();
    protected $warning;

    /**
     *
     * @todo Should really make recursive
     * @param \SimpleXMLElement $rule
     */
    public function __construct(\SimpleXMLElement $rule)
    {
        foreach ($rule as $key => $node)
        {
            if ($node->count() == 0)
                $this->options[$key] = (string) $node;
            else
                $this->options[$key] = (array) $node;
        }

        $this->constructWarning();
    }

    public function runFit(fit $fit)
    {
        return $this->_runFit($fit);
    }

    protected function _runFit(fit $fit)
    {
        return true;
    }

    public function runSetup(setup $setup)
    {
        return $this->_runSetup($setup);
    }

    protected function _runSetup(setup $fit)
    {
        return true;
    }

    public function getWarning()
    {
        return $this->warning;
    }

    protected function constructWarning()
    {
        if (isset($this->options['warning']))
            $this->warning = $this->options['warning'];
    }

} 