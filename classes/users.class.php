<?php

class users extends common {

    const TABLE = "members";
    const PRIMARY_ID = "id";
    const USER_LOG_ID = "id";
    const TBL_USER_LOG_INFO = "user_log_info";
    const TBL_MENTOR_MENTEE = 'mentor_mentee_requests';

    function __construct() {
        
    }
    
    function getWPUserEmailDELETE($jwt_token) {
        $url = TIN_WEBSITE.'wp-json/custom/v1/user/email';
    
        $options = array(
            'http' => array(
                'header'  => "Authorization: Bearer $jwt_token\r\n",
                'method'  => 'GET',
            ),
        );
    
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
    
        if ($result === FALSE) {
            throw new Exception('Failed to fetch user details');
        }
    
    
        $response = json_decode($result, true);
    
        if (isset($response['email'])) {
            return $response['email'];
        } else {
            throw new Exception('Email address not found in response');
        }
    }

    function insertUpdate($arrayFieldValues, $memberId = 0) {
        $excludeFields = array();
        if (intval($memberId) > 0) {
            if ($arrayFieldValues['password'] == '') {
                array_push($excludeFields, "pass_word");
            }
            
            parent::update(self::TABLE, $arrayFieldValues, self::PRIMARY_ID . "='$memberId'", $excludeFields);
            return $memberId;
        }
        else
            return parent::insert(self::TABLE, $arrayFieldValues);
    }

    function insertMentorMentee($arrayFieldValues) {
        return parent::insert(self::TBL_MENTOR_MENTEE, $arrayFieldValues);
    }

    function authChangePwd($password,$userId=0){		
		$userRslt=parent::selectRow(self::TABLE,array(self::PRIMARY_ID)," pass_word=('".md5($password)."') AND ".self::PRIMARY_ID."='".$userId."'");
		$fieldName=self::PRIMARY_ID;
		return intval($userRslt->$fieldName);
	}
		
    function changePassword($arrayFieldValues, $memberId = 0) {
        parent::update(self::TABLE, $arrayFieldValues, self::PRIMARY_ID . "='$memberId'");
    }

    function getLatestMembers($isActive = '', $arrayFields = array("member_id", "full_name", "email","mobile_number","show_mobile","profile_link"), $userType = 2){
            $whereCond = self::PRIMARY_ID . ">0" ;
            if ($isActive != '')
                $whereCond.=" AND is_active='" . $isActive . "'";
    
            if ($userType > 0)
                $whereCond.=" AND member_type='" . $userType . "'";
    
            $resultRow = parent::select(self::TABLE, $arrayFields, $whereCond, " RAND() desc","",0,10);
            return $resultRow;
    }

    function selectAll($isActive = '', $arrayFields = array("*"), $userType = 2) {
        $whereCond = self::PRIMARY_ID . ">0" ;
        if ($isActive != '')
            $whereCond.=" AND is_active='" . $isActive . "'";

        if ($userType > 0)
            $whereCond.=" AND member_type='" . $userType . "'";

        $resultRow = parent::select(self::TABLE, $arrayFields, $whereCond, self::PRIMARY_ID . " desc");
        return $resultRow;
    }

    function getIndividualMentors() {
        $whereCond = "is_active='1' and mentor_for like '%I%'" ;
        $resultRow = parent::select(self::TABLE, array(self::PRIMARY_ID), $whereCond);
        return $resultRow;
    }

    function getClubMentors() {
        $whereCond = "is_active='1' and mentor_for like '%C%'" ;
        $resultRow = parent::select(self::TABLE, array(self::PRIMARY_ID), $whereCond);
        return $resultRow;
    }

    function checkExists($memberId='0', $email = '',$phone='') {        
        if ($memberId > 0)
            return $this->selectRow(self::TABLE, array(self::PRIMARY_ID), "(email='" . $email . "' or mobile_number='".$phone."') AND member_id !='" . $memberId . "'");
        else
            return $this->selectRow(self::TABLE, array(self::PRIMARY_ID), "(email='" . $email . "' or mobile_number='".$phone."')");
    }

    function checkLogin() {//$session_val
        if (isset($_SESSION['memberId']))
            return true;
        else {
            echo "<div class='input-error'>You are not logged in. Please login to take action</div>";
            return false;
        }
    }

    function getName($memberId) {
        $userRstl = parent::selectRow(self::TABLE, array("full_name", "email"), self::PRIMARY_ID . "=" . $memberId);
        $name = ucwords($userRstl->full_name);
        if (trim($name) == '') {
            $name = $userRstl->email;
        }
        return $name;
    }

    function getEmail($memberId) {
        $userRstl = parent::selectRow(self::TABLE, array("email"), self::PRIMARY_ID . "=" . $memberId);
        return $userRstl->email;
    }
    
    function getDetail($memberId, $arrayFields = array("*")) {
        $resultRow = parent::selectRow(self::TABLE, $arrayFields, self::PRIMARY_ID . "=$memberId");
        return $resultRow;
    }

    function getmemberIdFromEmailOrMobile($emailOrMobile){
		return parent::selectRow(self::TABLE,array("*"),"email='$emailOrMobile' or mobile_number='$emailOrMobile'");
    }

    function authenticate($email, $password) {
        $userRstl=array();
        $extraWhere=" member_type='" . PUBLIC_MEMBER_TYPE . "' and (email='" . $email . "' or mobile_number='" . $email . "') AND pass_word=('" . md5($password) . "') and is_active='1'";
        $userRstl = parent::selectRow(self::TABLE, array(self::PRIMARY_ID, "member_type", "full_name", "is_active","pwd_reset_request","member_id","show_mobile"), $extraWhere);
       
        return $userRstl;
    }

    function __destruct() {
        $table = self::TABLE;
        parent::destructAll($table);
    }

}