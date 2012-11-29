<?php
/**
 * Sparse REST Client for Parse.com
 * @version 0.1
 */
namespace Stackmob;
include_once("Stackmob.php");
include_once("OAuth.php");

class Rest {

    public static $consumerKey;
    public static $consumerSecret;
    public static $DEVELOPMENT = true; 	// default development, false is production
    const API_URL = 'https://api.stackmob.com';
    const USER_AGENT = 'StackmobRest/0.1';
    const OBJECT_PATH_PREFIX = '';
    const PUSH_PATH = 'https://push.stackmob.com';
    const USER_PATH = 'user';
    const LOGIN_PATH = 'user/login';
    const LOGOUT_PATH = 'user/logout';

    public $timeout = 5;

    protected $_response;
    protected $_responseHeaders;
    protected $_statusCode;
    protected $_results;
    protected $_errorCode;
    protected $_error;
    protected $_count;
    protected $_oauthConsumer;
    protected $_apiUrl;
    protected $log;
    
    public function __construct($apiUrl = null) {
                $this->_apiUrl = $apiUrl ? $apiUrl : Rest::API_URL;
		$this->_oauthConsumer = new OAuthConsumer(Rest::$consumerKey, Rest::$consumerSecret, NULL);
                $this->log = \Logger::getLogger(__CLASS__);
		
	}
    // Convenience Methods for Objects, Users, Push Notifications

    // Objects /////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * GET Objects
     * @url https://developer.stackmob.com/sdks/rest/api#a-get_-_read_objects
     *
     * @param $objectClass
     * @param array $params
     * @return array
     */
    public function getObjects($objectClass,$params=array(),$selects=null,$order=null,$range=null,$depth=null){
        $path = $this->objectPath($objectClass);
        return $this->get($path,$params,$selects,$order,$range,$depth);
    }

    /**
     * GET Object
     * @url https://parse.com/docs/rest#objects-retrieving
     *
     * @param $objectClass
     * @param $objectId
     * @return array
     */
    public function getObject($objectClass,$objectId){
        $path = $this->objectPath($objectClass,$objectId);
        return $this->get($path);
    }

    /**
     * POST Object
     * @url https://parse.com/docs/rest#objects-creating
     *
     * @param $objectClass
     * @param $data
     * @return array
     */
    public function createObject($objectClass,$data){
        $path = $this->objectPath($objectClass);
        return $this->post($path,$data);
    }

    // New object: curl -svx localhost:8001 -H "Accept: application/vnd.stackmob+json; version=0" -d '{"score_id":"1", "my_score":5, "worries_score" : 6}' http://api.mob1.stackmob.com/user/jimbo/score
    // Existing object: curl -svx localhost:8001 -H "Accept: application/vnd.stackmob+json; version=0" -d '{"score_id":"5185B00C-CB03-449B-BD6D-2F466E24DC52"}' -X PUT http://api.mob1.stackmob.com/user/jimbo/score
    
    protected function relateAndCreate($objectClass, $id, $relateClass, $data) {
        $path = $this->objectPath($objectClass, $id);
        $path = "$path/$relateClass";
        return $this->post($path,$data);
    }
    
    protected function relate($objectClass, $id, $relateClass, $relateId) {
        $path = $this->objectPath($objectClass, $id);
        $path = "$path/$relateClass";
        $data = array ($relateClass . '_id' => $relateId);
        return $this->put($path, $data);
    }
    
    /**
     * PUT Object
     * @url https://developer.stackmob.com/sdks/rest/api#a-put_-_update_object
     *
     * @param $objectClass
     * @param $objectId
     * @param $data
     * @return array
     */
    public function updateObject($objectClass,$objectId,$data){
        $path = $this->objectPath($objectClass,$objectId);
        return $this->put($path,$data);
    }

    /**
     * DELETE Object
     * @url https://developer.stackmob.com/sdks/rest/api#a-delete_-_delete_object
     *
     * @param $objectClass
     * @param $objectId
     * @param $pk
     * @return array
     */
    public function deleteObject($objectClass,$pk,$objectId){
        $path = $this->deleteObjectPath($objectClass,$pk,$objectId);
        return $this->delete($path);
    }

    // Push Notifications //////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * POST a push notification
     *
     * @url https://developer.stackmob.com/sdks/rest/api#a-sending_to_a_specific_user_s_
     *
     * @param $channels - one or more "channels" to target
     * @param array $data - Dictionary with supported keys (or any arbitrary ones)
     *  - alert : the message to display
     *  - badge : an iOS-specific value that changes the badge of the application icon (number or "Increment")
     *  - sound : an iOS-specific string representing the name of a sound file in the application bundle to play.
     *  - content-available : an iOS-specific number which should be set to 1 to signal Newsstand app
     *  - action : an Android-specific string indicating that an Intent should be fired with the given action type.
     *  - title : an Android-specific string that will be used to set a title on the Android system tray notification.
     *
     * @param array $params - Additional params to pass, supported:
     *  - type : the "type" of device to target ("ios" or "android", or omit this key to target both)
     *  - push_time : Schedule delivery up to 2 weeks in future, ISO 8601 date or UNIX epoch time in seconds (UTC)
     *  - expiration_time : Schedule expiration, ISO 8601 date or UNIX epoch time in seconds (UTC)
     *  - expiration_interval : Set interval in seconds from push_time or now to expire
     *  - where : parameter that specifies the installation objects
     *
     * @return array
     */
    public function push($channels,$data,$params=array()){

        $path = Rest::PUSH_PATH;

        $params['channels'] = $channels;
        $params['data'] = $data;

        return $this->post($path,$params);
    }

