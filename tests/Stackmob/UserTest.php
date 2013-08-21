<?php

/**
 * Test of User class.
 * 
 * Run like this: phpunit --stderr UserTest.php
 *
 * @author jobiwankanobi
 */
namespace Stackmob;

include_once(dirname(__FILE__) . "/../../src/Stackmob/Stackmob.php");

class UserTest extends \PHPUnit_Framework_TestCase {
   
   protected $log;

   public function setUp() {
       parent::setUp();
        Rest::$consumerKey = "a368126f-4b41-4e54-ac45-394df81fe404";
        Rest::$consumerSecret = "1dd779df-ab0e-446b-8bdb-52641ef97df4";
        $this->log = new DummyLogger();
        $this->log->debug("TEST: PHPVERSION" . PHP_VERSION);
   }
   
   
   public function testSignupUserStatic() {
       $this->log->debug("testSignupUserStatic");
        $user = User::signUpUser("jimbo", "123456");
        $user->set("age", 25);
        $this->assertEquals("jimbo", $user->getUsername());
        $this->assertArrayHasKey("createddate", $user->attributes());
        $this->assertArrayHasKey("lastmoddate", $user->attributes());
        $this->assertArrayHasKey("sm_owner", $user->attributes());
    }

     public function testLoginUserSuccess() {
       $this->log->debug("testLoginUserSuccess");
        $user = new User(array("username" => "jimbo", "password" => "123456"));
        $user->logIn();
        $this->assertArrayHasKey("lastmoddate", $user->attributes());
        $this->assertArrayHasKey("createddate", $user->attributes());
        session_destroy();
    }  

    public function testLoginCreateObjectOwner() {
         $this->log->debug("testLoginCreateObjectOwner");
        $user = new User(array("username" => "jimbo", "password" => "123456"));
        $user->logIn();
        
        $flimmy = new Object("Flimmy", array("flimlevel" => 5));
        $flimmy->save();
        $this->assertEquals("user/jimbo", $flimmy->get('sm_owner'));
        $flimmy->delete();
    }

    
    public function testDeleteUser() {
         $this->log->debug("testDeleteUser");
        $user = new User(array("username" => "jimbo"));
        $user->delete();
        try {
            $user->fetch();
        } catch(\Stackmob\StackmobException $e) {
            $this->assertEquals('The requested URL returned error: 404 Not Found', $e->getMessage());
        }
    }
    
    /**
     * @expectedException \Stackmob\StackmobException
     */
    public function testLoginFailedException() {
         $this->log->debug("testLoginFailedException");

        $user = new User();
        $user->logIn("jimbo", "23423423423");   
    }


    
    public function testSignupUserNonStatic() {
        $this->log->debug("testSignupUserNonStatic");
        $user = new User(array("username" => "jimbo", "password" => "123456"));
        $user->signUp();
        $this->assertArrayHasKey("createddate", $user->attributes());
        $this->assertArrayHasKey("lastmoddate", $user->attributes());
        $this->assertArrayHasKey("sm_owner", $user->attributes());
    }
    
   public function testFetchUser() {
            $this->log->debug("testFetchUser");
         $user = new User(array("username" => "jimbo"));
        $user->fetch();
        $this->assertArrayHasKey("createddate", $user->attributes());
        $this->assertArrayHasKey("lastmoddate", $user->attributes());
        $this->assertArrayHasKey("sm_owner", $user->attributes());
        $user->delete();    // clean up
    }

}

?>
