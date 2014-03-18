<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/13/14
 * Time: 2:54 PM
 */

namespace eveATcheck\lib\database;

/**
 * Just a PDO wrapper
 *
 * Class database
 * @package eveATcheck\lib\database\
 */
class database
{
    protected $conn = null;

    public function __construct($dbhost,$dbport,$dbname,$dbuser,$dbpass)
    {
        $this->conn = new \PDO("mysql:host=$dbhost;port=$dbport;dbname=$dbname", $dbuser, $dbpass, array( \PDO::ATTR_PERSISTENT => false));
    }

    public function getConnection()
    {
        return $this->conn;
    }

} 