    // Parse User //////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * GET Users
     * @url https://developer.stackmob.com/sdks/rest/api#a-get_-_read_objects
     * 
     * @param type $params
     * @param type $selects
     * @param type $order
     * @param type $range
     * @param type $depth
     * @return type
     */
    public function getUsers($params=array(),$selects=null,$order=null,$range=null,$depth=null){
        $path = $this->userPath();
        return $this->get($path,$params,$selects,$order,$range,$depth);
    }

    /**
     * GET User
     * @url https://developer.stackmob.com/sdks/rest/api#a-find_by_id
     *
     * @param $objectId
     * @return array
     */
    public function getUser($username,$depth){
        $path = $this->userPath($username,$depth);
        $this->log->debug( "PATH: $path");
        return $this->get($path);
    }

    /**
     * POST a new User
     *
     * @url https://developer.stackmob.com/sdks/rest/api#a-post_-_create_object
     *
     * @param $username
     * @param $password
     * @param array $additional
     *
     * @return array
     */
    public function createUser($username,$password,$additional=array()){

        $path = Rest::USER_PATH;

        $required = array('username'=>$username,'password'=>$password);
        $data = array_merge($required,$additional);

        return $this->post($path,$data);
    }

    /**
     * PUT updates for a user, user must be signed in
     * @param $objectId
     * @param $data
     * @return array
     */
    public function updateUser($objectId,$data){
        $path = $this->userPath($objectId);
        return $this->put($path,$data);
    }

    /**
     * GET User details by logging in
     *
     * @param $username
     * @param $password
     *
     * @return array
     */
    public function login($username,$password){

        $path = Rest::LOGIN_PATH;

        $data = array('username'=>$username,'password'=>$password);
        
        $qs = http_build_query($data);
        
        return $this->get($path . '?' . $qs,null);
    }
    
    public function logout($username) {
        $path = Rest::LOGOUT_PATH;
        $data = array('username' => $username);
        $qs = http_build_query($data);
        return $this->get($path . '?' . $qs,null);
    }

    /**
     * POST a request for password reset for given email
     * @param $email
     *
     * @return array
     */
    public function requestPasswordReset($email){

        $path = Rest::PASSWORD_RESET_PATH;

        return $this->post($path,array('email'=>$email));
    }

    // Getters /////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @return string - raw response from parse
     */
    public function response(){
        return $this->_response;
    }

    /**
     * @return mixed
     */
    public function results(){
        return $this->_results;
    }

    /**
     * @return string
     */
    public function statusCode(){
        return $this->_statusCode;
    }

    /**
     * @return string
     */
    public function errorCode(){
        return $this->_errorCode;
    }

    /**
     * @return string
     */
    public function error(){
        return $this->_error;
    }

    /**
     * @return mixed
     */
    public function responseHeaders(){
        return $this->_responseHeaders;
    }

    /**
     * @return int
     */
    public function count(){
        if($this->_count){
            return $this->_count;
        }elseif(is_array($this->results())){
            $this->_count = count($this->results());
        }
        return $this->_count;
    }

    /**
     * @return array
     */
    public function details(){
        return array(
            'response'=>$this->response(),
            'statusCode'=>$this->statusCode(),
            'error'=>$this->error(),
            'errorCode'=>$this->errorCode(),
            'results'=>$this->results(),
        );
    }

    // Generic Actions /////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * POST
     *
     * @param $path
     * @param $data
     * @return array
     */
    public function post($path,$data){
        return $this->request($path,'POST',$data);
    }

    /**
     * GET
     *
     * @param $path
     * @param array $data
     * @return array
     */
    public function get($path,$data=array(),$selects=null,$order=null,$range=null,$depth=null){
        $headers = array();
        if($depth)
            $data[] = array("_expand" => $depth);
        if($selects)
            $headers[]=$selects;
        if($order)
            $headers[]=$order;
        if($range)
            $headers[]=$range;
        $query = http_build_query($data);
        if($query)
            $path = "$path?$query";
        return $this->request($path,'GET',null,implode("\n", $headers));
    }

    /**
     * PUT
     *
     * @param $path
     * @param $data
     * @return array
     */
    public function put($path,$data){
        return $this->request($path,'PUT',$data);
    }

