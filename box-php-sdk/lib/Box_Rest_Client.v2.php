<?php
define('SITE_LOG', './logs');
include ('KLogger.php');
$logger = KLogger::instance(SITE_LOG, KLogger::DEBUG);

/*************************************************************************************
 *    ___  ____ _  _     ____ ____ ____ ___     ____ _    _ ____ _  _ ___ 
 *		|__] |  |  \/      |__/ |___ [__   |      |    |    | |___ |\ |  |  
 *		|__] |__| _/\_ ___ |  \ |___ ___]  |  ___ |___ |___ | |___ | \|  |  v0.2
 *
 *
 * 05 Nov 2012 - Brendan Vu : v0.1 for Box API 2.0 
 * 05 Dec 2012 - Brendan Vu : v0.2, include file upload
 *
 * Special thanks to Angelo R for the initial build of this library
 **************************************************************************************
 
 *	The Box_Rest_Client v2 is a PHP library for accessing the Box.net ReST api 2.0. It 
 *	provides a PHP cURL based interface that allows access to any number of 
 *  api methods that are currently in place. The code is built in a way to 
 *  ensure modularity, easy updates (everything is this one file) and aims to 
 *  be a simple easy to use solution for working with the excellent Box api. 
 *  
 *  Each of the classes in this file was licensed under the MIT Licensing 
 *  agreement located below this introductory comment block. 
 *  
 *  Dependencies:
 *  	1) cURL: This library relies on cURL to perform the http verbs. Without 
 *  			it, this library will not function. 
 *  	2) SimpleXML: Results from the Box api currently return (sometimes 
 *  			malformed) xml. 
 *  	3) KLogger : to capture log messages for Trace/Debug purpose.
 *  
 *  
 *  Installation:
 *  	1) Copy this file wherever you want.
 *      2) Donwload KLogger.php and place in the same folder as this file
 *  
 *  Using:
 *  	Please check out the sample.php file included with this download. 
 *      You should see sample of the most commonly used functions there.
 *      I would continue to add more documentations but that's not a priority right now. 
 *  
 */


// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
// http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.


/**
 *
 * I recommend that you pull this class into its own file and the proceed to make
 * any modifications to it you want. It will always receive the auth_token as the
 * first argument and then you are free to do whatever you want with it.
 *
 * Since we invoke the class like it has a constructor, you could potentially
 * connect to a database and create more methods (apart from store) that could
 * act as a model for the authentication token.
 * @author Angelo R
 *
 */
class Box_Rest_Client_Auth
{

    /**
     * 
     * This is the method that is called whenever an authentication token is  
     * received. 
     * 
     * @param string $auth_token
     */
    public function store($auth_token)
    {
        return $auth_token;
    }
}

/**
 * 
 * The ReST Client is really what powers the entire class. It provides access 
 * the the basic HTTP verbs that are currently supported by Box
 *
 */

class Rest_Client
{
    /**
     * Perform any get type operation on a url
     * @param string $url
     *        array $header_opts : indicate the parameter to be included in header
     *              header format is "Authorization: BoxAuth $_key1=$_val1&$_key2=$_val2"
     * @return string The resulting data from the get operation
     * ** Note ** : for authentication(), Box still use API 1.0 so $header_opts MUST BE EMPTY
     *              for any other types, header_opts typically contain the api_key and auth_token
     */
    public static function get($url, array $header_opts)
    {
        //GET action is sent without any GET parameter options
        return self::custom_curl('get', $url, array(), $header_opts);
    }

    /**
     * 
     * Perform any post type operation on a url
     * @param string $url
     * @param array $params A list of post-based params to pass
     */
    public static function post($url, array $params = array(), array $header_opts)
    {
        return self::custom_curl('post', $url, $params, $header_opts);
    }

    /**
     * 
     * Perform any post type operation on a url
     * @param string $url
     * @param array $params A list of post-based params to pass
     */
    public static function delete($url, array $params = array(), array $header_opts)
    {
        return self::custom_curl('delete', $url, $params, $header_opts);
    }

    /**
     * 
     * Perform any put type operation on a url
     * @param string $url
     * @param array $params A list of post-based params to pass
     */
    public static function put($url, array $params = array(), array $header_opts)
    {
        return self::custom_curl('put', $url, $params, $header_opts);
    }

    /**  Define this private single method so it can be used in get/post/delete/put without rewriting
     *   @author Brendan Vu
	 *
     *   Currently, the code appears to violate any good pattern design, but since these operations
     *   doesnt change behavior much, and there are only 4 of them, let's just ignore pattern design here.
     *   
     *   @param : $operation   : can be 'get', 'post', 'delete', 'put'
     *			 $url         : the destination url of this REST request
     *			 $params      : optional parameter, the data sent if body of the request
     *			 $header_opts : required field, the data sent in header of the request
     *							Mandatory since at least you need to send in the auth_token and api_key details
     *						    Format of $header_opts is and array of strings									
     *							Example : 
     *							{ 'Authorization: BoxAuth auth_token=1234&api_key=jfkdh', 'If-Match: a_unique_sha1' }
     */
    private static function custom_curl($operation, $url, array $params = array(),
        array $header_opts)
    {
        $c_logger = KLogger::instance(SITE_LOG, KLogger::DEBUG);
        $c_logger->logDebug('Rest_Client::custom_curl()::operation=' . $operation .
            ', url=' . $url);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");

        if ($operation == 'post') {
            curl_setopt($ch, CURLOPT_POST, true);

            $c_logger->logDebug('Rest_Client::custom_curl()::params=' . json_encode($params));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        } else
            if ($operation == 'delete')
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            else
                if ($operation == 'put') {
                    curl_setopt($ch, CURLOPT_PUT, true);

                    $put_data = json_encode($params);
                    $c_logger->logDebug('Rest_Client::custom_curl()::params=' . $put_data .
                        ', strlen=' . strlen($put_data));

                    $put_file = tmpfile();
                    fwrite($put_file, $put_data);
                    fseek($put_file, 0);
                    curl_setopt($ch, CURLOPT_INFILE, $put_file);
                    curl_setopt($ch, CURLOPT_INFILESIZE, strlen($put_data));
                }

        if (is_array($header_opts) && !empty($header_opts)) {
            $c_logger->logDebug('Rest_Client::custom_curl()::header_opts=' . var_export($header_opts,
                1));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header_opts);
        }

