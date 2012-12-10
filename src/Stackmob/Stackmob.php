<?php
namespace Stackmob;

class Stackmob {

    /**
     * @var Rest
     */
    protected static $_restClient;
}
require __DIR__ . '/../../vendor/autoload.php';
\Logger::configure(__DIR__ . '/log4php.xml');

include("Rest.php");
include("Object.php");
include("User.php");
include("Query.php");
include("CustomCode.php");
include("StackmobException.php");
include("LoginSessionExpiredException.php");
// include("Push.php");

// Your credentials:
Rest::$consumerKey = "2423423423423423423";
Rest::$consumerSecret = "23423432423424234234";
Rest::$DEVELOPMENT = true;	// replace or override with false for live code