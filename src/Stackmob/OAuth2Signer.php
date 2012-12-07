<?php
namespace Stackmob;
require '../../vendor/autoload.php';

require_once("OAuth.php");
/**
 * Class for signing requests after logging in
 *
 * @author jobiwankanobi
 */
class OAuth2Signer {
    protected $_accessToken;
    protected $_macKey;
    protected $log;
    /**
     * 
     * @param type $accessToken
     * @param type $macKey
     */
    function __construct($accessToken, $macKey) {
        $this->_accessToken = $accessToken;
        $this->_macKey = $macKey;
        $this->log = \Logger::getLogger(__CLASS__);
        $this->log->debug("Access token: $accessToken");
        $this->log->debug("Mac Key: $macKey");
    }
    
    // Private Methods
    
    /**
     * 
     * @param type $ts
     * @param type $nonce
     * @param type $method
     * @param type $uri
     * @param type $host
     * @param type $port
     * @return type
     */
    function _createBaseString($ts, $nonce, $method, $uri, $host, $port) {
        $nl = "\n";
        return ($ts . $nl . $nonce . $nl . $method . $nl . $uri . $nl . $host . $nl . $port . $nl . $nl);
    }
    
    /**
     * 
     * @param type $array
     * @return type
     */
    function _bin2String($array) {
        $result = "";
        for ( $i = 0; $i < count($array); $i++) {
            $result = ($result . chr($array[$i]));
        }
        return $result;
    }
    
    // Public methods
    
    /**
     * 
     * @param type $macKey
     * @return type
     */
    function macKey($macKey = null) {
        if(!$macKey)
            return $this->_macKey;
        else
            return $this->_macKey = $macKey;
    }
    
    /**
     * 
     * @param type $accessToken
     * @return type
     */
    function accessToken($accessToken = null) {
        if(!$accessToken)
            return $this->_accessToken;
        else
            return $this->_accessToken = $accessToken;
    }
    
    /**
     * 
     * @param type $method
     * @param type $hostWithPort
     * @param type $path
     * @return type
     */
    function generateMAC($method, $fullHost, $path) {
        $this->log->debug("Fullhost: $fullHost");
        $hostWithPort = preg_replace('/^http[s]?:\/\//', "", $fullHost);
        $this->log->debug("HostWIthPort: $hostWithPort");
        $splitHost = split(':', $hostWithPort);
        $hostNoPort = count($splitHost) > 1 ? $splitHost[0] : $hostWithPort;
        $port = count($splitHost) > 1 ? $splitHost[1] : 80;  //use default port 80 if http.  If you're using https then this should be 443
        $this->log->debug("HostWithPort: $hostWithPort");
        $this->log->debug("Port: $port");
        $ts = \Stackmob\OAuthRequest::generate_timestamp();
        $nonce = "n" . rand(0, 10000);
        
        $base = $this->_createBaseString($ts, $nonce, $method, $path, $hostNoPort, $port);
        $this->log->debug("BASE: $base");
        $this->log->debug("Access token: " . $this->_accessToken);
        $this->log->debug("Mac Key: " . $this->_macKey);
        $mac = \base64_encode(\hash_hmac('sha1', $base, $this->_macKey, true));
//        $this->log->debug("BHMAC: $bhmac");
//        $bstring = $this->_bin2String($bhmac);
//        $this->log->debug("BSTRING: $bstring");
//        $mac = base64_encode($bstring);
        $this->log->debug("HMAC: $mac");
        
        return 'Authorization:MAC id="' .  $this->_accessToken . '",ts="' . $ts . '",nonce="' . $nonce
        . '",mac="' . $mac . '"';
    }
}

?>