        //as advised by Box, for download file (ie. operation='get') , need to enable CURLOPT_FOLLOWLOCATION
        if ($operation == 'get' && strstr($url, '/files/') && strstr($url, '/content')) {
            $c_logger->logDebug('Rest_Client::custom_curl()::Set CURLOPT_FOLLOWLOCATION to true');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }

        $data = curl_exec($ch);
        curl_close($ch);
        if (isset($put_file) || !empty($put_file))
            fclose($put_file);
        return $data;
    }
}


/**********************************************************************************
***********************************************************************************
***********************************************************************************/

/**
 * This is the main API class. This is what you will be invoking when you are dealing with the 
 * API. 
 *
 */
class Box_Rest_Client
{
    private $c_logger;
    private $classname;

    public $api_key;
    public $ticket;
    public $auth_token;

    //public $api_version = '2.0';
    public $base_url = 'https://api.box.com/2.0';
    public $base_url_api1 = 'https://www.box.net/api/1.0'; //keep this URL for authentication, which is still in API 1.0
    public $upload_url = 'https://upload.box.net/api';

    // Not implemented yet sadly..
    public $MOBILE = false;

    /**
     * You need to create the client with the API KEY that you received when 
     * you signed up for your apps. 
     * 
     * @param string $api_key
     */
    public function __construct($api_key = '')
    {
        $this->c_logger = KLogger::instance(SITE_LOG, KLogger::DEBUG);
        $this->classname = 'Box_Rest_Client';

        if (empty($this->api_key) && empty($api_key)) {
            throw new Box_Rest_Client_Exception('Invalid API Key. Please provide an API Key when creating an instance of the class, or by setting Box_Rest_Client->api_key');
        } else {
            $this->api_key = (empty($api_key)) ? $this->api_key : $api_key;
        }
    }


    /******************** [region] COMMON METHODS IN API ****************************/
    /**
     * 
     * Because the authentication method is an odd one, I've provided a wrapper for 
     * it that should deal with either a mobile or standard web application. You 
     * will need to set the callback url from your application on the developer 
     * website and that is called automatically. 
     * 
     * When this method notices the "auth_token" query string, it will automatically 
     * call the Box_Rest_Client_Auth->store() method. You can do whatever you want 
     * with it in that method. I suggest you read the bit of documentation that will 
     * be present directly above the class.
     */
    public function authenticate()
    {
        if (array_key_exists('auth_token', $_GET)) {
            $this->c_logger->logDebug($this->classname .
                '::authenticate::Getting auth_token from _GET..');
            $this->auth_token = $_GET['auth_token'];

            $box_rest_client_auth = new Box_Rest_Client_Auth();
            return $box_rest_client_auth->store($this->auth_token);
        } else {
            $this->c_logger->logDebug($this->classname .
                '::authenticate::Start authenticating..');
            $res = $this->get('get_ticket', array('api_key' => $this->api_key));
            if ($res['status'] === 'get_ticket_ok') {
                $this->ticket = $res['ticket'];

                if ($this->MOBILE) {
                    header('location: https://m.box.net/api/1.0/auth/' . $this->ticket);
                } else {
                    header('location: https://www.box.net/api/1.0/auth/' . $this->ticket);
                }
            } else {
                throw new Box_Rest_Client_Exception($res['status']);
            }
        }
    }

    //todo
    /* Box API : GET /shared_items
    *  Example : curl https://api.box.com/2.0/shared_items
    * 			-H "Authorization: BoxAuth api_key=API_KEY&auth_token=AUTH_TOKEN&shared_link=SHARED_LINK"
    */
    //public function get_shared_items ($shared_link) {
    //	$res = $this->get_api2('shared_items', array(), array('Athorization:'=>'shared_link='.$shared_link) );
    //	return $res;
    //}

    /**
     * Executes an api function using get with the required opts. It will attempt to 
     * execute it regardless of whether or not it exists.
     * This method point to the base URL in API 1.0 . Mainly used for authentication .
     * 
     * @param string $api
     * @param array $opts
     */
    public function get($api, array $opts = array())
    {
        $this->c_logger->logDebug($this->classname . '::get(api=' . $api . ', opts=' .
            var_export($opts, 1) . ')');

        $opts = $this->set_opts($opts);
        $url = $this->build_url($api, $opts);

        $this->c_logger->logDebug($this->classname . '::get()-url=' . $url . ', opts=' .
            var_export($opts, 1));
        $data = Rest_Client::get($url, array(''));

        return $this->parse_result($data);
    }


    /** In API 2.0, always have to send authentication details in header
     *  "Authorization: BoxAuth api_key=API_KEY&auth_token=AUTH_TOKEN"
	 *  @author Brendan Vu
	 *
     *  Prerequisite : this method can only be called after authentication phase, 
     *                 when we have both $this->api_key and $this->auth_token
     *   Note that this method return 2 different types of data depending on the opts :
     *      1. Raw data, if $url_opts include a download action 'content'
     *      2. Parsed data from json object, if $opts doesn't have any download action
     *   @param
     *  	   $api 	   : is the api you want to access, eg. 'folders', 'files', 'comments'
     *      $url_opts   : is the additional options you want to include in URL, 
     *					 eg. ( {folder id}, items ), ( {file id}, content )
     *  				   note that the $url_opts should EXCLUDE the default : ('api_key'=>api_key) and ('auth_token'=>auth_token)
     *      $header_ops : is the additional options you want to include in HEADER
     *  				   this should EXCLUDE the default header
     *                    "Authorization: BoxAuth api_key=API_KEY&auth_token=AUTH_TOKEN"
     *  				   Example : { 'Authorization:'=>'shared_link=SHARED_LINK', 'If-Match:'=>'a_unique_sha1' }
     *                  Note : If the header_ops contain any entry belonging to "Authorization:",
     *					      it will combine with the default header 
     */
    public function get_api2($api, array $url_opts = array(), array $header_ops =
        array())
    {
        $url = $this->build_url_api2($api, $url_opts);

        //construct the header here
        $this->c_logger->logDebug($this->classname . '::get_api2()-url=' . $url);

        $header_details = array($this->default_authen_header());
        //if there are additional options in header_ops, include them in
        if (!empty($header_ops))
            foreach ($header_ops as $key => $val) {
                //append to the default header, if it is 'Authorization' tag
                if ($key == 'Authorization:')
                    $header_details[0] .= '&' . $val;
                else
                    $header_details[] = $key . ' ' . $val;
            }
        $data = Rest_Client::get($url, $header_details);

        //if this is for downloading file content, do not parse result, just return the raw data
        if (strstr($url, '/files/') && strstr($url, '/content'))
            return $data;

        return $this->parse_result_api2($data);
    }

