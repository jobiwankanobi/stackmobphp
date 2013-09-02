<?php

/**
* @author Federico Freire <nerdscape@gmail.com>
*/

namespace Stackmob;

class Configuration
{
    private static $key;
    private static $secret;
    private static $logger;
    private static $environment = 'dev';

    public static function getKey()
    {
        if (empty(self::$key)) {
            throw new \Exception(__METHOD__ . " - Stackmob key must be set.", 1);
            
        }
        return self::$key;
    }

    public static function setKey($key)
    {
        self::$key = $key;
    }

    public static function getSecret()
    {
        if (empty(self::$secret)) {
            throw new \Exception(__METHOD__ . " - Stackmob secret must be set.", 1);
            
        }
        return self::$secret;
    }

    public static function setSecret($secret)
    {
        self::$secret = $secret;
    }

    public static function getLogger()
    {
        return self::$logger;
    }

    public static function setLogger($logger)
    {
        self::$logger = $logger;
    }

    public static function getEnvironment()
    {
        return self::$environment;
    }

    public static function setEnvironment($environment)
    {
        self::$environment = $environment;
    }

    public static function getVersion()
    {
        return (self::$environment === 'prod') ? 1 : 0;
    }
}