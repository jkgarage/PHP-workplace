<?php
include('lib/Box_Rest_Client.v2.php');
session_start();
            
//$logger ->logDebug("photodownload::session status".session_status()."; session list ".var_export($_SESSION,1) );
			
if( array_key_exists('box_net_client',$_SESSION) &&  !empty($_SESSION['box_net_client'])) {
    $dl_box_net = $_SESSION['box_net_client'];
}
else { 
	$logger ->logDebug("photodownload::There is no session for Box_net_client." );
	exit();
	//todo, need to initialize the box_net_client again - for now, that session is init in main php file
}

if ( array_key_exists('file_id',$_POST) &&  !empty($_POST['file_id']) 
   && array_key_exists('local_name',$_POST) &&  !empty($_POST['local_name']) )
{
	set_time_limit(0); //to prevent session time out
	$logger->logDebug('photodownload.php=>reach to main if(), downloading file ');
			
	$file_id = $_POST['file_id'];
	$local_fname = $_POST['local_name'];
	$result = $dl_box_net->download($file_id, $local_fname); // << this line is to move to download_url
	//todo : add validation of download result
}

?>