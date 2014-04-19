<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/20/14
 * Time: 12:44 PM
 */

namespace eveATcheck\model;


class itemModel extends baseModel
{


    /**
     * Returns the eve groupName for a given itemName
     *
     * @param string $type
     * @return string
     */
    public function getGroupName($type)
    {
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('SELECT ig.groupName FROM invTypes it LEFT JOIN invGroups ig ON it.groupID = ig.groupID WHERE it.typeName = :moduleName');
        $sth->bindValue(':moduleName', $type, \PDO::PARAM_STR);
        $sth->execute();
        $row = $sth->fetch(\PDO::FETCH_ASSOC);

        return $row['groupName'];
    }

    public function getModule($name)
    {
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('
            SELECT it.typeName, ig.groupName, de.effectName, de.displayName
            FROM invTypes it
                INNER JOIN invGroups      ig  ON it.groupID   = ig.groupID
                INNER JOIN dgmTypeEffects dte ON it.typeID    = dte.typeID
                INNER JOIN dgmEffects     de  ON dte.effectID = de.effectID
            WHERE
                dte.effectID IN (11,12,13,2663,3772) -- low/high/med/rig/subsystem
                AND
                it.typeName = :moduleName');
        $sth->bindValue(':moduleName', $name, \PDO::PARAM_STR);
        $sth->execute();
        $row = $sth->fetch(\PDO::FETCH_ASSOC);

        return $row;
    }

    public function getItem($name)
    {
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('
            SELECT
              it.typeID,
              it.typeName,
              ig.groupName,
              ic.categoryName
            FROM invTypes it
                INNER JOIN invGroups      ig  ON it.groupID    = ig.groupID
                INNER JOIN invCategories  ic  ON ig.categoryID = ic.categoryID
            WHERE
                it.typeName = :moduleName');
        $sth->bindValue(':moduleName', $name, \PDO::PARAM_STR);
        $sth->execute();
        $row = $sth->fetch(\PDO::FETCH_ASSOC);

        return $row;
    }

    public function getItemsByGroup($groupName)
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

        return $results;
    }

    public function getItemMetaGroupByName($itemName)
    {
        $conn = $this->db->getConnection();
        $sth  = $conn->prepare('
            SELECT it.typeID, it.typeName, img.metaGroupID, img.metaGroupName
            FROM
                invTypes it
                INNER JOIN invMetaTypes imt ON it.typeID = imt.typeID
                INNER JOIN invMetaGroups img ON imt.metaGroupID = img.metaGroupID
            WHERE
            it.typeName = :itemName');
        $sth->bindValue(':itemName', $itemName, \PDO::PARAM_STR);
        $sth->execute();
        $row = $sth->fetch(\PDO::FETCH_ASSOC);

        return $row;
    }
} 