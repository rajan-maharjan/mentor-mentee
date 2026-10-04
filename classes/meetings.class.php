<?php

class meetings extends common {

    const TABLE      = "tiab_meetings"; // becomes TBL_PREFIX.members_club_role_code, e.g. tin_members_club_role
    const PRIMARY_ID = "id";

   

    function __construct() {

    }

    function selectAll() {
        $this->sql = "select t1.id, t1.meeting_number, t1.theme, t1.meeting_date, t2.club_id, t2.club_name from " . self::TABLE . " t1 inner join
        tiab_meeting_clubs t2 on t1.id=t2.meeting_id where t2.club_id in (select club_id from tin_members_club_role where member_id = ".($_SESSION['session_member_id'])." and fy_id=5 and is_active='1') order by meeting_date desc";
        $exec = $this->execute();

        $returnArray=array();
		while($row=mysqli_fetch_object($exec)){
			array_push($returnArray,$row);			
			}		
		return $returnArray;
    }

    
    function __destruct() {
        //$table = self::TABLE;
        //parent::destructAll($table);
    }

}