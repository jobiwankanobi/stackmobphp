<?php

/**
 * Test of User class.
 * 
 * Run like this: phpunit --stderr UserTest.php
 *
 * @author jobiwankanobi
 */
namespace Stackmob;

include_once("Stackmob.php");

class UserTest extends \PHPUnit_Framework_TestCase {
    
    /**
     * @expectedException \Stackmob\StackmobException
     */
    public function testLoginFailedException() {
        Rest::$consumerKey = "a368126f-4b41-4e54-ac45-394df81fe404";
        Rest::$consumerSecret = "1dd779df-ab0e-446b-8bdb-52641ef97df4";

        $user = new User();
        $user->logIn("jimbo", "23423423423");   // login with wrong password
    }
    
    public function testSignupUserStatic() {
        $user = User::signUpUser("jimbo", "123456");
        $this->assertEquals("jimbo", $user->getUsername());
        $this->assertArrayHasKey("createddate", $user->attributes());
        $this->assertArrayHasKey("lastmoddate", $user->attributes());
        $this->assertArrayHasKey("sm_owner", $user->attributes());
    }
    
    public function testLoginUserSuccess() {
        $user = new User(array("username" => "jimbo", "password" => "123456"));
        $user->logIn();
        $this->assertArrayHasKey("lastmoddate", $user->attributes());
        $this->assertArrayHasKey("createddate", $user->attributes());
        $this->assertNotNull($_SESSION[\Stackmob\User::STACKMOB_USER_SESSION_KEY]);
    }
    
    public function testFetchUser() {
        $user = new User(array("username" => "jimbo"));
        $user->fetch();
        $this->assertArrayHasKey("createddate", $user->attributes());
        $this->assertArrayHasKey("lastmoddate", $user->attributes());
        $this->assertArrayHasKey("sm_owner", $user->attributes());
    }
    
    public function testDeleteUser() {
        $user = new User(array("username" => "jimbo"));
        $user->delete();
        try {
            $user->fetch();
        } catch(\Stackmob\StackmobException $e) {
            $this->assertEquals($e->getMessage(), "The requested URL returned error: 404");
        }
    }
    
    public function testSignupUserNonStatic() {
        $user = new User(array("username" => "jimbo", "password" => "123456"));
        $user->signUp();
        $this->assertArrayHasKey("createddate", $user->attributes());
        $this->assertArrayHasKey("lastmoddate", $user->attributes());
        $this->assertArrayHasKey("sm_owner", $user->attributes());
        $user->delete();
    }
}

?>
