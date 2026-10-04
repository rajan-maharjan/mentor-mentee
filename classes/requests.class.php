<?php

class requests extends common {
    const TABLE = "mentor_mentee_requests";
    const PRIMARY_ID = "request_id";

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

    function getDetail($requestId) {
        return $this->selectRow(self::TABLE, array("*"), self::PRIMARY_ID . "='$requestId'");
    }

    function checkRequestExists($requestedTo) {
        return $this->selectRow(self::TABLE, array("*"), "is_active!='2' and status!='C' and requested_to='$requestedTo' and requested_by='".$_SESSION['session_user_id']."'");
    }

    function approveRequest($requestId) {
        return $this->update(self::TABLE, array("status"=>'A', 'approved_on'=>date("Y-m-d H:i:s")), self::PRIMARY_ID . "='$requestId'");
    }

    function completeRequest($requestId) {
        return $this->update(self::TABLE, array("status"=>'C', 'completed_on'=>date("Y-m-d H:i:s")), self::PRIMARY_ID . "='$requestId'");
    }

    function cancelRequest($requestId) {
        return $this->delete(self::TABLE, self::PRIMARY_ID . "='$requestId'");
    }
}