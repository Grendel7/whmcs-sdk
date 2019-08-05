<?php

namespace HansAdema\WhmcsSdk;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;
use HansAdema\WhmcsSdk\Exceptions\Exception;
use HansAdema\WhmcsSdk\Exceptions\RequestException;
use HansAdema\WhmcsSdk\Exceptions\ResponseException;

class Client
{
    /**
     * @var Guzzle
     */
    protected $http;

    /**
     * @var string The WHMCS API username
     */
    protected $username;

    /**
     * @var string The WHMCS API password
     */
    protected $password;

    /**
     * @var string The WHMCS installation url
     */
    protected $url;

    /**
     * @var boon is WHCMS verison 7.2 or newer
     */
    protected $v7_2_plus;

    /**
     * Api constructor.
     * @param string $url The WHMCS installation URL
     * @param string $username The WHMCS API username
     * @param string $password The WHMCS API password
     */
    public function __construct($url, $username, $password, $v7_2_plus = true)
    {

        if(!endsWith($url, "/"))
        {
            $url += "/";
        }
        $this->url = $url;
        $this->username = $username;
        if($v7_2_plus)
        {
            $this->password = $password;
        }
        else {
            $this->password = md5($password);
        }

        $this->http = new Guzzle(['base_uri' => $url . 'includes/api.php']);
    }

    public function endsWith($haystack, $needle) {
        return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }

    public function auth_array()
    {

        if($this->v7_2_plus)
        {
            return [
                'identifier' => $this->username,
                'secret' => $this->password,
            ];
        }

        return [
            'username' => $this->username,
            'password' => $this->password,
        ];

    }

    /**
     * Send a request to WHMCS
     *
     * @param string $action The API action name
     * @param array $params The action parameters
     * @return array The API response data
     * @throws Exception
     */
    public function sendRequest($action, $params = [])
    {
        try {
            $response = $this->http->post('', [
                'form_params' => array_merge($params, [
                    'action' => $action,
                    'responsetype' => 'json',
                ], $this->auth_array()),
            ]);
        } catch (ClientException $e) {
            throw new RequestException($e->getResponse());
        }

        $data = json_decode($response->getBody(), true);

        if (isset($data['result']) && $data['result'] === 'success') {
            return $data;
        } else {
            throw new ResponseException($response);
        }
    }

    /**
     * Magic method to automagically build a sendRequest
     *
     * @param string $name
     * @param array $arguments
     * @return array
     */
    public function __call($name, $arguments)
    {
        $params = isset($arguments[0]) && is_array($arguments[0]) ? $arguments[0] : [];

        return $this->sendRequest(ucfirst($name), $params);
    }
}