<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/14/14
 * Time: 1:03 AM
 */

namespace eveATcheck\lib\rulechecker\rules;

use eveATcheck\lib\evefit\lib\fit;

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

    public function run(fit $fit)
    {
        return $this->_run($fit);
    }

    protected function _run()
    {
        throw new \Exception('_run needs to be implemented');
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