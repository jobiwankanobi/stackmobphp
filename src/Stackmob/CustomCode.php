<?php
/**
 *
 */

namespace Stackmob;

class CustomCode extends Stackmob {

    const API_PATH = 'http://api.mob1.stackmob.com/';

    /**
     * @param $name
     * @param $data
     * @return array|null
     */
    public static function run($name,$data=array()){

        if(!Stackmob::$_restClient){
            Stackmob::$_restClient = new Rest(CustomCode::API_PATH);
        }

        $result = Sparse::$_restClient->post($name,$data);

        return $result;
    }
}
