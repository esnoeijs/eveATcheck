<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/13/14
 * Time: 2:11 AM
 */

namespace eveATcheck\lib\user;
use eveATcheck\lib\evemodel\evemodel;
use eveATcheck\lib\user\auth\auth;

/**
 * Class user
 * @package eveATcheck\lib\user
 */
class user
{
    protected $model;
    protected $auth;
    protected $loggedin = false;

    public function __construct(evemodel $model, auth $auth)
    {
        $this->model = $model;
        $this->auth  = $auth;
    }


    public function isLoggedin()
    {
        return $this->loggedin;
    }

    public function login($user, $password)
    {
        $login = $this->auth->login($user, $password);
        if (!$login) return false;

        return true;
    }

    public function logout()
    {

    }

    public function register($username, $password)
    {
        return $this->auth->register($username,$password);
    }

    /**
     * Retrieves fits from the database or session depending on if the user is loggedin
     * @return array
     */
    public function getFits()
    {
        if ($this->loggedin)
        {

        } else {
            if (!isset($_SESSION['fits'])) $_SESSION['fits'] = array();
            $fits = $_SESSION['fits'];
        }
        return $fits;
    }

    /**
     * Saves fits to session or database depending on if the user is loggedin.
     * @return bool
     */
    public function saveFits($fits)
    {
        if ($this->loggedin)
        {

        } else {
            if (!isset($_SESSION['fits'])) $_SESSION['fits'] = array();
            $_SESSION['fits'] = $fits;
        }
        return true;
    }

    /**
     * Retrieves fits from the database or session depending on if the user is loggedin
     * @return array
     */
    public function getSetups()
    {
        if ($this->loggedin)
        {

        } else {
            if (!isset($_SESSION['setups'])) $_SESSION['setups'] = array();
            $setups = $_SESSION['setups'];
        }
        return $setups;
    }

    /**
     * Saves fits to session or database depending on if the user is loggedin.
     * @return bool
     */
    public function saveSetups($setups)
    {
        if ($this->loggedin)
        {

        } else {
            if (!isset($_SESSION['setups'])) $_SESSION['setups'] = array();
            $_SESSION['setups'] = $setups;
        }
        return true;
    }
} 