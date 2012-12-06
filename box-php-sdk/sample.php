<?php
//The sample code include a number of logs, you need to have KLogger to it to work
// Include the Box_Rest_Client class
include('lib/Box_Rest_Client.v2.php');
session_start();
                  
// Set your API Key. If you have a lot of pages reliant on the 
// api key, then you should just set it statically in the 
// Box_Rest_Client class.
$api_key = 'xxxxxxxxxxxxxxx';
$box_net = new Box_Rest_Client($api_key);

if(!array_key_exists('auth',$_SESSION) || empty($_SESSION['auth'])) {
    $_SESSION['auth'] = $box_net->authenticate();
}
else {
    // If the auth $_SESSION key exists, then we can say that 
    // the user is logged in. So we set the auth_token in 
    // box_net.
    $box_net->auth_token = $_SESSION['auth'];
}

$folder = $box_net->folder(0);
//$logger ->logDebug("sample::folder=".var_export($folder,1) );

/*************** SAMPLE FOLDER OPERATIONS *************/
/**** Sample to create new folder ****/
//$folder_new = new Box_Client_Folder();
//$folder_new->attr('name', "BrendanVuTk");
//$folder_new->attr('parent_id', 0);
//$ret_folder = $box_net->create ($folder_new);
////$logger ->logDebug("sample::folder after creation=".var_export($ret_folder,1) );


echo '<html><body>';
echo $folder->print_to_table();

/**** Sample to delete a folder ****/
/**** Be very careful with this, esp if you set recursive to true
 ** It may delete your folder and all contents
 ** Disclaimer : I won't be responsible for your data loss if misused. Use this at your own risk !
 */
////$folder_id = xxxxxxx;
////$recursive = true;
////$result=$box_net->delete_folder($folder_id, $recursive);
////if ( empty($result) )
////	echo ('Delete successful !');
////else
////	var_dump($result);
//////$logger ->logDebug("sample::folder after deletion=".var_export($result,1) );

$folder_id = xxxxxxxx;
/**** Sample to copy a folder ****/
//$result=$box_net->copy_folder($folder_id, 0, 'n_TinhKyVuT');
////$logger ->logDebug("sample::folder after copy=".var_export($result,1) );
//var_dump($result);


/**** Sample to move a folder ****/
//$result=$box_net->move_folder($folder_id, 457305891, 'TinhKyVuT_rn');
////$logger ->logDebug("sample::folder after moved=".var_export($result,1) );
//var_dump($result);

//echo '</body></html>';


/*************** SAMPLE FILE OPERATIONS *************/
/**** replace the file_id of your own ****/
$file_id = 0000000;

/**** Sample to retrieve all details for the file ****/
//$file = $box_net->file($file_id);
////$logger ->logDebug("sample::file=".var_export($file,1) );

/**** Sample to retrieve all comments for the file ****/
//$comments = $box_net->get_file_comments ($file_id);
////$logger ->logDebug("sample::comments=".var_export($comments,1) );

/**** Sample to retrieve all file versions, note: only avail to premium customer ****/
//$version = $box_net->get_file_versions ($file_id);
////$logger ->logDebug("sample::versions=".var_export($version,1) );

/**** Sample to download a file ****/
//echo $box_net->download($file_id);

/**** Sample to copy a file ****/
//$result=$box_net->copy_file($file_id, 0, 'TK.jpg');
////$logger ->logDebug("sample::file after copy=".var_export($result,1) );
//var_dump($result);

/**** Sample to delete a file ****/
/**** Be very careful with this  ****
 ** Disclaimer : I won't be responsible for your data loss if misused. Use this at your own risk !
 */
////$file_id=000000;
////$file_sha1=$box_net->file($file_id)->attr('etag');
//////$logger ->logDebug("sample::file before delete:file_id=".$file_id.',file_sha1='.$file_sha1 );
////$result=$box_net->delete_file ($file_id, $file_sha1);
//////$logger ->logDebug("sample::file after delete=".var_export($result,1) );
////if ( empty($result) )
////	echo ('Delete successful !');
////else
////	var_dump($result);


/**** Sample to rename a file ****/
//$file_id=00000000;
//$old_name=$box_net->file($file_id)->attr('name');
////$logger ->logDebug("sample::file before delete:file_id=".$file_id.',old_name='.$old_name );
//$result=$box_net->rename_file ($file_id, 'new_'.$old_name);
////$logger ->logDebug("sample::file after rename=".var_export($result,1) );
//var_dump($result);


/**** Sample to move a file ****/
//$file_id=00000; $des_folder_id = xxxxx;
//$result=$box_net->move_file ($file_id, $des_folder_id);
////$logger ->logDebug("sample::file after move=".var_export($result,1) );
//var_dump($result);

/**** Sample to upload an updated version of an existing file ****/
////Note that for the folder path, you should indicate with forward splash (/), don't use backlash(\)
$file_name = "C:/tmp_touch.txt1";

////Assume we know the file we gonna update is in root folder
$file_list = $box_net->folder(0)->file;
$file_id = -1;
foreach ($file_list as $key=>$val)
	if ($val->attr('name') == 'tmp_touch.txt1')
	{
		$file_id = $val->attr('id');
		$etag = $val->attr('etag');
	}
if ( $file_id != -1 )
{
	$result=$box_net->upload_update_file($file_id, $file_name, $etag);
	//$logger ->logDebug("sample::new file after uploaded=".var_export($result,1)." -- ".$result );
	var_dump($result);
}

/**** Sample to upload a list of new files ****/
////Note that for the folder path, you should indicate with forward splash (/), don't use backlash(\)
//$file_name = array("C:/tmp_touch.txt1");
//$file_name[] = "C:/tmp_touch.txt";
//$destination_folder = 0;
//$result=$box_net->upload_new_file($destination_folder, $file_name);
////$logger ->logDebug("sample::file after uploaded=".var_export($result,1)." -- ".$result );
//var_dump($result);

?>