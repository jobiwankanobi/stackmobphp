# Stackmob

Simple synchronous php library for [stackmob.com](http://stackmob.com).

I need this for a stackmob project, and have only begun to change the basic
functionality to work with stackmob instead of parse.  Feel free to contribute
if you are in need of the other pieces of the puzzle like "Push notifications",
etc.  I will update the documentation when this project is farther along.

Based on the project this is forked from: https://github.com/spacious/sparse

Modeled after the [Parse Javascript SDK API](https://parse.com/docs/js/).

Uses the REST API over curl.

Requires PHP 5.3+ (uses [namespaces](http://www.php.net/manual/en/language.namespaces.php))

The ultimate purpose of this project is to make it easier to work with a more consistent API between JS/PHP.

This project is currently in early stages, so it's dirty and may have a few inconsistencies.

Features are implemented as needed, fork it and help out!

_Mostly complete_*, exceptions noted below.

*Supports basic Object, Query, User, Push and Cloud functions.

__Not currently implemented:__

* Op
* Collections
* GeoPoint (Locations)
* Roles
* Facebook Users
* Push messages

## Setup

The only setup is to give the rest client class (Rest.php) your parse credentials:

This can be done anywhere or for portability just in Stackmob.php like so:

````php
    // Your credentials:
    Rest::$consumerKey = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890";
    Rest::$consumerSecret = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890";
    Rest::$VERSION = 0;	// replace or override with 1 for production
````
If done outside a Stackmob namespaced file, you'll need the namespace:

````php
    // Your credentials:
    \Stackmob\Rest::$consumerKey = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890";
    \Stackmob\Rest::$restAPIKey = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890";
    \Stackmob\Rest::$VERSION = 0;	// replace or override with 1 for production
````

## Simple usage

The simplest way to use the library is just use Rest.php. It provides methods to get, post and put using the Stackmob REST
API. (https://developer.stackmob.com/sdks/rest/api#a-rest_docs) Shortcut methods are also provided for Objects, Users, etc.

The remaining classes are meant to provide a more declaritive way to work with Objects and Queries.

We'll try to also mimic stackmob's documentation structure and note APIs which have not been implemented.

## Objects

### Stackmob\\Object

With Stackmob it's usually easier to just use Object directly without subclassing it, like:

````php
    $gameScore = new Stackmob\Object('GameScore');
    // or create with data (associative array of attributes)
    $data = array('score'=>1337,'playerName'=>"Sean Plott",'cheatMode'=>false);
    $gameScore = new Stackmob\Object('GameScore', $data);
````

If you use the object a lot or need custom methods you can extend as so:

````php
    class Monster extends Stackmob\Object {

        public function __construct($attributes=array()){

            parent::__construct('Monster',$attributes);
        }

        public function hasSuperHumanStrength() {
            return $this->get("strength") > 18;
        }

        public static function spawn($strength){

            // many ways to do this
            $monster = new Monster();
            $monster->set('strength',$strength); // or $monster->strength = $strength;

            // or just simply:
            //$monster = new Monster(array('strength'=>$strength));

            return $monster;
        }
    }
````

Stackmob\\User is also an example of an Object subclass.

### Saving Objects

````php
    $gameScore = new Stackmob\Object('GameScore');
    $gameScore->set("score", 1337);
    $gameScore->set("playerName", "Sean Plott");
    $gameScore->set("cheatMode", false);
    // Note Stackmob object getters/setters are overloaded so this is also possible:
    //$gameScore->score = 1337;
    //$gameScore->playerName = "Sean Plott";
    //$gameScore->cheatMode = false;

    $gameScore->save();

    // this is a synchronous call so you can check the save was successful with:
    // Note id() is the same has objectId or get('objectId'), it exists to mimic the JS API
    if($gameScore->id()){

        echo('GameScore saved.'."<br />");

    }else{

        die('Could not save GameScore.'."<br />");
    }
````

Your object will now have the following properties:

````php
    echo('objectId: '.$gameScore->id()."<br />");
    echo('createdAt: '.$gameScore->createdate."<br />");
````

Just like the JS API, you can set your properties on save() as well:

````php
    $gameScore = new Stackmob\Object('GameScore');
    $data = array('score'=>1337,'playerName'=>"Sean Plott",'cheatMode'=>false);
    $gameScore->save($data);
````

### Retrieving Objects

````php
    // Using a Query

    $query = new \Stackmob\Query('GameScore');
    $gameScore = $query->get('fc0Gy7fdf1');

    if($gameScore){
        echo('<pre>');
        print_r($gameScore);
        echo('</pre>');

        // Your object will have the following properties:
        echo('objectId: '.$gameScore->id()."<br />");
        echo('createdAt: '.$gameScore->createdate."<br />");
        echo('updatedAt: '.$gameScore->updateddate."<br />");
    }

    // Using fetch:

    $gameScore = new Stackmob\Object('GameScore');
    $gameScore->id('fc0Gy7fdf1');
    $gameScore->fetch();

    // Your object will have the following properties:
    echo('objectId: '.$gameScore->id()."<br />");
    echo('createdAt: '.$gameScore->createdate."<br />");
    echo('updatedAt: '.$gameScore->updateddate."<br />");
````

### Updating Objects

````php
    $gameScore = new Stackmob\Object('GameScore');
    $gameScore->set("score", 1337);
    $gameScore->set("playerName", "Sean Plott");
    $gameScore->set("cheatMode", false);
    $gameScore->set("skills", array('pwnage','flying'));

    $gameScore->save();

    if($gameScore->id()){

        $gameScore->set("score", 1338);
        $gameScore->set("cheatMode", true);
        $gameScore->save();
    }
````

#### Counters
Not yet implemented

````php
    $gameScore->increment('score');
    $gameScore->save();
````

#### Arrays
Not yet implemented

````php
    $gameScore->addUnique("skills", "flying");
    $gameScore->add("skills", "kungfu");
    $gameScore->remove("skills",'pwnage');
    $gameScore->save();
````

### Destroying Objects
Not yet implemented

````php
    $gameScore->destroy();

    if(!$gameScore->id()){
        echo('GameScore deleted.'."<br />");
    }
````

Delete a single field...
Not yet implemented

__NOTE:__ Changed from unset() to unsetAttr() (unset is reserved in PHP)

````php
    $gameScore->unsetAttr('playerName');
    $gameScore->save();
````

### Relational Data
Partially implemented - php  code is different.
Will update...

````php
    $post = new \Stackmob\Object('Post');
    $comment = new \Stackmob\Object('Comment');

    $post->title = "I'm Hungry";
    $post->content = "Where should we go for lunch?";
    $post->save();

    $comment->comment = "Let's do Sushirrito.";
    $comment->set('parent',$post);
    $comment->save();

    // or with id:

    $post = new \Stackmob\Object('Post');
    $post->id = '0HETDIVfuq';
    $comment->set('parent',$post);*/

    // Fetching related data

    $comment = new \Stackmob\Object('Comment');
    $comment->id('T0z3RIgdqD');
    $comment->fetch();

    $post = $comment->parent->fetch();
````

__NOTE:__ Parse Relations are NOT yet implemented

### Objects method name differences from JS API:

Name changes due to reserved words in PHP:

* unset() to unsetAttr()
* clone() to cloneObject()

### Objects methods NOT yet implemented:

These are stubbed in but are no-ops:

* changedAttributes
* escape
* existed
* isValid
* op
* previous
* previousAttributes
* relation
* setACL

### Extra Objects methods provided by Stackmob:

Extra public API provided to API:

Get/Set all the attributes of an Object:

__NOTE:__ Setting all the attributes directly does not set any as 'dirty'.

````php
    $gameScore = new Stackmob\Object('GameScore');
    $data = array('score'=>1337,'playerName'=>"Sean Plott",'cheatMode'=>false);
    $gameScore->attributes($data);

    $data = $gameScore->attributes();
````

Get the class name of the Object:

````php
    $gameScore->className();
````

Get/Set objectId:

````php
    $objectId = $gameScore->id();
    $gameScore->id('fc0Gy7fdf1');
````

Create a 'pointer' representation of this Object:

````php
    $gameScore->pointer();
````

## Queries

### Stackmob\\Query

### Basic Queries

Queries are simpler in stackmob and based on object,
so I don't have a separate class.
Will update documentation.


````php
    $query = new \Stackmob\Query('GameScore');
    $query->equalTo('playerName','Dan Stemkoski');
    $gameScores = $query->find();

    if($gameScores){

        echo("Successfully retrieved ".count($gameScores)." scores."."<br />");
    }
````

### Query constraints

````php
    $query->notEqualTo('playerName','Michael Yabuti');
    $query->greaterThan("playerAge", 18);

    // Limit
    $query->limit(10);

    // First
    $query = new \Stackmob\Query('GameScore');
    $query->equalTo("playerEmail", "dstemkoski@example.com");
    $gameScore = $query->first();

    if($gameScore){

        echo("Successfully retrieved the object: ".$gameScore->id."<br />");
    }

    // skip the first 10 result
    $query->skip(10);

    // Sorts the results in ascending order by the score field
    $query->ascending("score");

    // Sorts the results in descending order by the score field
    $query->descending("score");

    // Restricts to wins < 50
    $query->lessThan("wins", 50);

    // Restricts to wins <= 50
    $query->lessThanOrEqualTo("wins", 50);

    // Restricts to wins > 50
    $query->greaterThan("wins", 50);

    // Restricts to wins >= 50
    $query->greaterThanOrEqualTo("wins", 50);

    // Finds scores from any of Jonathan, Dario, or Shawn
    $query->containedIn("playerName", array("Jonathan Walsh", "Dario Wunsch", "Shawn Simon"));

    // Finds scores from anyone who is neither Jonathan, Dario, nor Shawn
    $query->notContainedIn("playerName", array("Jonathan Walsh", "Dario Wunsch", "Shawn Simon"));

    // Finds objects that have the score set
    $query->exists("score");

    // Finds objects that don't have the score set
    $query->doesNotExist("score");
````

### Query constraints NOT yet implemented

* matchesKeyInQuery
* doesNotMatchKeyInQuery
* doesNotMatchQuery

### Queries on Array Values

__NOTE:__ Not tested!

````php
    $query->equalTo("arrayKey", 2);
````

### Queries on String Values

````php
    $query = new \Stackmob\Query("BarbecueSauce");
    $query->matches("name", '/^[A-Z][0-9]/');
    $sauces = $query->find();

    // PCRE modifiers
    $query = new \Stackmob\Query("BarbecueSauce");
    $query->matches("description", "bbq", "im");
    $sauces = $query->find();

    $query = new \Stackmob\Query("BarbecueSauce");
    $query->contains("name", "Extra Spicy!");

    $query = new \Stackmob\Query("BarbecueSauce");
    $query->startsWith("name", "Big Daddy's");

    $query = new \Stackmob\Query("BarbecueSauce");
    $query->endsWith("Original Recipe");
````

### Relational Queries

````php
    // equal to existing object:
    $post = new \Stackmob\Object('Post');
    $post->id = '0HETDIVfuq';
    $query = new \Stackmob\Query('Comment');
    $query->equalTo("parent", $post);
    $comments = $query->find();

    // equal to result of a query:
    $innerQuery = new \Stackmob\Query('Post');
    $innerQuery->exists("image");
    $query = new \Stackmob\Query('Comment');
    $query->matchesQuery("post", $innerQuery);
    $comments = $query->find();

    // include related objects:
    $query = new \Stackmob\Query('Comment');

    // Retrieve the most recent ones
    $query->descending("createdAt");

    // Only retrieve the last ten
    $query->limit(10);

    // Include the post data with each comment
    // Changed from include()
    $query->includeObject("parent");
    // using dot-notation
    //$query->includeObject("parent.author");

    $comments = $query->find();

    if($comments){
        foreach($comments as $comment){
            $post = $comment->parent;
        }
    }
````

### Counting Objects

````php
    $query = new \Stackmob\Query('GameScore');
    $query->equalTo('playerName',"Sean Plott");
    $count = $query->count();

    echo("Sean has played " . $count . " games");
````

### Compound Queries

````php
    $lotsOfWins = new \Stackmob\Query("Player");
    $lotsOfWins->greaterThan("wins",150);

    $fewWins = new \Stackmob\Query("Player");
    $fewWins->lessThan("wins",5);

    $mainQuery = \Stackmob\Query::queryWithOrQueries(array($lotsOfWins,$fewWins));
    $results = $mainQuery->find();
````

### Query method name differences from JS API:

Name changes due to reserved words in PHP:

* include() changed to includeObject()
* or() changed to queryWithOrQueries();

### Query methods NOT yet implemented:

These are stubbed in but are no-ops:

* collection
* doesNotMatchQuery
* matchesKeyInQuery
* near
* toJSON
* withinGeoBox
* withinKilometers
* withinMiles
* withinRadians

### Extra Objects methods provided by Stackmob:

Sets the seldom used 'arrayKey'

````php
    $query->arrayKey($n);
````

Get the class name of the Query:

````php
    $query->className();
````

Add 'or' queries

````php
    $query->matchesOrQueries($queries);
````

## Collections

__NOTE:__ Collections NOT yet implemented

## Users

### Signing up

````php
    $user = new Stackmob\User;
    $user->set("username", "my name");
    $user->set("password", "my pass");
    $user->set("email", "email@example.com");

    // other fields can be set just like with Parse.Object
    $user->set("phone", "415-392-0202");

    user->signUp();

    // or

    $user = Stackmob\User::signUpUser("my name","my pass",array('email'=>"email@example.com"));
````

### Logging In

The JS API uses a static login method, right now we have an instance method.

````php
    $user = new Stackmob\User;
    $user->set("username", "my name");
    $user->set("password", "my pass");
    $user->login();

    // or just use the rest class
    $restClient = new Stackmob\Rest();
    $loggedIn = $restClient->login($username,$password);
````

### Current User

````php
    $currentUser = Stackmob\User::current();
    if ($currentUser) {
        // do stuff with the user
    } else {
        // show the signup or login page
    }

    Stackmob\User::logOut();
    $currentUser = Stackmob\User::current(); // this will now be null
````

### Resetting Passwords

````php
    Stackmob\User::requestPasswordReset("email@example.com");
````

## Roles

__NOTE:__ Roles API NOT yet implemented

## Facebook Users

__NOTE:__ Facebook Users API NOT yet implemented

## GeoPoints

__NOTE:__ GeoPoints API NOT yet implemented

## Push

All the options available for Push via the REST API are supported.

````php
    // Super simple example
    Stackmob\Push::send(array(
        'channels' => array('channel1','channel2'),
        'data' => array('message'=>'blah')
    ));
````

## Cloud

````php
    Stackmob\Cloud::run('testMethod',array('someParam'));
````