    /**
     * DELETE
     *
     * @param $path
     * @return array
     */
    public function delete($path){
        return $this->request($path,'DELETE');
    }

    // Protected/Private ///////////////////////////////////////////////////////////////////////////////////////////////

     protected function strVarDump($var) {
	ob_start();
	var_dump($var);
	$dump = ob_get_contents();
	ob_end_clean();
	return $dump;
   }
   
    function send_request($http_method, $url, $auth_header=null, $postData=null, $headers=null) {  
	$this->log->debug( "send_request:");
	$this->log->debug( print_r($http_method, true)."");
	$this->log->debug( print_r($url, true)."");
	$this->log->debug( print_r($auth_header, true)."");
        $this->log->debug("Headers: $headers");
	$this->log->debug( print_r($postData, true)."");
        $version = Rest::$DEVELOPMENT ? 0 : 1;
	
  $curl = curl_init($url);  
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
  curl_setopt($curl, CURLOPT_FAILONERROR, true);  
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  
  switch($http_method) {  
    case 'GET':  
      curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/vnd.stackmob+json;",
				'Content-Length: 0',
				"Accept: application/vnd.stackmob+json; version=$version",
                                $headers,
				$auth_header));   
      break;  
    case 'POST':
	  	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
	  		'Content-Type: application/vnd.stackmob+json;',
				'Content-Length: '.strlen(json_encode($postData)),
				"Accept: application/vnd.stackmob+json; version=$version",
                                $headers,
				$auth_header));
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);                                          
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));  
      break;  
    case 'PUT':
			curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/vnd.stackmob+json;",
				'Content-Length: '.strlen(json_encode($postData)),
				"Accept: application/vnd.stackmob+json; version=$version",
                                $headers,
				$auth_header));
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);  
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));  
			break;  
    case 'DELETE':
			curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/vnd.stackmob+json;",
				'Content-Length: 0',
				"Accept: application/vnd.stackmob+json; version=$version",
                                                            $headers, $auth_header));   
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);   
      break;  
  }  
  
  $this->log->debug( $curl."\n\n");
  
  $response = curl_exec($curl);  
  $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  $this->log->debug("Response: $response");
  $this->log->debug("Status code: $statusCode");
    if (!$response && $statusCode !== 200) {  
         $response = curl_error($curl);
         curl_close($curl);  
         
         throw new StackmobException($response, $statusCode);
    } else {


        $this->_statusCode = $statusCode;
        $this->_response = $response;
        $this->_results = null;

        $decoded = json_decode($response);

        if(is_object($decoded)){
            $this->_results = $decoded;
        }
        curl_close($curl);  

        return $this->_results;
    }
}


    /**
     * 
     * @param type $path
     * @param type $method
     * @param type $postData
     * @param type $headers
     * @return type
     */
    protected function request($path,$method,$postData=array(),$headers=null){
        $params=NULL;
        $endpoint = $this->_apiUrl.'/'.$path;
        $this->log->debug( "endpoint: " . $endpoint . "");


        // Setup OAuth request - Use NULL for OAuthToken parameter
        $request = OAuthRequest::from_consumer_and_token($this->_oauthConsumer, NULL, $method, $endpoint, $params);

        // Sign the constructed OAuth request using HMAC-SHA1 - Use NULL for OAuthToken parameter
        $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->_oauthConsumer, NULL);

        // Extract OAuth header from OAuth request object and keep it handy in a variable
        $oauth_header = $request->to_header();

        $this->log->debug( "request:".print_r($request, true)."");


        $response = $this->send_request($request->get_normalized_http_method(), $endpoint, $oauth_header, $postData, $headers);

        $this->log->debug( "response:" . print_r($response, true) . "");

        return $response;
    }

    /**
     * Helper method to concatenate paths for objects
     * @param $objectClass
     * @param null $objectId
     * @return string
     */
    protected function objectPath($objectClass,$objectId=null,$depth=null){
        $pieces = array(lc($objectClass));
        if($objectId){
            $pieces[] = $objectId;
        }
        $url = \implode('/',$pieces);
        if($depth)
            $url = "$url?_expand=$depth";
        return $url;
    }

    protected function deleteObjectPath($objectClass,$pk,$objectId) {
        return \strtolower($objectClass) . "?$pk=$objectId";
    }
    /**
     * @param null $objectId
     * @return string
     */
    protected function 
            userPath($username=null){
        $pieces = array(Rest::USER_PATH);
        if($username){
            $pieces[] = $username;
        }
        $url = \implode('/',$pieces);
        return $url;
    }

    /**
     * From User Contributed Notes: http://php.net/manual/en/function.http-parse-headers.php
     *
     * @param $header
     * @return array
     */
    protected function http_parse_headers($header) {
        $retVal = array();
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
        foreach( $fields as $field ) {
            if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
                $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
                if( isset($retVal[$match[1]]) ) {
                    $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
                } else {
                    $retVal[$match[1]] = trim($match[2]);
                }
            }
        }
        return $retVal;
    }
}