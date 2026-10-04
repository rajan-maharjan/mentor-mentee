<?php

class skills extends common {
    const TABLE = "skills";
    const PRIMARY_ID = "skill_id";
    const USER_TABLE = "members";

    function __construct() {

    }

    function selectAll($isActive = '', $condition = FALSE, $end='', $start='') {
        $whereCond = "1 = 1 ";
        if ($isActive != '')
            $whereCond.=" AND is_active='Y'";

        if ($condition) {
            $whereCond.=" AND " . $condition;
        }
        $resultRow = parent::select(self::TABLE, array("*"), $whereCond, self::PRIMARY_ID . " desc", $groupby = "", $start, $end);
        return $resultRow;
    }

    function getDetail($skillId) {
        return $this->selectRow(self::TABLE, array("*"), self::PRIMARY_ID . "='$skillId'");
    }

    function getName($skillId) {
        $userRstl = parent::selectRow(self::TABLE, array("skill_name"), self::PRIMARY_ID . "=" . $skillId);
        $name = ucwords($userRstl->skill_name);
        return $name;
    }

    

}