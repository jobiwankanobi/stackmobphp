<?php

/**
* Used if no logger object was passed to the class that needs to log.
* @author Federico Freire
*/

namespace Stackmob;

class DummyLogger
{
    public function debug()
    {
    }
}