    /**
     * Executes an api function using post with additional options. It will
     * attempt to execute it regardless of whether or not it exists.
     *
     * @param string $api     : the base url of the request
     * @param array $params   : data to be sent in the post request
     * @param array $url_opts : additional options to include in url, usually empty
     */
    public function post($api, array $params = array(), array $url_opts = array())
    {
        $url = $this->build_url_api2($api, $opts);

        //this is the default header details
        $header_details = array($this->default_authen_header());

        $data = Rest_Client::post($url, $params, $header_details);
        return $this->parse_result_api2($data);
    }
    /******************** [end region] COMMON METHODS IN API ************************/


    /**************** [region] FOLDERS RELATED FUNCTIONS ****************************/
    /**
     * 
     * This folder method is provided as it tends to be what a lot of people will most 
     * likely try to do. It returns a list of folders/files utilizing our 
     * Box_Client_Folder and Box_Client_File classes instead of the raw tree array 
     * that is normally returned.
     * 
     * You can totally ignore this and instead rely entirely on get/post and parse the 
     * tree yourself if it doesn't quite do what you want.
     * 
     * @param int $root The root directory that you want to load the tree from.
     * @param string $params Any additional params you want to pass, comma separated.
     * @return Box_Client_Folder
     */

    // retrieve folder info + load the folders into tree structure
    // return an empty object if the folder doesn't exist
    // BOX API : GET /folders/{folder id}
    // Sample :
    //    curl https://api.box.com/2.0/folders/FOLDER_ID \
    //    -H "Authorization: BoxAuth api_key=API_KEY&auth_token=AUTH_TOKEN"
    public function folder($root)
    {
        $res = $this->get_api2('folders/' . $root);

        $folder = new Box_Client_Folder;

        //check if the return is a folder, import the tree structure into Box_Client_Folder object
        if (is_array($res) && array_key_exists('type', $res) && $res['type'] == 'folder') {
            $folder->import($res);
        }
        return $folder;
    }

    /**
     *  Creates a folder on the server with the specified attributes.
     * 
     *  Box API : POST /folders
     *  Example : curl https://api.box.com/2.0/folders/ \
     *		     -H "Authorization: BoxAuth api_key=API_KEY&auth_token=AUTH_TOKEN" \
     * 			 -d '{"name":"New Folder", "parent": {"id": "0"}}' \
     *		     -X POST
     *
     *  @param Box_Client_Folder $folder
     */
    public function create(Box_Client_Folder $folder)
    {
        $post_params = array('name' => $folder->attr('name'), 'parent' => array('id' =>
                    intval($folder->attr('parent_id'))));

        $resp = $this->post('folders', $post_params, array());
        $result = new Box_Client_Folder();

        if (array_key_exists('type', $resp) && $resp['type'] == 'folder') {
            $result->import($resp);
            return $result;
        } else
            return $resp;
    }


    /** Box API : DELETE /folders/{folder id}
     *  Example : curl https://api.box.com/2.0/folders/FOLDER_ID?recursive=true  \
     *		     -H "Authorization: BoxAuth api_key=API_KEY&auth_token=AUTH_TOKEN" \
     *			 -X DELETE
     *  This function will delete the folder specified AND ALL ITS CONTENT, SUB-FOLDERS + FILES
     *  So please be extra careful when calling this !!!
     *  @prerequisite : can call this method after authenticate(), when we have both 'api_key', and 'auth_token'
     *  @param : $folder_id : the folder to be deleted
     *           $recursive : true/false, to indicate whether to force delete all folder contents\
     *                        Default to false to prevent accident deletion of folder and contents
     */
    public function delete_folder($folder_id, $recursive = false)
    {
        $converted_res = ($recursive) ? 'true' : 'false';

        $this->c_logger->logDebug($this->classname . '::delete_folder(folder_id=' . $folder_id .
            ', recursive=' . $converted_res . ')');
        $url = $this->build_url_api2('folders/' . $folder_id, array('recursive' => $converted_res));

        $this->c_logger->logDebug($this->classname . '::delete_folder()-url=' . $url);

        $header_details = array($this->default_authen_header());
        $res = Rest_Client::delete($url, array(''), $header_details);

        return $res;
    }

    /** Box API : POST /folders/{folder id}/copy
     *  Example : curl https://api.box.com/2.0/folders/FOLDER_ID/copy \
     *			 -H "Authorization: BoxAuth api_key=API_KEY&auth_token=AUTH_TOKEN" \
     *			 -d '{"parent": {"id" : DESTINATION_FOLDER_ID}}' \
     *			 -X POST
     *  This function will copy the folder specified AND ALL ITS CONTENT, SUB-FOLDERS + FILES to a new folder
     *  @prerequisite : can call this method after authenticate(), when we have both 'api_key', and 'auth_token'
     *  @param : $folder_id             : the folder to be copied
     *           $destination_parent_id : required field, id of the destination folder
     *           $new_name         	   : optional field, new name of the folder after copy
     *									 If not indicated, reuse the old name
     */
    public function copy_folder($folder_id, $destination_parent_id, $new_name = '')
    {
        $this->c_logger->logDebug($this->classname . '::copy_folder(folder_id=' . $folder_id .
            ', destination_parent_id=' . $destination_parent_id . ', new_name=' . $new_name .
            ')');
        $url = $this->build_url_api2('folders/' . $folder_id . '/copy', array());

        $this->c_logger->logDebug($this->classname . '::copy_folder()-url=' . $url);

        $header_details = array($this->default_authen_header());
        $post_params = array('parent' => array('id' => $destination_parent_id));
        if (!empty($new_name))
            $post_params['name'] = $new_name;
        $res = Rest_Client::post($url, $post_params, $header_details);

        return $res;
    }


