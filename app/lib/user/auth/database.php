<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/21/14
 * Time: 1:31 PM
 */

namespace eveATcheck\lib\user\auth;


use eveATcheck\lib\evemodel\evemodel;

class database extends auth
{
    protected $model;

    public function __construct(evemodel $model)
    {
        $this->model = $model;
    }

    /**
     * Verifies the given password for a user against the database
     *
     * @param String $username
     * @param String $password
     * @return bool
     */
    public function login($username, $password)
    {
        $user = $this->model->getModel('user')->getUser($username);
        return password_verify($password, $user['password']);
    }

    /**
     * Creates a hash from the password and inserts the new user into the database.
     *
     * @param String $username
     * @param String $password
     * @return mixed
     */
    public function register($username, $password)
    {
        $hash = password_hash($password, PASSWORD_BCRYPT, array('cost' => 11));
        return $this->model->getModel('user')->insertUser($username, $hash);
    }



} 