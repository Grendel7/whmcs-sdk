# WHMCS SDK

A simple wrapper around the WHMCS SDK.

## Installation
This package is installed using Composer. You can use the following command to add it to a project.

```bash
composer require hansadema/whmcs-sdk
composer update
```

## Usage
Create an API client:
```php
$api = new \HansAdema\WhmcsSdk\Client('https://example.com/whmcs/installation/url/', 'myusername', 'mypassword');
```

Send an API request:
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