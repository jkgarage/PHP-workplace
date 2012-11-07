The Box_Rest_Client is a simple way to access Box.net ReST API 2.0 through PHP.
This API is developed based on the SDK for Box 1.0, initially provided here https://github.com/box/box-php-sdk , and written by
https://github.com/AngeloR

## Dependencies
SimpleXML is required during the authentication. 
cURL is required for all the HTTP requests.

- cURL: http://us3.php.net/manual/en/curl.installation.php 
- SimpleXML: http://us3.php.net/manual/en/simplexml.installation.php


## How does it work?
`Box_Rest_Client` provides a standard way to execute various api methods from the Box API 2.0 .

I try to provide the most common functions you likely use with Box API 2.0 .
This work is continued to be updated, as of now it supports :
		
	/******************** [region] COMMON METHODS IN API ****************************/
	/** This will lead your user to key in the Box credential and give you the authen_token */
	public function authenticate()	
		
	/* This is a generic GET request, you can use it to access any BOX API using GET
	*  Note that this method return 2 different types of data depending on the opts :
	*      1. Raw data, if the request is a download action
	*      2. Parsed data from json object, if the request doesn't have any download action
	*/
	public function get_api2($api, array $url_opts = array(), array $header_ops = array()) 
	
	/* This is a generic POST request, you can use it to access any BOX API using POST **/
	public function post($api, array $params = array(), array $url_opts = array())
	/******************** [end region] COMMON METHODS IN API ************************/
	
	
	/**************** [region] FOLDERS RELATED FUNCTIONS ****************************/
	// retrieve folder info + load the folders into tree structure
	public function folder($root);
	
	/* Creates a folder on the server with the specified attributes */
	public function create(Box_Client_Folder $folder);
	
	/* This function will delete the folder specified AND ALL ITS CONTENT, SUB-FOLDERS + FILES */
	public function delete_folder ($folder_id, $recursive = false);
	
	/* This function will copy the folder specified AND ALL ITS CONTENT, SUB-FOLDERS + FILES to a new folder */
	public function copy_folder ($folder_id, $destination_parent_id, $new_name='')

	/* This function will move the folder specified AND ALL ITS CONTENT, SUB-FOLDERS + FILES to a new location */
	public function move_folder ($folder_id, $destination_parent_id, $new_name='')
	/**************** [end region] FOLDERS RELATED FUNCTIONS ************************/
	
	
	/**************** [region]  FILES RELATED FUNCTIONS *****************************/
	/* This will grab the info for a file and push it back as a Box_Client_File. */
	public function file($file_id)
	
	/* Get the list of all comments for this file */
	public function get_file_comments ($file_id)
	
	/* Get the list of all versions for this file */
	public function get_file_versions ($file_id)

	//use API 2.0, download file and write the file to local folder
	public function download($file_id, $saved_name = '')
	
	/* This function will delete the file specified */
	public function delete_file ($file_id, $file_sha1)
	
	/* This function will copy the file specified to a new location **/
	public function copy_file ($file_id, $destination_parent_id, $new_name='')
	
	/* This function will move the file specified to a new location **/
	public function move_file ($file_id, $destination_parent_id, $new_name='')
	
	/* This function will rename the file specified to new name **/
	public function rename_file ($file_id, $new_name)
	/*************** [end region] FILES RELATED FUNCTIONS ***************************/


If you find yourself limited by the wrapper functions provided, feel free to invoke BOX API 2.0 directly using the  generic get()/post() methods .

For more details and some code examples, take a look at the sample.php file. 

I will continue to update more functions of the API, and a better documentations.
For suggestion/feedback, please feel free to drop me a note.