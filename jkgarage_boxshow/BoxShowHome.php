<?php
//The sample code include a number of logs, you need to have KLogger to it to work
//Include the Box_Rest_Client class
include('lib/Box_Rest_Client.v2.php');
include('config.php');
session_start();

$CACHE_SIZE = 8;
$REPEAT_CYCLE = 2; //let the slideshow repeat 2 times
$logger->logDebug('BoxShowHome.php=>Executing..');

if ( array_key_exists('bss_callback', $_POST) && !empty($_POST['bss_callback']) && $_POST['bss_callback'] == 'true'
  && array_key_exists('bss_ticker',$_POST) && !empty($_POST['bss_ticker']) )
{
	$logger->logDebug('BoxShowHome.php=>callback:Entering..');
	$fileid_list = $_SESSION['fileid_list'];
	//todo : validate if Session is still valid
	
	$ticker = $_POST['bss_ticker'];
	$cursor = $ticker % count($fileid_list);
	
	//spawn a background process, download the file to local folder
	$local_fname = 'images/boxphoto'.($cursor % $CACHE_SIZE).'.jpg';
	
	$download_url = ROOT_URL.'/photodownload.php'; //[todo] construct the URL for download of file
	$params['file_id'] = $fileid_list[$cursor];
	$params['local_name'] = $local_fname;
	
	if(!array_key_exists('box_net_client',$_SESSION) || empty($_SESSION['box_net_client'])) 
	{
		$logger->logDebug('BoxShowHome.php=>go reset the session ');
		if(!array_key_exists('auth',$_SESSION) || empty($_SESSION['auth'])) {
			$_SESSION['auth'] = $box_net->authenticate();
		}
		else {
			$box_net->auth_token = $_SESSION['auth'];
		}
		$_SESSION['box_net_client'] = $box_net;
	}
	
	curl_post_async($download_url, $params); //<< this url does the downloading
	$logger->logDebug('BoxShowHome.php=>callback:after curl_post_async('.$download_url.', '.var_export($params,1).');');
	$logger->logDebug('BoxShowHome.php=>callback:cursor='.$cursor.', count($fileid_list)='.count($fileid_list) );
}//if callback
else
{
	// Set your API Key. 
	// state your API key in 'config.php' for 
	$box_net = new Box_Rest_Client(BOX_API_KEY);

	if(!array_key_exists('auth',$_SESSION) || empty($_SESSION['auth'])) {
		$_SESSION['auth'] = $box_net->authenticate();
	}
	else {
		// If the auth $_SESSION key exists, then we can say that 
		// the user is logged in. So we set the auth_token in 
		// box_net.
		$box_net->auth_token = $_SESSION['auth'];
	}
	$_SESSION['box_net_client'] = $box_net;

	/** Photos for slideshow shall be placed in "root/CloudPhotoShow/", 
	where the name 'CloudPhotoShow' is configurable in 'config.php' **/
	$folder = $box_net->folder(0);

	//look for folder "CloudPhotoShow" (or any name configured in 'config.php')
	$photo_folder_id = -1;
	foreach ($folder->folder as $key=>$val)
		if ($val->attr('name') == BOX_PHOTO_FOLDER)
		{
			$photo_folder_id = $val->attr('id');
			break;
		}
		
	if ( $photo_folder_id == -1 )
	{
		echo '<html><body><h1>Cannot find folder ~/'.BOX_PHOTO_FOLDER.'/ in your Box account.<br>';
		echo 'Exiting..</h1></body></html>';
	}
	else
	{
		/********************************************************************************** 
		*   Assumptions: 
		*   1. while the slideshow is running, this photo folder does NOT change
		*      If the photo folder content changes, program may crash
		*   2. All photos are in .jpg format
		*   3. In your local folder, need to create a sub-folder /images/
		*   4. Preferably, all photos are of size 600x800
		***********************************************************************************/	
		//enter the photo folder
		$folder = $box_net->folder($photo_folder_id);
		$photos = $folder->file;
		$fileid_list = array();
		for ($i = 0; $i < count($photos); $i++)
			$fileid_list[$i] = $photos[$i]->attr('id');
		$_SESSION['fileid_list'] = $fileid_list;

		$counter = 0;
		$cursor = 0;

			$local_fname = 'images/boxphoto'.($cursor % $CACHE_SIZE).'.jpg';
			
			$download_url = ROOT_URL.'/photodownload.php'; //[todo] construct the URL for download of file
			$params['file_id'] = $fileid_list[$cursor];
			$params['local_name'] = $local_fname;
			
			if(!array_key_exists('box_net_client',$_SESSION) || empty($_SESSION['box_net_client'])) 
			{
				$logger->logDebug('BoxShowHome.php=>go reset the session ');
				if(!array_key_exists('auth',$_SESSION) || empty($_SESSION['auth'])) {
					$_SESSION['auth'] = $box_net->authenticate();
				}
				else {
					$box_net->auth_token = $_SESSION['auth'];
				}
				$_SESSION['box_net_client'] = $box_net;
			}
			
			curl_post_async($download_url, $params); //<< this url does the downloading
			$logger->logDebug('BoxShowHome.php=>after curl_post_async('.$download_url.', '.var_export($params,1).');');
			$logger->logDebug('BoxShowHome.php=>cursor='.$cursor.', counter='.$counter.', count($photos)='.count($photos) );
			
	}//if can find the photo folder

	include ('simpleSlideshow_index.html');
		
}//if not callback


/** Function to fire background GET request **/
function curl_post_async($url, $params)
{
    foreach ($params as $key => &$val) {
      if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key.'='.urlencode($val);
    }
    $post_string = implode('&', $post_params);

    $parts=parse_url($url);

    $fp = fsockopen($parts['host'],
        isset($parts['port'])?$parts['port']:80,
        $errno, $errstr, 30);

    $out = "POST ".$parts['path']." HTTP/1.1\r\n";
    $out.= "Host: ".$parts['host']."\r\n";
    $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
	$out.= "Cookie: PHPSESSID=" . $_COOKIE['PHPSESSID'] . "\r\n";
    $out.= "Content-Length: ".strlen($post_string)."\r\n";
    $out.= "Connection: Close\r\n\r\n";
    if (isset($post_string)) $out.= $post_string;

    fwrite($fp, $out);
    fclose($fp);
}
?>