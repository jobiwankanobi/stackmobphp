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
   
   public function setUp() {
       parent::setUp();
        Rest::$consumerKey = "a368126f-4b41-4e54-ac45-394df81fe404";
        Rest::$consumerSecret = "1dd779df-ab0e-446b-8bdb-52641ef97df4";
        $this->log = \Logger::getLogger(__CLASS__);
        $user = new User(array("username" => "jimbo", "password" => "123456", "age" => 25));
        $user->signUp();
        
        // Set up test objects
        
        $object1 = new Object('Flimmy', array('flimlevel' => 5,   // TODO: camel case should convert to underscore
            'flimtackular' => false));
        $object1->save();
        
        $object2 = new Object('Flimmy', array('flimlevel' => 7, 'flimtackular' => true, 'flammy' => 'slammy'));
        $object2->save();
   }
   
   public function tearDown() {
       parent::tearDown();
       $user = new User(array("username" => "jimbo", "password" => "123456"));     
       $user->logIn();
       
       $q = new Query("Flimmy");
       $objects = $q->find();
       foreach ($objects as $object) {
           $object->delete();
       }
       
       $user->delete();
   }
   
   public function testObjectFetchWithEquals() {
       $this->log->debug("testObjectFetchWithEquals");
       $user = new User(array("username" => "jimbo", "password" => "123456"));     
       $user->logIn();
       $query = new Query('Flimmy');
       $query->isEqual('flimlevel', 7);
       
       $results = $query->find();
       $this->assertEquals(1, count($results));
       $flim = $results[0];
       $this->assertEquals(7, $flim->get('flimlevel'));
   }

   public function testObjectFetchIsNull() {
       $this->log->debug("testObjectFetchIsNull");
       $user = new User(array("username" => "jimbo", "password" => "123456"));     
       $user->logIn();
       $query = new Query('Flimmy');
       $query->isNull('flammy');
       
       $results = $query->find();
       $this->assertEquals(1, count($results));
       $flim = $results[0];
       $this->assertEquals(7, $flim->get('flimlevel'));
   }

    public function testUserFetchWithAgeEquals() {
        $this->log->debug("testUserFetchWithAgeEquals");
        $user = new User(array("username" => "jimbo", "password" => "123456"));     
        $user->logIn();
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
        $user = new User(array("username" => "jimbo", "password" => "123456"));     
        $user->logIn();
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
        $user = new User(array("username" => "jimbo", "password" => "123456"));     
        $user->logIn();
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