    /** Box API : PUT /folders/{folder id}
     *  Example : curl https://api.box.com/2.0/folders/FOLDER_ID \
     *			 -H "Authorization: BoxAuth api_key=API_KEY&auth_token=AUTH_TOKEN" \
     *			 -d '{"parent": {"id" : DESTINATION_FOLDER_ID}, "name":"New Folder Name!"}' \
     *			 -X PUT
     *  This function will move the folder specified AND ALL ITS CONTENT, SUB-FOLDERS + FILES to a new location
     *  This function utilise the Update Information About a Folder Box API
     *  @prerequisite : can call this method after authenticate(), when we have both 'api_key', and 'auth_token'
     *  @param : $folder_id             : the folder to be copied
     *           $destination_parent_id : required field, id of the destination folder
     *           $new_name         	   : optional field, new name of the folder after move
     *									 If not indicated, reuse the old name
     */
    public function move_folder($folder_id, $destination_parent_id, $new_name = '')
    {
        $this->c_logger->logDebug($this->classname . '::move_folder(folder_id=' . $folder_id .
            ', destination_parent_id=' . $destination_parent_id . ', new_name=' . $new_name .
            ')');
        $url = $this->build_url_api2('folders/' . $folder_id, array());

        $this->c_logger->logDebug($this->classname . '::move_folder()-url=' . $url);

        $header_details = array($this->default_authen_header());
        if (empty($new_name))
            $new_name = $this->folder($folder_id)->attr('name');

        $put_params = array('parent' => array('id' => $destination_parent_id), 'name' =>
                $new_name);
        $res = Rest_Client::put($url, $put_params, $header_details);

        return $res;
    }
    /**************** [end region] FOLDERS RELATED FUNCTIONS ************************/


    /**************** [region]  FILES RELATED FUNCTIONS *****************************/
    /**
     * 
     * Since we provide a way to get information on a folder, it's only fair that we 
     * provide the same interface for a file. This will grab the info for a file and 
     * push it back as a Box_Client_File. Note that this method (for some reason) 
     * gives you less information than if you got the info from the tree view. 
     * 
     * @param int $file_id
     * @return Box_Client_File
     */
    public function file($file_id)
    {
        $res = $this->get_api2('files/' . $file_id);

        $file = new Box_Client_File;

        //check if the return is a file, then import it into Box_Client_File object
        if (array_key_exists('type', $res) && $res['type'] == 'file')
            $file->import($res);

        return $file;
    }

    /**  Box API : GET /files/{file id}/comments

     * Example :  curl https://api.box.com/2.0/files/FILE_ID/comments \
     * -H "Authorization: BoxAuth api_key=API_KEY&auth_token=AUTH_TOKEN"
     */
    public function get_file_comments($file_id)
    {
        $this->get_objs_by_action($file_id, 'comments');
    }

    /** Box API : GET /files/{file id}/versions
     *
     *  Example : curl https://api.box.com/2.0/files/FILE_ID/versions
     * -H "Authorization: BoxAuth api_key=API_KEY&auth_token=AUTH_TOKEN"
     */
    public function get_file_versions($file_id)
    {
        return $this->get_objs_by_action($file_id, 'versions');
    }

    /** use API 2.0, download file and write the file to local folder
     *  @param $file_id    : the file_id of the file to download
     *  $saved_name : the name of the file to be saved to on local folder
     *                if there is no name specified, use the same file_name stored on Box
     *  return : true if save is successful, false if failed
     *  Box.net API : GET /files/{file id}/content
     *  Sample : curl -L https://api.box.com/2.0/files/FILE_ID/content \
     *               -H "Authorization: BoxAuth api_key=API_KEY&auth_token=AUTH_TOKEN"
     */
    public function download($file_id, $saved_name = '')
    {
        $data = $this->get_api2('files/' . $file_id . '/content');

        $name = $saved_name;
        if (empty($saved_name))
            $name = $this->file($file_id)->attr('name');

        // Write the contents back to the file
        return file_put_contents($name, $data);
    }

    /** Box API : DELETE /files/{file id}
     *  Example : curl https://api.box.com/2.0/files/FILE_ID  \
     *			 -H "Authorization: BoxAuth api_key=API_KEY&auth_token=AUTH_TOKEN" \
     *			 -H "If-Match: _a_unique_sha1" \
     *			 -X DELETE
     *            where _a_unique_sha1 is the sha1 of the file, this is in the ‘etag’ field of the file obj
     *  This function will delete the file specified . So please be extra careful when calling this !!!
     *  @prerequisite : can call this method after authenticate(), when we have both 'api_key', and 'auth_token'
     *  @param : $file_id   : the file to be deleted
     *			$file_sha1 : required field. You need to obtain this from the ‘etag’ field of the file obj
     *						 It is required here to doubly sure you are gonne delete the correct file.
     */
    public function delete_file($file_id, $file_sha1)
    {
        $this->c_logger->logDebug($this->classname . '::delete_file(file_id=' . $file_id);
        $url = $this->build_url_api2('files/' . $file_id, array());

        $this->c_logger->logDebug($this->classname . '::delete_file()-url=' . $url);

        $header_details = array($this->default_authen_header(), 'If-Match: ' . $file_sha1);
        $res = Rest_Client::delete($url, array(''), $header_details);

        return $res;
    }

