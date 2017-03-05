# WHMCS SDK

A simple wrapper around the WHMCS API.

## Installation
This package is installed using Composer. You can use the following command to add it to a project.

```bash
composer require hansadema/whmcs-sdk
composer update
```

## Usage
This API client encapsulates the WHMCS with a simple OO wrapper. 

First, you need to create a client instance with the details of your WMCS installation:
```php
$api = new \HansAdema\WhmcsSdk\Client('https://example.com/whmcs/installation/url/', 'myusername', 'mypassword');
```
Note the trailing `/` in the URL. The username and password are the credentials of a user with the "API Access" permission. You can use your main admin user for this, but for security it's recommended to create a special API user for every project.

After creating the client, you can start to send a request. The methods correspond to the action names from [the WHMCS API](https://developers.whmcs.com/api/api-index/), the other attributes can be submitted as an array.

For example, to execute the "AcceptOrder" action, you could use the following code.
```php
try {
    $result = $api->acceptOrder([
        'orderid' => 123,
        'serverid' => 456,
        //...
    ]);
} catch (\HansAdema\WhmcsSdk\RequestException $e) {
    echo "Error connecting to WHMCS: ".$e->getMessage();
} catch (\HansAdema\WhmcsSdk\ResponseException $e) {
    echo "There was an issue with your API call: ".$e->getMessage();
}
```

Note that two different types of exceptions are being used here. The `RequestException` is used whenever there is a problem connecting to your WHMCS installation, for example because the installation is down or the credentials are not correct. The `ResponseException` is thrown whenever the API result was not successful, for example due to missing or invalid method parameters.