<?php
/**
 * Push
 */
namespace Stackmob;

use Stackmob\Rest;

class Push {

    /**
     * @var Rest
     */
    protected static $_restClient;

    /**
     * @param $data - The data of the push notification. Valid fields are:
     *  - alert : the message to display
     *  - badge : an iOS-specific value that changes the badge of the application icon (number or "Increment")
     *  - sound : an iOS-specific string representing the name of a sound file in the application bundle to play.
     * @return array|null
     */
    public static function send($data, $users = array()){

        if(!Push::$_restClient){
            Push::$_restClient = new Rest();
        }

        return Push::$_restClient->push($data, $users);
    }

    /**
     * Initialize is an empty function by default. Override it with your own initialization logic.
     */
    public function initialize(){
        // empty
    }    
}
