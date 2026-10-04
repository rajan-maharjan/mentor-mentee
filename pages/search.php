<div class="col-12 grid-margin">
    <div class="card">
    <div class="card-body">
        <h4 class="card-title">Search Member/Mentor</h4>
        <form class="form-sample" name="formSearch" id="formSearch" method="POST" action="">
        <p class="card-description">
            Search with parameters. Enter below information to find your match
        </p>
        <div class="row">
            <div class="col-md-6">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Toastmaster's Name</label>
                <div class="col-sm-9">
                <input type="text" class="form-control" name="full_name" id="full_name" placeholder="Full Name" value="<?php echo ($_POST['full_name'])??''?>" />
                </div>
            </div>
            </div>
            <div class="col-md-6">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Clubs</label>
                <div class="col-sm-9">
                    <select class="form-control form-control-lg" id="club_id" name="club_id">
                        <option value=''>Any Club</option>
                        <?php 
                        $clubList = $objectClub->selectAll();
                        foreach($clubList as $singleMember){
                          echo '<option value="'.$singleMember->club_id.'">'.$singleMember->club_name.'('.$singleMember->current_area.')</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            </div>
        </div>
        
        <div class="row">
            
            <div class="col-md-6">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Mobile Number</label>
                <div class="col-sm-9">
                <input type="number" minlength="10" maxlength="10" class="form-control" name="mobile_number" id="mobile_number" placeholder="Mobile Number without country code" />
                </div>
            </div>
            </div> 
            
            <div class="col-md-6">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Email Address</label>
                <div class="col-sm-9">
                <input type="text" minlength="5" maxlength="100" class="form-control" name="email" id="email" placeholder="Email address" />
                </div>
            </div>
            </div> 
             
        </div>
        
        <div class="row">
            <div class="col-md-4">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Looking for</label>
                <div class="col-sm-9">
                    <div class="form-check">
                        <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="mentor_for" id="mentorshipboth" value="" checked>
                        Both
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="mentor_for" id="mentorshipindividual" value="I">
                        Individual Mentor
                        </label>
                    </div>
                    <div class="form-check">
                        <label class="form-check-label">
                        <input type="radio" class="form-check-input" name="mentor_for" id="mentorshipboth" value="C">
                        Club Mentor
                        </label>
                    </div>
                </div>
            </div>
            </div>
            <div class="col-md-8">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Searching Skills For</label>
                    <?php $parentSkills = $objectSkill->selectAll(1,'parent_id=0');?>
                    <div class="col-sm-9">
                        <div style="float:left;width:<?php echo 100/count($parentSkills)?>%">
                    <?php 
                        foreach($parentSkills as $singleParentSkill){?>
                            <label class="form-check-label">
                                <strong><?php echo strtoupper($singleParentSkill->skill_name);?></strong>
                            </label>                             
                            <?php 
                            $childSkills = $objectSkill->selectAll(1,'parent_id='.$singleParentSkill->skill_id);
                            foreach($childSkills as $singleChildSkill){?>
                              <div class="form-check">
                                  <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="childSkills[]" id="childSkill<?php echo $singleChildSkill->skill_id?>" value="<?php echo $singleChildSkill->skill_id?>">
                                    <?php echo ($singleChildSkill->skill_name);?>  
                                  <i class="input-helper"></i></label>
                              </div>                   
                          <?php } ?>
                          </div>
                          <div style="float:left;width:<?php echo 100/count($parentSkills)?>%">
                       <?php                    
                        }
                        ?>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
        <button type="submit" class="btn btn-primary mb-2" name="btnSearch" id="btnSearch">Search Members</button>
        </form>
    </div>
    </div>
    <?php
    if(isset($_POST['btnSearch'])){
        $showResult = false;
        $whereCondition="member_id>0 /*and skill_ids is not null and mentor_for is not null*/";
        if(isset($_POST['full_name']) && trim($_POST['full_name'])!=''){
            $whereCondition.=" and upper(full_name) like '%".str_replace(' ','%',strtoupper(trim($_POST['full_name'])))."%'";
            $showResult = true;
        }
        if(isset($_POST['gender']) && trim($_POST['gender'])!=''){
            $whereCondition.=" and gender = '".trim($_POST['gender'])."'";
            $showResult = true;
        }
        if(isset($_POST['club_id']) && trim($_POST['club_id'])!=''){
            $whereCondition.=" and club_id = '".trim($_POST['club_id'])."'";
            $showResult = true;
        }
        if(isset($_POST['email']) && trim($_POST['email'])!=''){
            $whereCondition.=" and email like '%".trim($_POST['email'])."%' and show_email='Y'";
            $showResult = true;
        }
        if(isset($_POST['mobile_number']) && trim($_POST['mobile_number'])!=''){
            $whereCondition.=" and mobile_number = '".trim($_POST['mobile_number'])."' and show_mobile='Y'";
            $showResult = true;
        }
        if(isset($_POST['mentor_for']) && trim($_POST['mentor_for'])!=''){            
            $whereCondition.=" and mentor_for like '%".trim($_POST['mentor_for'])."%'";
            $showResult = true;
        }
        
        #echo $whereCondition;
        if($showResult)
            $searchResult = $objectUser->select($objectUser::TABLE, array("*"), $whereCondition,"full_name asc");   
        else
            $searchResult=array();
    
    ?>
    <div class="card-body">
        <h4 class="card-title">Available Mentors</h4>
        <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr> 
                <th>Name</th>
                <th>
                Club
                </th>                          
                <th>
                Email
                </th>
                <th>
                Mobile
                </th>                          
                <th>Option</th>                                                
            </tr>
            </thead>
            <tbody>
            <?php 
            $counter=1;
            foreach($searchResult as $singleMember){ 
                $interestedAs = '';                
                if(trim($singleMember->mentor_for)!=''){
                    $explodeMentorFor = explode(",",$singleMember->mentor_for);
                    
                    foreach($explodeMentorFor as $singleMentorVal){
                        $interestedAs .= ($singleMentorVal=="I"?"Individual":($singleMentorVal=="C"?"Club":"N/A")).", ";
                    }
                }         
            ?>
            <tr title="Interested as Mentor for : <?php echo $interestedAs?>">   
                <td class="py-1">
                <strong><?php echo $counter++;?>. 
                <?php
                if(! empty($singleMember)){
                $profilePic=isset($singleMember->profile_picture)?$singleMember->profile_picture:'';
                if(trim($profilePic)=='')
                    $profilePic='noimage'.$singleMember->gender.'.png';
                ?>
                <a href="<?php echo $singleMember->profile_link?>" target="_blank">
                <!--img src="images/members/<?php echo $profilePic?>" alt="image"/-->  
                 <?php echo $singleMember->full_name?></strong>
            </a>
            <?php  } else echo "n/a";?>
            </td>                        
                <td>
                <?php 
                $clubFound=$objectClub->getMembersClubDetail($singleMember->member_id);
                  if(!empty($clubFound))
                    echo $clubFound->club_name;
                  else echo "N/A";
                ?>                
                </td>
                <td>
                <?php if($singleMember->show_email=="Y"){?>
                      <a href="mailto:<?php echo $singleMember->email?>"><?php echo $singleMember->email?></a>
                      <?php } else { echo "Not displayed due to privacy";} ?>
                </td>
                <td>
                <?php echo ($singleMember->show_mobile=='Y'?($singleMember->mobile_number):'Not disclosed by member') ?>
                </td>
                <td>
                    <?php 
                    $checkRecord=$objectRequest->checkRequestExists($singleMember->member_id);
                    if (empty($checkRecord)){
                    ?>
                <button type="button" class="btn btn-primary mr-2 sendrequest" id="<?php echo $singleMember->member_id?>">Send Request</button>
            <?php } else {
                        echo "your request is ";
                        switch($checkRecord->status){
                            case 'P':echo 'Pending';break;
                            case 'A':echo 'Approved';break;
                            case 'C':echo 'Completed';break;
                            default:echo 'N/A';
                          }
                          if ($checkRecord->status=='A'){?>
                            <a href="<?php echo SITE_PATH?>appointment/<?php echo base64_encode($checkRecord->request_id)?>.html" class="btn btn-success btn-rounded btn-fw">Add Meeting Details</a>
                          <?php }
                        } ?>    
            </td>
            </tr>
            <?php } if($counter==1){ ?>
                        <tr>
                            <td colspan="4" style="color:red;text-align:center;font-weight:bold">No members found with the criteria you provided.</td>
                        </tr>
                        <?php } ?>
            
            </tbody>
        </table>
        </div>
    </div>
    <?php }?>
</div>
