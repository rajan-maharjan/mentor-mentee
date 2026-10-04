$(document).ready(function(){
    
    $("#meeting_date").datepicker({ dateFormat: 'yy-mm-dd' });
    
    $('.sendrequest').click(
        function(){
            var _id = $(this).attr('id');
            var _this = $(this);
            $(_this).html('Sending...');	
            jQuery.ajax({
                type:"POST",
                url:_sitePath+"ajax.php",
                data:{choice:'sendrequest',receiver:_id, 'csrf_token': _csrfToken},									
                success: function(msg){
                    if(msg==1)
                        $(_this).parent().html('Request Sent.');	
                    else
                        $(_this).removeClass('success-message').addClass('error-message').html(msg);	
                    }				
            });
        });
    
    $('.cancel-request').click(
        function(){
            if(confirm('Are you sure to CANCEL/REJECT the request?')){
                var _id = $(this).attr('id');
                var _this = $(this);
                $(_this).html('Cancelling...');
                jQuery.ajax({
                    type:"POST",
                    url:_sitePath+"ajax.php",
                    data:{choice:'cancelrequest',request_id:_id,'csrf_token': _csrfToken},									
                    success: function(msg){
                        if(msg==1){
                            $(_this).parent().parent().slideUp();
                            }                    	
                        }				
                });
            }
        });

    $('.approve-request').click(
        function(){
            if(confirm('Are you about to APPROVE the request?')){
                var _id = $(this).attr('id');
                var _this = $(this);
                $(_this).html('Approving...');	
                jQuery.ajax({
                    type:"POST",
                    url:_sitePath+"ajax.php",
                    data:{choice:'approverequest',request_id:_id,'csrf_token': _csrfToken},									
                    success: function(msg){
                        $(_this).parent().html($msg);                   	
                        }				
                });
            }
        });

    $('.delete-appointment').click(
        function(){
            if(confirm('Are you sure to delete?')){
                var _id = $(this).attr('id');
                var _this = $(this);
                $(_this).html('Deleting...');
                jQuery.ajax({
                    type:"POST",
                    url:_sitePath+"ajax.php",
                    data:{choice:'deleteappointment',request_id:_id,'csrf_token': _csrfToken},									
                    success: function(msg){
                        if(msg==1){
                            $(_this).parent().parent().slideUp();
                            }                    	
                        }				
                });
            }
        });
});