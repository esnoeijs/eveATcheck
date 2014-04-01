<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 4/1/14
 * Time: 3:23 PM
 */

namespace eveATcheck\model;


class fitModel extends baseModel
{
    /**
     * Inserts a new fit into the database.
     *
     * @param string $name
     * @param string $description
     * @param int    $qty
     * @param int    $shipTypeId
     * @param string $eftData
     * @param int    $userId
     * @param int    $setupId
     * @return bool
     * @throws \Exception
     */
    public function insertFit($name, $description, $qty, $shipTypeId, $eftData, $userId, $setupId)
    {
        $conn = $this->db->getConnection();
        $conn->beginTransaction();
        $sth  = $conn->prepare('INSERT INTO fit (name, setupId, description, qty, publishDate, updateDate)VALUES(:name, :setupId, :description, :qty, NOW(), NOW())');
        $sth->bindValue(':name', $name, \PDO::PARAM_STR);
        $sth->bindValue(':description', $description, \PDO::PARAM_STR);
        $sth->bindValue(':qty', $qty, \PDO::PARAM_INT);
        $sth->bindValue(':setupId', $setupId, \PDO::PARAM_INT);
        $success = $sth->execute();

        if (!$success)
        {
            $conn->rollBack();
            throw new \Exception('Error inserting fit: ' . print_r($sth->errorInfo(), true));
        }

        $fitId = $conn->lastInsertId();

        $sth = $conn->prepare('INSERT INTO fitData (fitId, EFTData, shiptypeId, publishDate, userId)VALUES(:fitId, :eftData, :shiptypeId, NOW(), :userId )');
        $sth->bindValue(':fitId', $fitId, \PDO::PARAM_INT);
        $sth->bindValue(':eftData', $eftData, \PDO::PARAM_STR);
        $sth->bindValue(':shiptypeId', $shipTypeId, \PDO::PARAM_INT);
        $sth->bindValue(':userId', $userId, \PDO::PARAM_INT);
        $success = $sth->execute();

        if (!$success)
        {
            $conn->rollBack();
            throw new \Exception('Error inserting fit: ' . print_r($sth->errorInfo(), true));
        }

        $conn->commit();

        return true;
    }

    /**
     * Returns latest data for all fits.
     *
     * @return array
     * @throws \Exception
     */
    public function getFits()
    {
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('
            SELECT
                f.id, f.qty, f.name, f.description, f.publishDate, f.updateDate, fd.userId, fd.EFTData, fd.shiptypeId, it.typeName, ig.groupName, f.setupId
            FROM
                fit f
                INNER JOIN fitData fd ON fd.fitId = f.id AND fd.id = (SELECT MAX(id) FROM fitData WHERE fitId = f.id)
                INNER JOIN invTypes it ON it.typeID = fd.shiptypeId
                LEFT JOIN invGroups ig ON it.groupID = ig.groupID
        ');

        if (!$sth->execute())
            throw new \Exception('Error fetching fits: ' . print_r($sth->errorInfo(), true));


        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);

        return $results;
    }
} 