<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/20/14
 * Time: 1:07 PM
 */

namespace eveATcheck\model;
use eveATcheck\lib\database\database;

/**
 * Class baseModel
 * @package eveATcheck\model
 */
class baseModel
{
    /**
     * @var database
     */
    protected $db;

    public function __construct(database $db)
    {
        $this->db = $db;
    }
} 