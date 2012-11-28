<?php
namespace Stackmob;

class Stackmob {

    /**
     * @var Rest
     */
    protected static $_restClient;
}

include("Rest.php");
include("Object.php");
include("User.php");
include("StackmobException.php");
// include("Push.php");

// Your credentials:
Rest::$consumerKey = "1e2fc86e-50ad-4230-a30d-0ba86ee50fb0";
Rest::$consumerSecret = "72b2fe32-1629-4684-88ec-05b5c29ea68d";
Rest::$DEVELOPMENT = true;	// replace or override with false for live code