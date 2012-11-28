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
}

?>
