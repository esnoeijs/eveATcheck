<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/20/14
 * Time: 12:45 PM
 */

namespace eveATcheck\model;


class userModel extends baseModel
{
    public function userExists($username)
    {
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('SELECT username FROM user  WHERE username = :username');
        $sth->bindValue(':username', $username, \PDO::PARAM_STR);
        $sth->execute();
        $row = $sth->fetch(\PDO::FETCH_ASSOC);

        return ($row['username']);
    }

    public function getPassword($username)
    {
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('SELECT username,password FROM user WHERE username = :username');
        $sth->bindValue(':username', $username, \PDO::PARAM_STR);
        $sth->execute();
        $row = $sth->fetch(\PDO::FETCH_ASSOC);

        return $row;
    }

    public function getUser($username)
    {
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('SELECT id, username, valid, admin FROM user WHERE username = :username');
        $sth->bindValue(':username', $username, \PDO::PARAM_STR);
        $sth->execute();
        $row = $sth->fetch(\PDO::FETCH_ASSOC);

        return $row;
    }

    public function insertUser($username, $hash)
    {
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('INSERT INTO user (username, password)VALUES(:username, :password)');
        $sth->bindValue(':username', $username, \PDO::PARAM_STR);
        $sth->bindValue(':password', $hash, \PDO::PARAM_STR);
        $success = $sth->execute();

        if (!$success)
            throw new \Exception('Error inserting user: ' . $sth->errorInfo());

        return true;
    }

    public function getAll()
    {
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('SELECT id, username, valid FROM user');
        $sth->execute();

        $users = array();
        while ($user = $sth->fetch(\PDO::FETCH_ASSOC)) {
            $users[] = $user;
        }

        return $users;
    }

    public function toggleValid($userId)
    {
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('UPDATE user SET valid = valid XOR 1 WHERE id = :userId');
        $sth->bindValue(':userId', $userId, \PDO::PARAM_INT);
        $success = $sth->execute();

        if (!$success)
            throw new \Exception('Error toggling valid status user: ' . $sth->errorInfo());

        return true;
    }
} 
