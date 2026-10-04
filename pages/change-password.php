<?php
$messageText="";
if(isset($_POST['btnChangePassword'])){
    $old_password=trim($_POST['old_password']);
    $new_password=trim($_POST['new_password']);
    $retype_password=trim($_POST['retype_password']);

    if(!$objectFunctions->validateCsrfToken($_POST['csrf_token'] ?? '')){
        $className="text-danger";
        $messageText="Opps! Your session has expired. Please reload the page and try again.";
    }
    elseif(strlen($new_password) < 8){
        $className="text-danger";
        $messageText="Opps! Your new password must be at least 8 characters long.";
    }
    elseif($new_password!=$retype_password){
        $className="text-danger";
        $messageText="Opps! Looks like your new password and confirmed password are not same.";
    }
    else{
        $userId=$objectUser->authChangePwd($old_password,$_SESSION['session_user_id']);
        if(intval($userId)>0 && $userId==trim($_SESSION['session_user_id'])){
        	$objectUser->changePassword(array("pass_word"=>password_hash($new_password, PASSWORD_DEFAULT),"pwd_reset_request"=>"N"),$_SESSION['session_user_id']);
        	$className="btn-success";
        	$messageText="Password successfully changed. You will be loged out in few seconds...";
        	$_POST=array();
        	unset($_POST);
        }
        else{
        	$className="text-danger";
            $messageText="Opps!, Looks like you entered wrong old password. Please try by entering correct one.";
        }
    }
}
?>

<div class="col-12 grid-margin">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Change Password</h4>
            <form method="POST" action="" name="form-reset-pwd" id="form-reset-pwd">
                <input type="hidden" name="csrf_token" value="<?php echo $objectFunctions->getCsrfToken()?>" />
                <p class="<?php echo ($className!='')?$className: 'error-message'?>" style="display:<?php echo (trim($messageText)!="")?"block":'none'?>;"> <?php echo $messageText;?></p>
                <?php if($className=="btn-success"){?>
                <script>setInterval("window.location='logout.php'",3000);</script>
                <?php

                };?>
                <div class="row">
                    <div class="col-md-6">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Current Password</label>
                        <div class="col-sm-9">
                        <input type="password" class="form-control" id="old_password" name="old_password" />
                        </div>
                    </div>
                    </div>
                    <div class="col-md-6">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">New Password</label>
                        <div class="col-sm-9">
                        <input type="password" class="form-control" id="new_password" name="new_password" />
                        </div>
                    </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Confirm Password</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="retype_password" name="retype_password" />
                        </div>
                    </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mb-2" name="btnChangePassword" id="btnChangePassword" value="1">Submit</button>
            </form>
        </div>
    </div>
</div>
