<?php
/**
 * Created by PhpStorm.
 * User: erik
 * Date: 3/20/14
 * Time: 12:44 PM
 */

namespace eveATcheck\model;


class item extends baseModel
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
} 