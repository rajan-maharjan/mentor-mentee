<?php
// Note: the h() escaping helper is defined once, globally, in files.inc.php.
$userID  = $_SESSION['session_user_id'];
$userDetail = $objectUser->getDetail($userID);
$roleList   = MemberClubrole::getroles();
$clubList   = $objectClub->selectAll();

$messageText = "";
$hasError    = false;
$formRows    = array(); // rows shown in the club/role_code section

/**
 * Renders one "club + role_code" row. $i is the row index, $removable false for row 0.
 */
function renderClubRow($i, $clubList, $roleList, $clubId, $role_code, $isPrimary, $removable)
{
    ob_start(); ?>
    <div class="row club-row align-items-center mb-2">
        <div class="col-md-2">
            <div class="form-check">
                <label class="form-check-label">
                    <input type="radio" class="form-check-input primary-radio" name="primary_index"
                           value="<?php echo h($i) ?>" <?php echo $isPrimary ? 'checked="checked"' : '' ?>>
                    Primary
                </label>
            </div>
        </div>
        <div class="col-md-5">
            <select class="form-control club-select" name="club_id[]">
                <option value="0">Club Name</option>
                <?php foreach ($clubList as $c) { ?>
                    <option value="<?php echo h($c->club_id) ?>"<?php echo ($c->club_id == $clubId) ? ' selected="selected"' : '' ?>>
                        <?php echo h($c->club_name . ' (' . $c->current_area . ')') ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-control" name="club_role_code[]">
                <?php foreach ($roleList as $code => $label) { ?>
                    <option value="<?php echo h($code) ?>"<?php echo ($code == $role_code) ? ' selected="selected"' : '' ?>>
                        <?php echo h($label) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2">
            <?php if ($removable) { ?>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-club">Remove</button>
            <?php } ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

if (isset($_POST['btnSubmit'])) {

    $fullName = trim($_POST['full_name'] ?? '');
    $mobile   = trim($_POST['mobile_number'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $pnNumber = trim($_POST['PN_Number'] ?? '');
    $intro    = trim($_POST['intro'] ?? '');

    if ($fullName === '') {
        $hasError = true;
        $messageText .= "<br />Missing your full name.";
    }
    if ($mobile === '' || !$objectFunctions->isValidMobile($mobile)) {
        $hasError = true;
        $messageText .= "<br />Missing or invalid mobile number";
    }
    if ($email === '' || !$objectFunctions->isValidEmail($email)) {
        $hasError = true;
        $messageText .= "<br />Missing or invalid email address";
    }
    if (!$objectFunctions->validateCsrfToken($_POST['csrf_token'] ?? '')){
        $hasError = true;
        $messageText .= "<br />Your session has expired. Please reload the page and try again.";
    }
    if ($pnNumber === '') {
        $hasError = true;
        $messageText .= "<br />Missing PN Number of Toastmaster International Portal";
    }
    elseif (!preg_match('/^[0-9]{1,10}$/', $pnNumber)) {
        $hasError = true;
        $messageText .= "<br />PN Number must be numeric.";
    }
    // Fix (IDOR): this field is used as the join key for this member's club/role
    // records, so it must never be allowed to collide with another account's.
    elseif ($objectUser->pnNumberBelongsToAnotherMember($pnNumber, $userID)) {
        $hasError = true;
        $messageText .= "<br />That PN Number is already associated with another account.";
    }

    if ($intro === '') {
        $hasError = true;
        $messageText .= "<br />Missing your short intro.";
    }

    // ---- Clubs & role_codes -------------------------------------------------
    $postClubs    = isset($_POST['club_id']) && is_array($_POST['club_id']) ? $_POST['club_id'] : array();
    $postroles    = isset($_POST['club_role_code']) && is_array($_POST['club_role_code']) ? $_POST['club_role_code'] : array();
    $postclubroleids  = isset($_POST['club_role_id']) && is_array($_POST['club_role_id']) ? $_POST['club_role_id'] : array();
    $primaryIndex = isset($_POST['primary_index']) ? (int) $_POST['primary_index'] : -1;

    $clubRows = array();
    $seen     = array();

    foreach ($postClubs as $i => $cid) {
        $role_code = isset($postroles[$i]) ? $postroles[$i] : 'MEM';
        $club_role_id = isset($postclubroleids[$i]) ? $postclubroleids[$i] : '0';
        if (!isset($roleList[$role_code])) {
            $role_code = 'MEM';
        }
        $isPrimary = ((int) $i === $primaryIndex);

        // keep what the user typed so the form can be redisplayed on error
        $formRows[] = (object) array('id'=>$club_role_id, 'club_id' => $cid, 'role_code' => $role_code, 'is_primary' => $isPrimary ? 1 : 0);

        if ($cid === 0) {
            if ($isPrimary) {
                $hasError = true;
                $messageText .= "<br />Missing your primary club";
            }
            continue; // ignore other empty rows
        }

        if (isset($seen[$cid])) {
            $hasError = true;
            $messageText .= "<br />Same club selected more than once";
            continue;
        }
        $seen[$cid] = true;
        $clubRows[] = array('id'=>$club_role_id, 'club_id' => $cid, 'role_code' => $role_code, 'is_primary' => $isPrimary ? 1 : 0);
    }

    $primaryRow = null;
    foreach ($clubRows as $r) {
        if ($r['is_primary']) {
            $primaryRow = $r;
        }
    }
    if (!$clubRows || $primaryRow === null) {
        if (strpos($messageText, 'primary club') === false) {
            $hasError = true;
            $messageText .= "<br />Choose your primary club";
        }
    }

    // ---- Save ----------------------------------------------------------
    if (!$hasError) {
        $arrayData = array();
        $arrayData['full_name']       = $fullName;
        $arrayData['email']           = $email;
        $arrayData['mobile_number']   = $mobile;
        $arrayData['show_mobile']     = (isset($_POST['show_mobile']) && $_POST['show_mobile'] === 'Y') ? 'Y' : 'N';
        $arrayData['show_email']      = (isset($_POST['show_email']) && $_POST['show_email'] === 'Y') ? 'Y' : 'N';
        // kept in tin_members for backward compatibility with the rest of the site
        $arrayData['current_do_position'] = $primaryRow['role_code'];
        $arrayData['member_id']       = $pnNumber;
        $arrayData['intro']           = $intro;
        $arrayData['gender']          = trim($_POST['gender'] ?? 'M');
        $arrayData['profile_link']    = trim($_POST['profile_link'] ?? '');
        $arrayData['skill_ids']       = join(",", array_map('intval', $_POST['skill_ids'] ?? array()));
        $arrayData['mentor_for']      = join(",", array_intersect($_POST['mentor_for'] ?? array(), array('I', 'C')));
        $arrayData['is_DTM']          = (($_POST['is_DTM'] ?? 'N') === 'Y') ? 'Y' : 'N';
        $arrayData['member_type']     = '2';
        $arrayData['updated_by']      = $userID;
        $arrayData['updated_on']      = date("Y-m-d H:i:s");

        try {
            $objectUser->insertUpdate($arrayData, $userID);
            $objectMemberClub->saveAll($pnNumber, $clubRows);
            $messageText = "<script language='javascript'>alert('Successfully updated your information.');window.location='profile.html'</script>";
        } catch (Exception $e) {
            error_log('Profile update failed: ' . $e->getMessage());
            $hasError    = true;
            $messageText = "<br />Could not save your information. Please try again.";
        }
    }
}

// ---- Rows to display (POST on error > saved rows > legacy single club > blank) ----
if (!$formRows) {
    $saved = $objectMemberClub->getByMember($userID);
    foreach ($saved as $s) {
        $formRows[] = (object) array('id'=> $s->id, 'club_id' => $s->club_id, 'role_code' => $s->role_code, 'is_primary' => $s->is_primary);
    }
}
/*
if (!$formRows) {
    $formRows[] = (object) array(
        'club_id'    => $userDetail->club_id,
        'role_code'  => (!empty($userDetail->current_position) && isset($roleList[$userDetail->current_position])) ? $userDetail->current_position : 'MEM',
        'is_primary' => 1,
    );
}
*/
// make sure exactly one row is checked as primary (default: first row)
$hasPrimary = false;
foreach ($formRows as $r) {
    if ($r->is_primary) {
        $hasPrimary = true;
    }
}
if (!$hasPrimary) {
    $formRows[0]->is_primary = 1;
}

$skillsArray = explode(",", (string) $userDetail->skill_ids);
$mentorArray = explode(",", (string) $userDetail->mentor_for);
if(isset($_POST['skill_ids']))
    $skillsArray = $_POST['skill_ids'];

if(isset($_POST['mentor_for']))
    $mentorArray = $_POST['mentor_for'];
?>

<div class="col-12 grid-margin">
    <div class="card">
    <div class="card-body">
        <h4 class="card-title">Edit Personal Information</h4>
        <form class="form-sample" method="POST" action="" name="profile-update-form">
        <input type="hidden" name="csrf_token" value="<?php echo $objectFunctions->getCsrfToken()?>" />
        <p class="card-description">
            Personal info
        </p>
        <?php if ($messageText != '') { ?>
            <div id="message_box" style="margin-bottom:10px" class="btn btn-<?php echo ($hasError ? "danger" : "success") ?>"><?php echo $messageText ?></div>
        <?php } ?>

        <div class="row">
            <div class="col-md-4">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Name <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                <input type="text" class="form-control" value="<?php echo h(isset($_POST['full_name']) ? $_POST['full_name'] : $userDetail->full_name) ?>" name="full_name" id="full_name" required />
                </div>
            </div>
            </div>
            <div class="col-md-4">
            <div class="form-group row">
                <label class="col-sm-6 col-form-label">Mobile Number <span class="text-danger">*</span></label>
                <div class="col-sm-6">
                <input type="number" minlength="10" maxlength="10" class="form-control" name="mobile_number" required value="<?php echo h(isset($_POST['mobile_number']) ? $_POST['mobile_number'] : $userDetail->mobile_number) ?>"/>
                </div>
            </div>
            </div>
            <div class="col-md-4">
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="show_mobile" id="show_mobile" value="Y" <?php echo ($userDetail->show_mobile == 'Y') ? 'checked="checked"' : "" ?>>
                    Show Mobile Number?
                    </label>
                </div>
            </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Email<span class="text-danger">*</span></label>
                <div class="col-sm-8">
                <input type="text" minlength="5" maxlength="100" class="form-control" name="email" required value="<?php echo h(isset($_POST['email']) ? $_POST['email'] : $userDetail->email) ?>"/>
                </div>
            </div>
            </div>
            <div class="col-md-4">
            <div class="form-group row">
                <div class="form-check">
                    <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="show_email" id="show_email" value="Y" <?php echo ($userDetail->show_email == 'Y') ? 'checked="checked"' : "" ?>>
                    Show Email Address?
                    </label>
                </div>
            </div>
            </div>
        </div>

        <!-- ================= Clubs & role_codes ================= -->
        <div class="row">
            <div class="col-md-12">
                <label class="col-form-label">Your Clubs &amp; role_code in each club <span class="text-danger">*</span></label>
                <div class="row mb-1">
                    <div class="col-md-3"><small class="text-muted">Primary club</small></div>
                    <div class="col-md-4"><small class="text-muted">Club</small></div>
                    <div class="col-md-3"><small class="text-muted">Role</small></div>
                </div>
                <div id="club-rows">
                    <?php
                    foreach ($formRows as $i => $r) {
                        echo renderClubRow($i, $clubList, $roleList, $r->club_id, $r->role_code, $r->is_primary, $i > 0);
                        echo "<input type='hidden' name='club_role_id[]' value = '".$r->id."'/>";
                    }
                    ?>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary mb-3" id="btn-add-club">+ Add another club</button>
            </div>
        </div>
        <!-- blank row used by JS (index 0 is replaced when cloning) -->
        <template id="club-row-template">
            <?php echo renderClubRow(0, $clubList, $roleList, 0, 'MEM', 0, true); ?>
            <input type='hidden' name='club_role_id[]' value = '0'/>
        </template>

        <div class="row">
            <div class="col-md-8">
            <div class="form-group row">
                <label class="col-sm-8 col-form-label">Toastmaster International PN Number (8-digit max)(Without character PN-) <span class="text-danger">*</span></label>
                <div class="col-sm-4">
                <input type="number" minlength="8" maxlength="8" class="form-control" name="PN_Number" placeholder='12345678' required value="<?php echo h(isset($_POST['PN_Number']) ? $_POST['PN_Number'] : $userDetail->member_id) ?>" />
                </div>
            </div>
            </div>
            <div class="col-md-4">
            <div class="form-group row">
                <label class="col-sm-6 col-form-label">Gender <span class="text-danger">*</span></label>
                <div class="col-sm-6">
                <select class="form-control" name="gender" id="gender">
                    <option value="M" <?php echo ($_POST['gender']??$userDetail->gender == 'M') ? ' selected="selected"' : '' ?>>Male</option>
                    <option value="F" <?php echo ($_POST['gender']??$userDetail->gender == 'F') ? ' selected="selected"' : '' ?>>Female</option>
                </select>
                </div>
            </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Profile Link</label>
                <div class="col-sm-10">
                <input type="text" class="form-control" name="profile_link" id="profile_link" value="<?php echo h(isset($_POST['profile_link']) ? $_POST['profile_link'] : $userDetail->profile_link) ?>" />
                </div>
            </div>
            </div>
            <div class="col-md-4">
            <div class="form-group row">
                <label class="col-sm-6 col-form-label">Are you DTM?</label>
                <div class="col-sm-6">
                <select class="form-control" name="is_DTM" id="is_DTM">
                    <option value='Y' <?php echo (($_POST['is_DTM']??$userDetail->is_dtm) == 'Y') ? ' selected="selected"' : '' ?>>Yes</option>
                    <option value='N' <?php echo (($_POST['is_DTM']??$userDetail->is_dtm) == 'N') ? ' selected="selected"' : '' ?>>No</option>
                </select>
                </div>
            </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Introduction <span class="text-danger">*</span></label>
                <div class="col-sm-10">
                <textarea class="form-control" name="intro" id="intro" rows="5" required><?php echo h(isset($_POST['intro']) ? $_POST['intro'] : $userDetail->intro) ?></textarea>
                </div>
            </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Your Skills</label>
                <div class="form-group row">
                    <?php
                    $parentSkills = $objectSkill->selectAll(1, 'parent_id=0');
                    foreach ($parentSkills as $singleParentSkill) { ?>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="parentSkills[]" id="parentSkill<?php echo h($singleParentSkill->skill_id) ?>" value="<?php echo h($singleParentSkill->skill_id) ?>">
                                <strong><?php echo h(strtoupper($singleParentSkill->skill_name)) ?></strong>
                            </label>
                            <?php
                            $childSkills = $objectSkill->selectAll(1, 'parent_id=' . (int) $singleParentSkill->skill_id);
                            foreach ($childSkills as $singleChildSkill) { ?>
                              <div class="form-check form-check-primary">
                                  <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="skill_ids[]" id="childSkill<?php echo h($singleChildSkill->skill_id) ?>"
                                    <?php echo (in_array($singleChildSkill->skill_id, $skillsArray)) ? "checked" : "" ?>
                                    value="<?php echo h($singleChildSkill->skill_id) ?>">
                                    <?php echo h($singleChildSkill->skill_name) ?>
                                  <i class="input-helper"></i></label>
                              </div>
                          <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
            <div class="form-group row">
            <label class="col-sm-3 col-form-label"></label>
                <div class="form-check">
                    <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="mentor_for[]" value="I" <?php echo in_array("I", $mentorArray) ? "checked" : "" ?>>
                    I Can Be Individual Mentor
                    </label>
                </div>
            </div>
            </div>
            <div class="col-md-6">
            <div class="form-group row">
            <label class="col-sm-3 col-form-label"></label>
                <div class="form-check">
                    <label class="form-check-label">
                    <input type="checkbox" class="form-check-input" name="mentor_for[]" value="C" <?php echo in_array("C", $mentorArray) ? "checked" : "" ?>>
                    I Can Be Club Mentor
                    </label>
                </div>
            </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mb-2" name="btnSubmit" id="btnSubmit" value="1">Submit</button>
        </form>
    </div>
    </div>
</div>

<script>
(function () {
    var wrap = document.getElementById('club-rows');
    var tpl  = document.getElementById('club-row-template');

    // Radio values must equal the row position, because club_id[] / club_role_code[] are position based.
    function renumber() {
        var rows = wrap.querySelectorAll('.club-row');
        var anyChecked = false;
        rows.forEach(function (row, i) {
            var radio = row.querySelector('.primary-radio');
            radio.value = i;
            if (radio.checked) anyChecked = true;
        });
        if (!anyChecked && rows.length) rows[0].querySelector('.primary-radio').checked = true;
    }

    document.getElementById('btn-add-club').addEventListener('click', function () {
        wrap.appendChild(document.importNode(tpl.content, true));
        renumber();
    });

    wrap.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-remove-club')) {
            e.target.closest('.club-row').remove();
            renumber();
        }
    });

    document.querySelector('form[name="profile-update-form"]').addEventListener('submit', function (e) {
        renumber();
        var picked = {}, ok = true;
        wrap.querySelectorAll('.club-select').forEach(function (s) {
            if (s.value !== '0') {
                if (picked[s.value]) ok = false;
                picked[s.value] = true;
            }
        });
        if (!ok) { alert('Same club selected more than once.'); e.preventDefault(); }
    });

    renumber();
})();
</script>
