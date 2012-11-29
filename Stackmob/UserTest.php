<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserTest
 *
 * @author jobrien
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
        $this->assertContains("createddate", array_keys($user->attributes()));
        $this->assertContains("lastmoddate", array_keys($user->attributes()));
        $this->assertContains("sm_owner", array_keys($user->attributes()));
    }
    
    public function testFetchUser() {
        $user = new User(array("username" => "jimbo"));
        $user->fetch();
        $this->assertContains("createddate", array_keys($user->attributes()));
        $this->assertContains("lastmoddate", array_keys($user->attributes()));
        $this->assertContains("sm_owner", array_keys($user->attributes()));
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
}

?>
