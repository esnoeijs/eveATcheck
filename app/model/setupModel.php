<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 4/1/14
 * Time: 2:27 PM
 */

namespace eveATcheck\model;


class setupModel extends baseModel
{


    public function insertSetup($name, $description, $userId)
    {
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('INSERT INTO setup (name, description, publishDate, updateDate, userId)VALUES(:name, :description, NOW(), NOW(), :userId)');
        $sth->bindValue(':name', $name, \PDO::PARAM_STR);
        $sth->bindValue(':description', $description, \PDO::PARAM_STR);
        $sth->bindValue(':userId', $userId, \PDO::PARAM_INT);
        $success = $sth->execute();

        if (!$success)
            throw new \Exception('Error inserting setup: ' . $sth->errorInfo());

        return $conn->lastInsertId();
    }

    public function updateSetup($setupId, $name, $description, $userId)
    {
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('UPDATE setup SET name = :name, description = :description, updateDate = NOW(), userId = :userId WHERE id = :setupId');
        $sth->bindValue(':name', $name, \PDO::PARAM_STR);
        $sth->bindValue(':description', $description, \PDO::PARAM_STR);
        $sth->bindValue(':userId', $userId, \PDO::PARAM_INT);
        $sth->bindValue(':setupId', $setupId, \PDO::PARAM_INT);
        $success = $sth->execute();

        if (!$success)
            throw new \Exception('Error inserting setup: ' . $sth->errorInfo());

        return true;
    }

    public function getSetups()
    {
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('SELECT s.id, s.name, s.description, s.publishDate, s.updateDate, s.userId, u.username
                                FROM setup s
                                    INNER JOIN user u ON u.id = s.userId
                                WHERE deleted IS NULL');
        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);

        return $results;
    }

    public function deleteSetup($setupId)
    {
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('UPDATE setup SET deleted = NOW() WHERE id = :setupId');
        $sth->bindValue(':setupId', $setupId, \PDO::PARAM_INT);
        $success = $sth->execute();

        if (!$success)
            throw new \Exception('Error deleting setup: ' . $sth->errorInfo());

        return true;
    }
} 
