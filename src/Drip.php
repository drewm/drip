<?php

namespace DrewM\Drip;

class Drip
{
    protected static $eventSubscriptions = [];
    protected static $receivedWebhook    = false;
    protected        $api_endpoint       = 'https://api.getdrip.com/v2';
    protected        $token              = false;
    protected        $accountID          = null;
    protected        $verify_ssl         = true;

    /**
     * Drip constructor.
     *
     * @param string      $token     API Token
     * @param string|null $accountID Drip account ID to operate on
     */
    public function __construct($token, $accountID = null)
    {
        $this->token = $token;

        if ($accountID !== null) {
            $this->accountID = $accountID;
        }
    }

    public static function subscribeToWebhook($event, callable $callback)
    {
        if (!isset(self::$eventSubscriptions[$event])) {
            self::$eventSubscriptions[$event] = [];
        }
        self::$eventSubscriptions[$event][] = $callback;

        self::receiveWebhook();
    }

    public static function receiveWebhook($input = null)
    {
        if ($input === null) {
            if (self::$receivedWebhook !== false) {
                $input = self::$receivedWebhook;
            } else {
                $input = file_get_contents("php://input");
            }
        }

        if ($input) {
            return self::processWebhook($input);
        }

        return false;
    }

    protected static function processWebhook($input)
    {
        if ($input) {
            self::$receivedWebhook = $input;
            $result                = json_decode($input, true);
            if ($result && isset($result['event'])) {
                self::dispatchWebhookEvent($result['event'], $result['data']);
                return $result;
            }
        }

        return false;
    }

    protected static function dispatchWebhookEvent($event, $data)
    {
        if (isset(self::$eventSubscriptions[$event])) {
            foreach (self::$eventSubscriptions[$event] as $callback) {
                $callback($data);
            }
            // reset subscriptions
            self::$eventSubscriptions[$event] = [];
        }
        return false;
    }

    /**
     * Set account ID if it was not passed into the constructor
     *
     * @param string $accountID
     *
     * @return void
     */
    public function setAccountId($accountID)
    {
        $this->accountID = $accountID;
    }

    /**
     * Make a GET request
     *
     * @param string $api_method API method to call
     * @param array  $args       API arguments
     * @param int    $timeout    Connection timeout (seconds)
     *
     * @return Response
     * @throws DripException
     */
    public function get($api_method, $args = [], $timeout = 10)
    {
        return $this->makeRequest('get', $api_method, $args, $timeout);
    }

    /**
     * Make the HTTP request
     *
     * @param string $http_verb  HTTP method used: get, post, delete
     * @param string $api_method Drip API method to call
     * @param array  $args       Array of arguments to the API method
     * @param int    $timeout    Connection timeout (seconds)
     * @param string $url        Optional URL to override the constructed one
     *
     * @return Response
     * @throws DripException
     */
    protected function makeRequest($http_verb, $api_method, $args = [], $timeout = 10, $url = null)
    {
        $this->checkDependencies();

        $url = $this->constructRequestUrl($url, $api_method);
        $ch  = $this->createCurlSession($url, $timeout);

        switch ($http_verb) {
            case 'post':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args));
                break;

            case 'get':
                curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($args));
                break;

            case 'delete':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        return $this->executeRequest($ch);
    }

    /**
     * Check for required PHP functionality
     *
     * @return bool
     * @throws DripException
     */
    private function checkDependencies()
    {
        if (!function_exists('curl_init') || !function_exists('curl_setopt')) {
            throw new DripException("cURL support is required, but can't be found.", 1);
        }

        return true;
    }

    /**
     * @param string|null $url
     * @param string      $api_method
     *
     * @return string
     * @throws DripException
     */
    private function constructRequestUrl($url, $api_method)
    {
        if ($url !== null) {
            return $url;
        }

        if ($this->accountID === null) {
            throw new DripException("This method requires an account ID and none has been set.", 2);
        }

        return $this->api_endpoint . '/' . $this->accountID . '/' . $api_method;
    }

    /**
     * Create a new CURL session (common setup etc)
     *
     * @param string $url
     * @param int    $timeout
     *
     * @return resource
     * @throws DripException
     */
    private function createCurlSession($url, $timeout = 10)
    {
        $ch = curl_init();

        if (!$ch) {
            throw new DripException("Unable to initialise curl", 3);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/vnd.api+json',
            'Content-Type: application/vnd.api+json',
        ]);
        curl_setopt($ch, CURLOPT_USERAGENT, 'DrewM/Drip (github.com/drewm/drip)');
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->token . ': ');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->verify_ssl);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_URL, $url);

        return $ch;
    }

    /**
     * Execute and handle the request result
     *
     * @param resource $ch Curl handle
     *
     * @return Response
     * @throws DripException
     */
    private function executeRequest(&$ch)
    {
        $result = curl_exec($ch);

        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            curl_close($ch);
            return new Response($info, $result);
        }

        $errno = curl_errno($ch);
        $error = curl_error($ch);

        curl_close($ch);

        throw new DripException($error, $errno);
    }

    /**
     * Make a GET request to a top-level method outside of this account
     *
     * @param string $api_method
     * @param array  $args
     * @param int    $timeout
     *
     * @return Response
     * @throws DripException
     */
    public function getGlobal($api_method, $args = [], $timeout = 10)
    {
        $url = $this->api_endpoint . '/' . $api_method;
        return $this->makeRequest('get', $api_method, $args, $timeout, $url);
    }

    /**
     * Make a POST request
     *
     * @param string $api_method API method
     * @param array  $args       Arguments to API method
     * @param int    $timeout    Connection timeout (seconds)
     *
     * @return Response
     * @throws DripException
     */
    public function post($api_method, $args = [], $timeout = 10)
    {
        return $this->makeRequest('post', $api_method, $args, $timeout);
    }

    /**
     * Make a DELETE request
     *
     * @param string $api_method API method
     * @param array  $args       Arguments to the API method
     * @param int    $timeout    Connection timeout (seconds)
     *
     * @return Response
     * @throws DripException
     */
    public function delete($api_method, $args = [], $timeout = 10)
    {
        return $this->makeRequest('delete', $api_method, $args, $timeout);
    }

    public function disableSSLVerification()
    {
        $this->verify_ssl = false;
    }
}
