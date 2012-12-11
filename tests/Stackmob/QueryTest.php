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
   protected $log;
   protected $user;
   protected $objects;
   
   public function setUp() {
       parent::setUp();
        Rest::$consumerKey = "a368126f-4b41-4e54-ac45-394df81fe404";
        Rest::$consumerSecret = "1dd779df-ab0e-446b-8bdb-52641ef97df4";
        $this->log = \Logger::getLogger(__CLASS__);
        $this->user = new User(array("username" => "jimbo", "password" => "123456", "age" => 25));
        $this->user->signUp();
        
        // Set up test objects
        $this->objects = array();
        $object1 = new Object('Flimmy', array('flimLevel' => 5,   // TODO: camel case should convert to underscore
            'flimtackular' => false, 'flammy' => 'blammy'));
        $object1->save();
        $this->objects[] = $object1;
        
        $object2 = new Object('Flimmy', array('flimLevel' => 7, 'flimtackular' => true, 'flammy' => 'slammy'));
        $object2->save();
        $this->objects[] = $object1;
        
        
   }
   
   public function tearDown() {
       parent::tearDown();
       $this->user->delete();
       foreach($this->objects as $object)
           $object->delete();
   }
   
   public function testObjectFetchWithEquals() {
       $this->log->debug("testObjectFetchWithEquals");
       $query = new Query('Flimmy');
       $query->isEqual('flimLevel', 7);
       
       $results = $query->find();
       $this->assertEquals(1, count($results));
       $flim = $results[0];
       $this->assertEquals('flimLevel', $flim->get('flimLevel'));
   }

    public function testUserFetchWithAgeEquals() {
        $this->log->debug("testUserFetchWithAgeEquals");
        $query = new Query('User');
        $query->isEqual('age', 25);
        $results = $query->find();
        $this->assertEquals(1, count($results));
        $user = $results[0];
        
        $this->assertEquals("jimbo", $user->getUsername());
        $this->assertArrayHasKey("createddate", $user->attributes());
        $this->assertArrayHasKey("lastmoddate", $user->attributes());
        $this->assertArrayHasKey("sm_owner", $user->attributes());
        $this->assertEquals("25", $user->get("age"));
    }
    
    public function testUserFetchWithAgeLessThan() {
        $this->log->debug("testUserFetchWithAgeLessThan");
        $query = new Query('User');
        $query->lessThan('age', 26);
        $results = $query->find();
        $this->assertEquals(1, count($results));
        $user = $results[0];
        
        $this->assertEquals("jimbo", $user->getUsername());
        $this->assertArrayHasKey("createddate", $user->attributes());
        $this->assertArrayHasKey("lastmoddate", $user->attributes());
        $this->assertArrayHasKey("sm_owner", $user->attributes());
        $this->assertEquals("25", $user->get("age"));
        
    }

        public function testUserFetchWithAgeGreaterThan() {
        $this->log->debug("testUserFetchWithAgeLessThan");
        $query = new Query('User');
        $query->greaterThan('age', 20);
        $results = $query->find();
        $this->assertEquals(1, count($results));
        $user = $results[0];
        
        $this->assertEquals("jimbo", $user->getUsername());
        $this->assertArrayHasKey("createddate", $user->attributes());
        $this->assertArrayHasKey("lastmoddate", $user->attributes());
        $this->assertArrayHasKey("sm_owner", $user->attributes());
        $this->assertEquals("25", $user->get("age"));
        
    }

}

?>