    /** Box API : POST /files/{file id}/copy
     *  Example : curl https://api.box.com/2.0/files/FILE_ID/copy \
     *				 -H "Authorization: BoxAuth api_key=API_KEY&auth_token=AUTH_TOKEN" \
     *				 -d '{"parent": {"id" : FOLDER_ID}}' \
     *				 -X POST
     *  This function will copy the file specified to a new location
     *  @prerequisite : can call this method after authenticate(), when we have both 'api_key', and 'auth_token'
     *  @param : $file_id               : the file to be copied
     *           $destination_parent_id : required field, id of the destination folder
     *           $new_name         	   : optional field, new name of the file after copy
     *									 If not indicated, reuse the old name
     */
    public function copy_file($file_id, $destination_parent_id, $new_name = '')
    {
        $this->c_logger->logDebug($this->classname . '::copy_file(file_id=' . $file_id .
            ', destination_parent_id=' . $destination_parent_id . ', new_name=' . $new_name .
            ')');

        $url = $this->build_url_api2('files/' . $file_id . '/copy', array());

        $this->c_logger->logDebug($this->classname . '::copy_file()-url=' . $url);

        //this is the default header details
        $header_details = array($this->default_authen_header());
        $post_params = array('parent' => array('id' => $destination_parent_id));
        if (!empty($new_name))
            $post_params['name'] = $new_name;
        $res = Rest_Client::post($url, $post_params, $header_details);

        return $res;
    }

    /**
     *  This function will move the file specified to a new location
     *  This function utilise the Update Information About a File Box API
     *  @prerequisite : can call this method after authenticate(), when we have both 'api_key', and 'auth_token'
     *  @param : $file_id               : the file to be moved
     *           $destination_parent_id : mandatory field, id of the destination folder
     *           $new_name         	   : optional field, new name of the file after move
     *								   If not indicated, reuse the old name
     */
    public function move_file($file_id, $destination_parent_id, $new_name = '')
    {
        return $this->move_rename_file($file_id, $destination_parent_id, $new_name);
    }

    /**
     *  This function will rename the file specified to new name
     *  This function utilise the Update Information About a File Box API
     *  @prerequisite : can call this method after authenticate(), when we have both 'api_key', and 'auth_token'
     *  @param : $file_id    : the file to be renamed
     *           $new_name   : mandatory field, new name of the file
     */
    public function rename_file($file_id, $new_name)
    {
        return $this->move_rename_file($file_id, '', $new_name);
    }

    /**
     *  This function will upload a list of new file to the designated folder
	 *  If file already exists in the designated folder, error will be return, old file will NOT be over-written
     *  Box API : POST /files/content
     *  Example : curl https://api.box.com/2.0/files/content \
     *			 -H "Authorization: BoxAuth api_key=API_KEY&auth_token=AUTH_TOKEN" \
     *			 -F filename1=@FILE_NAME1 \
     *			 -F filename2=@FILE_NAME2 \
     *			 -F folder_id=FOLDER_ID
     *  @prerequisite : can call this method after authenticate(), when we have both 'api_key', and 'auth_token'
     *  @param : $folder_id    : the destination folder where files are uploaded to
     *           $file_names   : mandatory field, array of names of files to be uploaded
	 *                           Supported Filenames: Filenames with characters \ / ” : < > | * ? 
	 *							 and filenames with leading or trailing whitespace are not allowed.
	 *  @return : return the response from box.net
     */
    public function upload_new_file($folder_id, array $file_names)
    {       
        $url = 'https://api.box.com/2.0/files/content';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");
        
        $header_details = array($this->default_authen_header());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_details);
        
        $post_vars = array();
        $i = 1;
        
        $post_vars['folder_id'] = $folder_id;
        
