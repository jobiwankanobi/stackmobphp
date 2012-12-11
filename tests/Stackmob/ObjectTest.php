<?php

/**
 * Test of Object class methods
 *
 * Run like this: phpunit --stderr ObjectTest.php
 * 
 * @author jobiwankanobi
 */

namespace Stackmob;



class ObjectTest extends \PHPUnit_Framework_TestCase {
   protected $log;
   protected $objects;
   
   public function setUp() {
      parent::setUp();
      Rest::$consumerKey = "a368126f-4b41-4e54-ac45-394df81fe404";
      Rest::$consumerSecret = "1dd779df-ab0e-446b-8bdb-52641ef97df4";
      $this->log = \Logger::getLogger(__CLASS__);

      // Set up test objects
      $this->objects = array();
      $object1 = new Object('Flimmy', array('flimLevel' => 5,   // TODO: camel case should convert to underscore
              'flimtackular' => false, 'flammy' => 'blammy'));
      $object1->save();
      $this->objects[] = $object1;

      $object2 = new Object('Flimmy', 
              array('flimLevel' => 7, 'flimtackular' => true, 'flammy' => 'slammy'));
      $object2->save();
      $this->objects[] = $object1;


   }

   public function tearDown() {
       parent::tearDown();
       $this->user->delete();
       foreach($this->objects as $object)
           $object->delete();
   }
}

?>
