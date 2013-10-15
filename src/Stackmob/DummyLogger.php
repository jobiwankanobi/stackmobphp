<?php

/**
* Used if no logger object was passed to the class that needs to log.
* @author Federico Freire
*/

namespace Stackmob;

class DummyLogger
{
    public function info($msg, $other = null)
    {
    }
    public function critical($msg, $other = null)
    {
    }
    public function warn($msg, $other = null)
    {
    }
    public function error($msg, $other = null)
    {
    }
    public function debug($msg, $other = null)
    {
    }
}