        foreach ($file_names as $key => $val) {
            //The at "@" below tells cURL it needs to send content of a file
            //not the text of file path
            $post_vars['filename'.$i] = '@'.$val;
            $i++;
        }

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vars );
        
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
	
	/**
     *  This function will upload a newer version of a file
	 *  The file should already exist in your Box account
	 *  If there is no such file, error will be return, new file will NOT be uploaded.
     *  Box API : POST /files/{file id}/content
     *  Example : curl https://api.box.com/2.0/files/FILE_ID/content \
     *			 -H "Authorization: BoxAuth api_key=API_KEY&auth_token=AUTH_TOKEN" \
	 *	         -H "If-Match: ETAG_OF_ORIGINAL"
     *			 -F filename=@FILE_NAME
     *  @prerequisite : can call this method after authenticate(), when we have both 'api_key', and 'auth_token'
     *  @param : $file_id    : mandatory, file id of the file you are going to update 
     *           $file_name  : mandatory, file name of the file you are going to update. Need to match with the file id
	 *                         Supported Filenames: Filenames with characters \ / ” : < > | * ? 
	 *						   and filenames with leading or trailing whitespace are not allowed.
	 *           $etag       : value captured in etag field of the original file
	 *  @return : return the response from box.net
     */
    public function upload_update_file($file_id, $file_name, $etag)
    {       
        $url = 'https://api.box.com/2.0/files/'.$file_id.'/content';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");
        
        $header_details = array($this->default_authen_header());
		$header_details[] = 'If-Match: '.$etag;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_details);
			
        $post_vars = array();
        
        //The at "@" below tells cURL it needs to send content of a file
        //not the text of file path
        $post_vars['filename'] = '@'.$file_name;

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vars );
        
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /*************** [end region] FILES RELATED FUNCTIONS ***************************/


    /*************** [region ] PRIVATE FUNCTIONS, NOT BELONG TO API *****************/
    // This can be used for getting file details where the return is a list of objects
    // eg. get comments, get versions
    // Pre-requisite : the response must contain at least 2 fields : 'total_count' and 'entries'
    private function get_objs_by_action($file_id, $action)
    {
        $res = $this->get_api2('files/' . $file_id . '/' . $action);

        $obj_list = array();

        //check if the return is not an error, then import the list of comments into our Box_Client_Object object
        if (array_key_exists('total_count', $res) && array_key_exists('entries', $res)) {
            foreach ($res['entries'] as $key => $val) {
                $obj = new Box_Rest_Client_Object;
                $obj->import($val); //this will also handle the setting of 'type' for object
                $obj_list[] = $obj;
            }
            $res['entries'] = $obj_list;
        }

        return $res;
    }

    private function default_authen_header()
    {
        $header_str = 'Authorization: BoxAuth api_key=' . $this->api_key .
            '&auth_token=' . $this->auth_token;
        return $header_str;
    }

    /**
     * To minimize having to remember things, get/post will automatically 
     * call this method to set some default values as long as the default 
     * values don't already exist.
     * 
     * @param array $opts
     */
    private function set_opts(array $opts)
    {
        if (!array_key_exists('api_key', $opts)) {
            $opts['api_key'] = $this->api_key;
        }

        if (!array_key_exists('auth_token', $opts)) {
            if (isset($this->auth_token) && !empty($this->auth_token)) {
                $opts['auth_token'] = $this->auth_token;
            }
        }

        return $opts;
    }

    /**
     * 
     * Build the final api url that we will be curling. This will allow us to 
     * get the results needed. 
     * 
     * @param string $api_func
     * @param array $opts
     */
    private function build_url($api_func, array $opts)
    {
        $base = $this->base_url_api1 . '/rest';

        $base .= '?action=' . $api_func;
        foreach ($opts as $key => $val) {
            if (is_array($val)) {
                foreach ($val as $i => $v) {
                    $base .= '&' . $key . '[]=' . $v;
                }
            } else {
                $base .= '&' . $key . '=' . $val;
            }
        }
        return $base;
    }

    private function build_url_api2($api_func, array $opts)
    {
        $base = $this->base_url . '/' . $api_func;

        if (!empty($opts)) {
            $base .= '?';
            foreach ($opts as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $i => $v) {
                        $base .= $key . '[]=' . $v . '&';
                    }
                    if (substr($base, -1) == '&')
                        $base = substr($base, 0, -1); //remove last '&'
                } else {
                    $base .= $key . '=' . $val . '&';
                }
            }
            if (substr($base, -1) == '&')
                $base = substr($base, 0, -1); //remove last '&'
        }

        return $base;
    }

    /**
     * 
     * Converts the XML we received into an array for easier messing with. 
     * Obviously this is a cheap hack and a few things are probably lost along 
     * the way (types for example), but to get things up and running quickly, 
     * this works quite well. 
     * 
     * @param string $res
     */
    private function parse_result($res)
    {
        $xml = simplexml_load_string($res);
        $json = json_encode($xml);
        $array = json_decode($json, true);

        return $array;
    }

    // in API 2, the response is not in XML, so no need to parse
    private function parse_result_api2($res)
    {
        $array = json_decode($res, true);
        //$this->c_logger->logDebug($this->classname.'::parse_result_api2::array='.var_export($array,1) );
        return $array;
    }

    /** Box API : PUT /files/{file id}
     *  Example : curl https://api.box.com/2.0/files/FILE_ID \
     *			 -H "Authorization: BoxAuth api_key=API_KEY&auth_token=AUTH_TOKEN" \
     *			 -d '{"parent":{"id":0} }' \
     *			 -X PUT
     *  This function will move the file specified to a new location, or a new name (ie. rename)
     *  This function utilise the Update Information About a File Box API
     *  @prerequisite : can call this method after authenticate(), when we have both 'api_key', and 'auth_token'
     *  @param : $file_id               : the file to be moved/renamed
     *           $destination_parent_id : optional field, id of the destination folder
     *								   If not indicated, doesnt move file but instead rename
     *           $new_name         	   : optional field, new name of the folder after move
     *								   If not indicated, reuse the old name
     */
    private function move_rename_file($file_id, $destination_parent_id = '', $new_name =
        '')
    {
        $this->c_logger->logDebug($this->classname . '::move_rename_file(file_id=' . $file_id .
            ', destination_parent_id=' . $destination_parent_id . ', new_name=' . $new_name .
            ')');
        $url = $this->build_url_api2('files/' . $file_id, array());

        $this->c_logger->logDebug($this->classname . '::move_rename_file()-url=' . $url);

        $header_details = array($this->default_authen_header());

        $put_params = array();
        if (!empty($destination_parent_id))
            $put_params['parent'] = array('id' => $destination_parent_id);
        if (!empty($new_name))
            $put_params['name'] = $new_name;

        $res = Rest_Client::put($url, $put_params, $header_details);

        return $res;
    }
    /*************** [end region ] PRIVATE FUNCTIONS, NOT BELONG TO API *************/


    /**
     * Returns the url to upload a file to the specified parent folder. Beware!
     * If you screw up the type the upload will probably still go through properly
     * but the results may be unexpected. For example, uploading and overwriting a
     * end up doing two very different things if you pass in the wrong kind of id
     * (a folder id vs a file id).
     *
     * For the right circumstance to use each type of file, check this:
     * http://developers.box.net/w/page/12923951/ApiFunction_Upload%20and%20Download
     *
     * @param string $type One of upload | overwrite | new_copy
     * @param int $id The id of the file or folder that you are uploading to
     */
    public function upload_url($type = 'upload', $id = 0)
    {
        $url = '';
        switch (strtolower($type)) {
            case 'upload':
                $url = $this->upload_url . '/' . $this->api_version . '/upload/' . $this->
                    auth_token . '/' . $id;
                break;
            case 'overwrite':
                $url = $this->upload_url . '/' . $this->api_version . '/overwrite/' . $this->
                    auth_token . '/' . $id;
                break;
            case 'new_copy':
                $url = $this->upload_url . '/' . $this->api_version . '/new_copy/' . $this->
                    auth_token . '/' . $id;
                break;
        }

        return $url;
    }

    /**
     * 
     * Uploads the file to the specified folder. You can set the parent_id 
     * attribute on the file for this to work. Because of how the API currently 
     * works, be careful!! If you upload a file for the first time, but a file 
     * of that name already exists in that location, this will automatically 
     * overwrite it.
     * 
     * If you use this method of file uploading, be warned! The file will bounce! 
     * This means that the file will FIRST be uploaded to your servers and then 
     * it will be uploaded to Box. If you want to bypass your server, call the 
     * "upload_url" method instead.
     * 
     * @param Box_Client_File $file
     * @param array $params A list of valid input params can be found at the Download
     * 											and upload method list at http://developers.box.net
     * @param bool $upload_then_delete If set, it will delete the file if the upload was successful
     * 
     */
    public function upload(Box_Client_File & $file, array $params = array(), $upload_then_delete = false)
    {
        if (array_key_exists('new_copy', $params) && $params['new_copy'] && intval($file->
            attr('id')) !== 0) {
            // This is a valid file for new copy, we can new_copy
            $url = $this->upload_url('new_copy', $file->attr('id'));
        } else
            if (intval($file->attr('file_id')) !== 0 && !$new_copy) {
                // This file is overwriting another
                $url = $this->upload_url('overwrite', $file->attr('id'));
            } else {
                // This file is a new upload
                $url = $this->upload_url('upload', $file->attr('folder_id'));
            }

            // assign a file name during construction OR by setting $file->attr('filename');
            // manually
            $split = explode(DIRECTORY_SEPARATOR, $file->attr('localpath'));
        $split[count($split) - 1] = $file->attr('filename');
        $new_localpath = implode('\\', $split);

        // only rename if the old filename and the new filename are different
        if ($file->attr('localpath') != $new_localpath) {
            if (!rename($file->attr('localpath'), $new_localpath)) {
                throw new Box_Rest_Client_Exception('Uploaded file could not be renamed.');
            } else {
                $file->attr('localpath', $new_localpath);
            }
        }

        $params['file'] = '@' . $file->attr('localpath');

        $res = Rest_Client::post($url, $params, array());

        // This exists because the API returns malformed xml.. as soon as the API
        // is fixed it will automatically check against the parsed XML instead of
        // the string. When that happens, there will be a minor update to the library.
        $failed_codes = array(
            'wrong auth token',
            'application_restricted',
            'upload_some_files_failed',
            'not_enough_free_space',
            'filesize_limit_exceeded',
            'access_denied',
            'upload_wrong_folder_id',
            'upload_invalid_file_name');

        if (in_array($res, $failed_codes)) {
            return $res;
        } else {
            $res = $this->parse_result($res);
        }
        // only import if the status was successful
        if ($res['status'] == 'upload_ok') {
            $file->import($res['files']['file']);

            // only delete if the upload was successful and the developer requested it
            if ($upload_then_delete) {
                unlink($file->attr('localpath'));
            }
        }

        return $res['status'];
    }


}


