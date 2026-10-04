<?php

class appointments extends common {
    const TABLE = "mentor_mentee_appointments";
    const PRIMARY_ID = "id";

    function __construct() {

    }

    function selectAll($isActive = '', $condition = FALSE, $end='', $start='') {
        $whereCond = "1 = 1 ";
        if ($isActive != '')
            $whereCond.=" AND is_active='1'";

        if ($condition) {
            $whereCond.=" AND " . $condition;
        }
        $resultRow = parent::select(self::TABLE, array("*"), $whereCond, self::PRIMARY_ID." asc", $groupby = "", $start, $end);
        return $resultRow;
    }

    function getDetail($primaryId) {
        return $this->selectRow(self::TABLE, array("*"), self::PRIMARY_ID . "='$primaryId'");
    }

    function insertUpdate($arrayFieldValues, $primaryId = 0) {
        $excludeFields = array();
        if (intval($primaryId) > 0) {            
            parent::update(self::TABLE, $arrayFieldValues, self::PRIMARY_ID . "='$primaryId'", $excludeFields);
            return $primaryId;
        }
        else
            return parent::insert(self::TABLE, $arrayFieldValues);
    }

    function remove($primaryId) {
        return $this->delete(self::TABLE, self::PRIMARY_ID . "='$primaryId'");
    }
}