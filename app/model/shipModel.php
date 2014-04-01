<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/20/14
 * Time: 12:45 PM
 */

namespace eveATcheck\model;


use eveATcheck\lib\evefit\evefit;

class shipModel extends itemModel
{


    public function loadShip(evefit $fit)
    {

    }


    public function getShipsByGroup($groupName)
    {
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('
            SELECT it.typeID, it.typeName, ig.groupName
            FROM invGroups ig
                INNER JOIN invTypes it  ON it.groupID   = ig.groupID
            WHERE
                ig.groupName = :groupName');
        $sth->bindValue(':groupName', $groupName, \PDO::PARAM_STR);
        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);

        $ships = array();
        foreach ($results as $row)
        {
            $ships[$row['typeID']] = array(
                'typeID' => $row['typeID'],
                'typeName' => $row['typeName'],
                'groupName' => $row['groupName']
            );
        }

        return $ships;
    }

    public function getShipsByType($typeName)
    {
        if (!is_array($typeName)) $typeName = array($typeName);

        $inQuery = implode(',', array_fill(0, count($typeName), '?'));
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('
            SELECT it.typeID, it.typeName, ig.groupName
            FROM invTypes it
                INNER JOIN invGroups ig ON it.groupID = ig.groupID
            WHERE
                it.typeName IN (' . $inQuery . ')');

        foreach ($typeName as $idx => $name)
            $sth->bindValue(($idx+1), $name, \PDO::PARAM_STR);

        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_ASSOC);

        $ships = array();
        foreach ($results as $row)
        {
            $ships[$row['typeID']] = array(
                'typeID' => $row['typeID'],
                'typeName' => $row['typeName'],
                'groupName' => $row['groupName']
            );
        }

        return $ships;
    }
} 