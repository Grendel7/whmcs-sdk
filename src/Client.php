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
     * Api constructor.
     * @param string $url The WHMCS installation URL
     * @param string $username The WHMCS API username
     * @param string $password The WHMCS API password
     */
    public function __construct($url, $username, $password)
    {
        $this->url = $url;
        $this->username = $username;
        $this->password = md5($password);

        $this->http = new Guzzle(['base_uri' => $url . 'includes/api.php']);
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
                    'username' => $this->username,
                    'password' => $this->password,
                    'action' => $action,
                    'responsetype' => 'json',
                ]),
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