/**********************************************************************************
***********************************************************************************
***********************************************************************************/
/**
 * 
 * Instead of returning a giant array of things for you to deal with, I've pushed 
 * the array into two classes. The results are either a folder or a file, and each 
 * has its own class. 
 * 
 * The Box_Client_Folder class will contain an array of files, but will also have 
 * its own attributes. In addition. I've provided a series of CRUD operations that 
 * can be performed on a folder.
 * @author Angelo R
 *
 */
class Box_Client_Folder
{

    private $attr;

    public $file;
    public $folder;

    public function __construct()
    {
        $this->attr = array();
        $this->file = array();
        $this->folder = array();

    }

    /**
     * 
     * Acts as a getter and setter for various attributes. You should know the name 
     * of the attribute that you are trying to access.
     * @param string $key
     * @param mixed $value
     */
    public function attr($key, $value = '')
    {
        if (empty($value) && array_key_exists($key, $this->attr)) {
            return $this->attr[$key];
        } else {
            $this->attr[$key] = $value;
        }
    }

    /**
     * 
     * Imports the tree structure and allows us to provide some extended functionality 
     * at some point. Don't run import manually. It expects certain things that are 
     * delivered through the API. Instead, if you need a tree structure of something, 
     * simply call Box_Rest_Client->folder(folder_id); and it will automatically return 
     * the right stuff.
     * 
     * Due to an inconsistency with the Box.net ReST API, this section invovles a few 
     * more checks than normal to ensure that all the necessary values are available 
     * when doing the import.
     * @param array $tree
     */
    public function import_api1(array $tree)
    {
        foreach ($tree['@attributes'] as $key => $val) {
            $this->attr[$key] = $val;
        }

        if (array_key_exists('folders', $tree)) {
            if (array_key_exists('folder', $tree['folders'])) {
                if (array_key_exists('@attributes', $tree['folders']['folder'])) {
                    // this is the case when there is a single folder within the root
                    $box_folder = new Box_Client_Folder;
                    $box_folder->import($tree['folders']['folder']);
                    $this->folder[] = $box_folder;
                } else {
                    // this is the case when there are multiple folders within the root
                    foreach ($tree['folders']['folder'] as $i => $folder) {
                        $box_folder = new Box_Client_Folder;
                        $box_folder->import($folder);
                        $this->folder[] = $box_folder;
                    }
                }
            }
        }

        if (array_key_exists('files', $tree)) {
            if (array_key_exists('file', $tree['files'])) {
                if (array_key_exists('@attributes', $tree['files']['file'])) {
                    // this is the case when there is a single file within a directory
                    $box_file = new Box_Client_File;
                    $box_file->import($tree['files']['file']);
                    $this->file[] = $box_file;
                } else {
                    // this is the case when there are multiple files in a directory
                    foreach ($tree['files']['file'] as $i => $file) {
                        $box_file = new Box_Client_File;
                        $box_file->import($file);
                        $this->file[] = $box_file;
                    }
                }
            }
        }
    }


    //in API 2, the response has different structure than API1
    public function import(array $tree)
    {
        foreach ($tree as $key => $val) {
            if ($key != 'item_collection')
                $this->attr[$key] = $val;
        }

        if (array_key_exists('item_collection', $tree)) {
            $item_collection = $tree['item_collection']['entries'];
            //Good to know: there is another property for $tree['item_collection'], that is 'total_count'

            foreach ($item_collection as $key => $val) {
                if ($val['type'] == 'folder') {
                    $box_folder = new Box_Client_Folder;
                    $box_folder->import($val);
                    $this->folder[] = $box_folder;
                } else
                    if ($val['type'] == 'file') {
                        $box_file = new Box_Client_File;
                        $box_file->import($val);
                        $this->file[] = $box_file;
                    }
            } //foreach entry in the collections
        } //if this tree has sub-folders/files
    }


