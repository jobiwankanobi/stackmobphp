<?php
namespace Stackmob;

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
        $path = '/' . $path;
        $hostWithPort = preg_replace('/^http[s]?:\/\//', "", $fullHost);
        $splitHost = split(':', $hostWithPort);
        $hostNoPort = count($splitHost) > 1 ? $splitHost[0] : $hostWithPort;
        $port = count($splitHost) > 1 ? $splitHost[1] : 80;  //use default port 80 if http.  If you're using https then this should be 443
        $ts = \Stackmob\OAuthRequest::generate_timestamp();
        $nonce = substr(number_format(hexdec(sha1(microtime(true).mt_rand(10000,90000))),0,'',''), 0, 17);
        
        $base = $this->_createBaseString($ts, $nonce, $method, $path, $hostNoPort, $port);
        $mac = \base64_encode(\hash_hmac('sha1', $base, $this->_macKey, true));
        
        return 'Authorization:MAC id="' .  $this->_accessToken . '",ts="' . $ts . '",nonce="' . $nonce
        . '",mac="' . $mac . '"';
    }
}

?>
