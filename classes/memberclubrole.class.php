<?php

class MemberClubrole extends common {

    const TABLE      = "members_club_role"; // becomes TBL_PREFIX.members_club_role_code, e.g. tin_members_club_role
    const PRIMARY_ID = "id";

    /*
     * Suggested table (adjust to taste, keep the column names below in sync
     * with this class):
     *
     *   CREATE TABLE tin_members_club_role (
     *       id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
     *       member_id   INT UNSIGNED NOT NULL,
     *       club_id     INT UNSIGNED NOT NULL,
     *       role_code        VARCHAR(10)  NOT NULL DEFAULT 'MEM',
     *       is_primary  TINYINT(1)   NOT NULL DEFAULT 0,
     *       updated_by  INT UNSIGNED NULL,
     *       updated_on  DATETIME     NULL,
     *       UNIQUE KEY uq_member_club (member_id, club_id)
     *   );
     */

    function __construct() {

    }

    /** Allowed club roles: code => label. */
    static function getroles() {
        return array(
            'MEM'  => 'Member',
            'PREZ' => 'Club President',
            'VPE'  => 'Vice President of Education',
            'VPM'  => 'Vice President of Membership',
            'VPPR' => 'Vice President of Public Relations',
            'SECR' => 'Club Secretary',
            'TRES' => 'Club Treasurer',
            'SAA'  => 'Sergeant at Arms',
            'IPP'  => 'Immediate Past President',
        );
    }

    /** All clubs/roles of a member, primary club first. */
    function getByMember($userID) {
        $whereCond = "id='" . $this->escape(trim($userID)) . "'";
        return parent::select(self::TABLE, array("*"), $whereCond, "is_primary desc, " . self::PRIMARY_ID . " asc");
    }

    /** Insert or update a single member/club row. */
    function insertUpdate($memberId, $clubId, $role_code, $isPrimary, $dbPrimaryId = 0) {
        $memberId = trim($memberId);
        $clubId   = trim($clubId);
        // Fix (SQL injection): dbPrimaryId ultimately comes from client-submitted
        // form data (club_role_id[]) and was previously concatenated raw into
        // a SQL WHERE clause. It must always be a row's own integer id.
        $dbPrimaryId = $this->safeInt($dbPrimaryId);

        $arrayFieldValues = array();
        $arrayFieldValues['member_id']  = $memberId;
        $arrayFieldValues['club_id']    = $clubId;
        $arrayFieldValues['role_code']  = $role_code;
        $arrayFieldValues['is_primary'] = $isPrimary ? 'Y' : 'N';
        $arrayFieldValues['updated_by'] = $dbPrimaryId;
        $arrayFieldValues['updated_on'] = date("Y-m-d H:i:s");

        if ($dbPrimaryId > 0) {
            $primaryField = self::PRIMARY_ID;
            parent::update(self::TABLE, $arrayFieldValues, self::PRIMARY_ID."='$dbPrimaryId'");
            return $dbPrimaryId;
        } else {
            return parent::insert(self::TABLE, $arrayFieldValues);
        }
    }

    /**
     * Sync the complete list of clubs for a member: upserts every row given,
     * clears is_primary on the rest, and removes clubs no longer submitted.
     *
     * @param array $rows each: array('club_id'=>int, 'role_code'=>string, 'is_primary'=>0|1)
     */
    function saveAll($memberId, $rows) {
        $memberId = $this->escape(trim($memberId));

        // Reset primary flag first so only one row ends up primary.
        $this->sql = "UPDATE " . TBL_PREFIX . self::TABLE . " SET is_primary='N' WHERE member_id='$memberId'";
        $this->execute();

        $keepIds = array();
        foreach ($rows as $row) {
            if($row['club_id']>0)
                // insertUpdate() now always returns a safe int (see above),
                // so this join is no longer an injection point.
                $keepIds[] = $this->insertUpdate($memberId, $row['club_id'], $row['role_code'], $row['is_primary'], $row['id']);
        }

        // Remove clubs the member took off the form.
        if (count($keepIds) > 0) {
            $keepIdsText = join(",", array_map('intval', $keepIds));
            $this->sql = "DELETE FROM " . TBL_PREFIX . self::TABLE . " WHERE member_id='$memberId' and id NOT IN ($keepIdsText)";
        } else {
            $this->sql = "DELETE FROM " . TBL_PREFIX . self::TABLE . " WHERE member_id='$memberId'";
        }
        #echo $this->sql;
        $this->execute();
    }

    function __destruct() {
        $table = self::TABLE;
        parent::destructAll($table);
    }

}