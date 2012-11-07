<?php 
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
//var_dump($folder);

/*************** SAMPLE FILE OPERATIONS *************/
/**** replace the file_id of your own ****/
$file_id = 0000000;

/**** Sample to retrieve all details for the file ****/
//$file = $box_net->file($file_id);
//var_dump($file);

/**** Sample to retrieve all comments for the file ****/
//$comments = $box_net->get_file_comments ($file_id);
//var_dump($comments);

/**** Sample to retrieve all file versions, note: only avail to premium customer ****/
//$version = $box_net->get_file_versions ($file_id);
//var_dump($version);

/**** Sample to delete a file ****/
/**** Be very careful with this  ****
 ** Disclaimer : I won't be responsible for your data loss if misused. Use this at your own risk !
 */
////$file_id=00000000;
////$file_sha1=$box_net->file($file_id)->attr('etag');
////$result=$box_net->delete_file ($file_id, $file_sha1);
////if ( empty($result) )
////	echo ('Delete successful !');
////else
////	var_dump($result);


/**** Sample to rename a file ****/
//$file_id=00000000;
//$old_name=$box_net->file($file_id)->attr('name');
//$result=$box_net->rename_file ($file_id, 'new_'.$old_name);
//var_dump($result);


/**** Sample to move a file ****/
//$file_id=00000; $des_folder_id = xxxxx;
//$result=$box_net->move_file ($file_id, $des_folder_id);
//var_dump($result);

/*************** SAMPLE FOLDER OPERATIONS *************/
/**** Sample to create new folder ****/
//$folder_new = new Box_Client_Folder();
//$folder_new->attr('name', "jkgarage");
//$folder_new->attr('parent_id', 0);
//$ret_folder = $box_net->create ($folder_new);
//var_dump($ret_folder);


echo '<html><body>';
echo $folder->print_to_table();

/**** Sample to download a file ****/
//echo $box_net->download($file_id);

/**** Sample to copy a folder ****/
//$new_f_name = 'n_dummy.nam';
//$result=$box_net->copy_file($file_id, 0, $new_f_name);
//var_dump($result);

/**** Sample to delete a folder a file ****/
/**** Be very careful with this, esp if you set recursive to true
 ** It may delete your folder and all contents
 ** Disclaimer : I won't be responsible for your data loss if misused. Use this at your own risk !
 */
////$folder_id = xxxxxx;
////$recursive = false;  //you can set to true to see it delete all the contents
////$result=$box_net->delete_folder($folder_id, $recursive);
////if ( empty($result) )
////	echo ('Delete successful !');
////else
////	var_dump($result);

$folder_id = xxxxxx;
/**** Sample to copy a folder ****/
//$new_folder_name = 'jkgarage_fold';
//$result=$box_net->copy_folder($folder_id, 0, $new_folder_name );
//var_dump($result);


/**** Sample to move a folder ****/
//$new_folder_name = 'jkgarage_fold';
//$destination_folder_id = xxxxx;
//$result = $box_net->move_folder($folder_id, $destination_folder_id, $new_folder_name);
//var_dump($result);
//
//
echo '</body></html>';

?>