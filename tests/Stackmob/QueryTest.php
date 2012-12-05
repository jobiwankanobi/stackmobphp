<?php

/**
 * Test of Query class.
 * 
 * Run like this: phpunit --stderr QueryTest.php
 *
 * @author jobiwankanobi
 */
namespace Stackmob;

include_once(dirname(__FILE__) . "/../../src/Stackmob/Stackmob.php");

class QueryTest extends \PHPUnit_Framework_TestCase {
    
    public function testUserFetchWithAge() {
        Rest::$consumerKey = "a368126f-4b41-4e54-ac45-394df81fe404";
        Rest::$consumerSecret = "1dd779df-ab0e-446b-8bdb-52641ef97df4";
       
        $query = new Query('User');
        $query->isEqual('age', 25);
        $query->find();
    }
    
}

?>
