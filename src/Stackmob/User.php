<?php
/**
 * User
 */
namespace Stackmob;

use Stackmob\User;
use Stackmob\Rest;

class User extends Object {

    // Class ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    const SM_LOGGED_IN_USER = "sm_logged_in_user";
    const SM_LOGGED_IN_USERNAME = "sm_logged_in_username";
    /**
     * @var User
     */
    private static $_current;

    /**
     * @return User
     */
    public static function current(){

        if(User::$_current){
            return User::$_current;
        }else{
            session_start();
            if(!empty($_SESSION[User::SM_LOGGED_IN_USER])){
                $attributes = json_decode($_SESSION[User::SM_LOGGED_IN_USER]);
                User::$_current = new User($attributes);
                return User::$_current;
            }else{
                session_destroy();
            }
        }

        return null;
    }

    /**
     * Requests a password reset email to be sent to the specified email address associated with the user account.
     */
    public static function requestPasswordReset($email){

        Object::$_restClient->requestPasswordReset($email);
    }

    /**
     * https://developer.stackmob.com/sdks/rest/api#a-post_-_create_object
     *
     * @param $username
     * @param $password
     * @param array $attributes
     * @return null|\Sparse\User
     */
    public static function signUpUser($username, $password, $attributes=array()){
        if(!Object::$_restClient){
            Object::$_restClient = new Rest();
        }

        $user = null;
        $created = Object::$_restClient->createUser($username,$password,$attributes);


        if(Object::$_restClient->statusCode() == 201){

            $user = new User((array)$created);
        }

        return $user;
    }



    // Instance ////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Required User parameters
     * @var array
     */
    protected $smRequiredAttributes = array('username','password');

    /**
     * Creates a new User model
     * Note: Example of an Parse.Object subclass Constructor
     * @param array $attributes
     */
    public function __construct($attributes=array()){

        parent::__construct('User',$attributes, 'username');
    }

    // API /////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Checks whether this user is the current user and has been authenticated.
     */
    public function authenticated(){
        $session = isset($_SESSION[User::STACKMOB_LOGGED_IN_COOKIE]);
        return ($this->isCurrent() && $session);
    }

    /**
     * Returns get("email").
     */
    public function getEmail(){
        return $this->get("email");
    }

    /**
     * Returns get("username").
     */
    public function getUsername(){
        return $this->get("username");
    }

    /**
     * Returns true if current would return this user.
     * @return boolean
     */
    public function isCurrent(){
        return ($this == User::current());
    }

    /**
     * Logs in a \Stackmob\User, retrieves that user,
     * and puts in session.
     *
     * @param $username
     * @param $password
     * @return boolean
     * @throws \Stackmob\StackmobException
     *
     */
    public function logIn($username = null, $password = null){
        $this->log->debug("Logging in....");
        if(session_status() == PHP_SESSION_ACTIVE) {
            $this->log->debug("LOGIN - Destroying session....");
            session_destroy();
        } else {
            $this->log->debug("No session variable...");
        }
        $username = $username ? $username : $this->getUsername();
        $password = $password ? $password : $this->get('password');
//        $this->log->debug("Logging in user $username...");

        if($username && $password){
            try {
                $user = Object::$_restClient->login($username,$password);
            } catch (\Stackmob\StackmobException $e) {
                throw($e);
            }
            if(Object::$_restClient->statusCode() == 200 && $user){
                $this->clearDirtyKeys();
                $this->updateAttributes((array)$user);
                $this->unsetAttr('password');

                User::$_current = $this;
                $ret = true;
            }
        }

        return $ret;
    }

    /**
     *
     * @return boolean
     * @throws \Stackmob\StackmobException
     */
    public function logout() {
        $ret = false;
        if(User::$_current){
            Object::$_restClient->logout($this->getUsername());
            User::$_current = null;
            session_destroy();
            $ret = true;
        }
        return $ret;
    }


    /**
     * Calls set("email", $email)
     * @param $email
     */
    public function setEmail($email){
        $this->set("email", $email);
    }

    /**
     * Calls set("password", $password)
     * @param $password
     */
    public function setPassword($password){
        $this->set("password", $password);
    }

    /**
     * Calls set("username", $username)
     * @param $username
     */
    public function setUsername($username){
        $this->set("username", $username);
    }

    /**
     * Signs up a new user.
     * https://developer.stackmob.com/sdks/rest/api#a-post_-_create_object
     * @param array $attributes
     */
    public function signUp($attributes=array()){

        $this->attributes($this->mergeAttributes($attributes));

        $username = $this->getUsername();
        $password = $this->get('password');
        $additional = $this->additionalAttributes();

        if($username && $password){

            $created = Object::$_restClient->createUser($username,$password,$additional);

            if(Object::$_restClient->statusCode() == 201){
                $this->clearDirtyKeys();
                $this->updateAttributes((array)$created);
            }
        }
    }

    /**
     * Filter out required fields
     * @return array
     */
    protected function additionalAttributes(){
        $attributes = array();
        foreach($this->_attributes as $k=>$v){
            if(!in_array($k,$this->smSuppliedAttributes)){
                $attributes[$k] = $v;
            }
        }
        return $attributes;
    }
}