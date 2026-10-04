<?php
class clubs extends common {
    const TABLE = "clubs";
    const PRIMARY_ID = "club_id";
    const USER_TABLE = "members";

    function __construct() {
    }

    function selectAll($divD="") {
        $this->sql= "SELECT distinct c.club_id, this_fy_area as current_area, club_name, club_whatspp_phone, meeting_frequency, concat(meeting_day,'DAY') meet_day,
        date_format(meeting_starts_time,'%H:%i') meeting_starts_time, meeting_venue
        FROM tin_fiscal_years fy inner join tin_fy_club_detail assoc on assoc.fy_id=fy.fy_id
        inner join tin_clubs c on c.club_id=assoc.club_id
        WHERE fy_start_date<=CURDATE() and CURDATE()<=fy_end_date
        and c.is_active='1' and assoc.is_active='1'";

        if($divD!='')
            $this->sql .= " and this_fy_area like '".$this->escape($divD)."%'";

        $this->sql.= "order by this_fy_area, club_name";

        $exec=$this->execute();
		$returnArray=array();
		while($row=mysqli_fetch_object($exec)){
			array_push($returnArray,$row);
			}
		return $returnArray;
    }
    function getDetail($clubId) {
        $clubId = $this->safeInt($clubId);
        return $this->selectRow(self::TABLE, array("*"), self::PRIMARY_ID . "='$clubId'");
    }
    function getName($clubId) {
        $clubId = $this->safeInt($clubId);
        $userRstl = parent::selectRow(self::TABLE, array("club_name"), self::PRIMARY_ID . "=" . $clubId);
        $name = ucwords($userRstl->club_name);
        return $name;
    }

    function getCurrentArea($clubId) {
        $clubId = $this->safeInt($clubId);
        $queryResult = parent::selectJoin('fy_club_detail', 'fiscal_years', array("this_fy_area"), array(), "inner", "t1.fy_id=t2.fy_id",
        "t1.is_active='1' and fy_start_date<=CURDATE() and CURDATE()<=fy_end_date and t1.club_id='".$clubId."'");
        return $queryResult[0]->this_fy_area;
    }

    function getDivisionList() {
        return parent::select('areas', array('distinct division'), "is_active='1'");
    }

    function getMembersClubDetail($memberID){
        $memberID = $this->escape($memberID);
        $this->sql= "SELECT c.* FROM  tin_members_club_role ma
        inner join tin_clubs c on c.club_id=ma.club_id
        WHERE ma.member_id='$memberID'
        and ma.is_active='1'
        and c.is_active='1'
        order by ma.updated_on desc
        limit 1;";

        $exec=$this->execute();
		$returnArray=array();
		while($row=mysqli_fetch_object($exec)){
			array_push($returnArray,$row);
			}
        if(empty($returnArray))
            return array();
		else return $returnArray[0];
    }

    function getPresident($clubId){
        $clubId = $this->safeInt($clubId);
        $this->sql= "SELECT m.* FROM `tin_fiscal_years` fy inner join tin_members_club_role ma on ma.fy_id=fy.fy_id
inner join tin_members m on m.member_id=ma.member_id
WHERE fy_start_date<=CURDATE() and CURDATE()<=fy_end_date
and role_code='PREZ'
and m.is_active='1'
and ma.club_id=".$clubId;

        $exec=$this->execute();
		$returnArray=array();
		while($row=mysqli_fetch_object($exec)){
			array_push($returnArray,$row);
			}
		if(empty($returnArray))
            return array();
		else return $returnArray[0];
    }
}