    //tabulate the folder's details + content and display in <table> html tags
    //cautious if this returns a long list, longer than the string limit
    public function print_to_table()
    {
        $result = '<table border="1">';
        $result .= '<caption>This folder details</caption>';
        $result .= '<tr>';
        foreach ($this->attr as $key => $val)
            if (!is_array($val))
                $result .= '<td>' . $key . '=>' . $val . '</td>';
        $result .= '</tr></table>';

        $result .= '<table border="1">';
        $result .= '<caption>All the sub-folders</caption>';
        foreach ($this->folder as $key => $val) {
            $result .= '<tr>';
            foreach ($val->attr as $folderk => $folderv)
                $result .= '<td>' . $folderk . '=>' . $folderv . '</td>';
            $result .= '</tr>';
        }
        $result .= '</table>';

        $result .= '<table border="1">';
        $result .= '<caption>All the files</caption>';
        foreach ($this->file as $key => $val)
            $result .= $val->print_to_row();
        $result .= '</table>';

        return $result;
    }
}

/**
 * 
 * Instead of returning a giant array of things for you to deal with, I've pushed 
 * the array into two classes. The results are either a folder or a file, and each 
 * has its own class. 
 * 
 * The Box_Client_File class will contain the attributes and tags that belong 
 * to a single file. In addition, I've provided a series of CRUD operations that can 
 * be performed on a file.
 * @author Angelo R
 *
 */
class Box_Client_File
{

    private $attr;
    private $tags;

    /**
     * During construction, you can specify a path to a file and a file name. This 
     * will prep the Box_Client_File instance for an upload. If you do not wish 
     * to upload a file, simply instantiate this class without any attributes. 
     * 
     * If you want to fill this class with the details of a specific file, then 
     * get_file_info and it will be imported into its own Box_Client_File class.
     * 
     * @param string $path_to_file
     * @param string $file_name
     */
    public function __construct($path_to_file = '', $file_name = '')
    {
        $this->attr = array();
        if (!empty($path_to_file)) {
            $this->attr('localpath', $path_to_file);

            if (!empty($file_name)) {
                $this->attr('filename', $file_name);
            }
        }
    }

    /**
     * Imports the file attributes and tags. At some point we can add further 
     * methods to make this a little more useful (a json method perhaps?)
     * @param array $file
     */
    public function import_api1(array $file)
    {
        foreach ($file['@attributes'] as $key => $val) {
            $this->attr[$key] = $val;
        }

        if (array_key_exists('tags', $file)) {
            foreach ($file['tags'] as $i => $tag) {
                $tags[$i] = $tag;
            }
        }
    }

    public function import(array $file)
    {
        foreach ($file as $key => $val) {
            if (is_array($val) && array_key_exists('type', $val)) {
                $obj = new Box_Rest_Client_Object($val['type']);
                $obj->import($val);
                $this->attr[$key] = $obj;
            } else {
                if ($key == 'type' && empty($this->type))
                    $this->type = $val;
                $this->attr[$key] = $val;
            }
        }
    }

    /**
     * Gets or sets file attributes. For a complete list of attributes please 
     * check the info object (get_file_info)
     * @param string $key
     * @param mixed $value
     */
    public function attr($key, $value = '')
    {
        if (empty($value) && array_key_exists($key, $this->attr)) {
            return $this->attr[$key];
        } else {
            $this->attr[$key] = $value;
        }
    }

    //display the file details in html <tr>,<td> tags
    public function print_to_row()
    {
        $result = '<tr>';
        foreach ($this->attr as $key => $val)
            if (!is_array($val))
                $result .= '<td>' . $key . '=>' . $val . '</td>';
        $result .= '</tr>';
        return $result;
    }

    public function tag()
    {

    }

    /**
     * The download link to a particular file. You will need to manually pass in
     * the authentication token for the download link to work.
     *
     * @param Box_Rest_Client $box_net A reference to the client library.
     * @param int $version The version number of the file to download, leave blank
     * 											if you want to download the latest version.
     *
     * @return string $url The url link to the download
     */
    public function download_url(Box_Rest_Client $box_net, $version = 0)
    {
        $url = $box_net->base_url . '/' . $box_net->api_version;
        if ($version == 0) {
            // not a specific version download
            $url .= '/download/' . $box_net->auth_token . '/' . $this->attr('id');
        } else {
            // downloading a certain version
            $url .= '/download_version/' . $box_net->auth_token . '/' . $this->attr('id') .
                '/' . $version;
        }

        return $url;
    }
}

/* This class is defined to encapsulate all the objects possibly returned from Box.net API
* The basic details are stored in 'attr' array . These are the possible objects :
*  - Folder
*  - Files
*  - Comments
*  - Createdby/Modifiedby
*/
class Box_Rest_Client_Object
{
    private $attr;
    private $type; //this value is only to be set at construct

    public function __construct($vtype = '')
    {
        $this->attr = array();
        if (!empty($vtype))
            $this->type = $vtype;
    }

    public function import(array $object)
    {
        foreach ($object as $key => $val) {
            if (is_array($val) && array_key_exists('type', $val)) {
                $obj = new Box_Rest_Client_Object($val['type']);
                $obj->import($val);
                $this->attr[$key] = $obj;
            } else {
                if ($key == 'type' && empty($this->type))
                    $this->type = $val;
                $this->attr[$key] = $val;
            }
        }
    }

    public function attr($key, $value = '')
    {
        if (empty($value) && array_key_exists($key, $this->attr)) {
            return $this->attr[$key];
        } else {
            $this->attr[$key] = $value;
        }
    }

    public function type()
    {
        return $this->type;
    }
}

/**
 * 
 * Thrown if we encounter an error with the actual client class. This is fairly 
 * useless except it gives you a little more information about the type of error 
 * being thrown.
 * @author Angelo R
 *
 */
class Box_Rest_Client_Exception extends Exception
{

}
