<?php
namespace Stackmob;

class Stackmob {

    /**
     * @var Rest
     */
    protected static $_restClient;
}
\Logger::configure(__DIR__ . '/log4php.xml');