<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/20/14
 * Time: 12:50 PM
 */

namespace eveATcheck\lib\evemodel;

/**
 * Class evemodel
 *
 * Just a simple class to pass on models
 *
 * @package eveATcheck\lib\evemodel
 */
class evemodel
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getModel($modelName)
    {
        $modelClass = '\\eveATcheck\model\\' . $modelName;

        if (class_exists($modelClass))
        {
            $model = new $modelClass($this->db);
            return $model;
        }

        throw new \Exception("Model '$modelClass' does not exist");
    }

} 