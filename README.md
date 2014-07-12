PHP VoltDB Client Wrapper
=========================
[![Latest Stable Version](https://poser.pugx.org/ytake/voltdb-client-wrapper/v/stable.svg)](https://packagist.org/packages/ytake/voltdb-client-wrapper)
[![Total Downloads](https://poser.pugx.org/ytake/voltdb-client-wrapper/downloads.svg)](https://packagist.org/packages/ytake/voltdb-client-wrapper)
[![Latest Unstable Version](https://poser.pugx.org/ytake/voltdb-client-wrapper/v/unstable.svg)](https://packagist.org/packages/ytake/voltdb-client-wrapper) [![License](https://poser.pugx.org/ytake/voltdb-client-wrapper/license.svg)](https://packagist.org/packages/ytake/voltdb-client-wrapper)
[![Build Status](https://travis-ci.org/ytake/VoltDB.PHPClientWrapper.svg)](https://travis-ci.org/ytake/VoltDB.PHPClientWrapper)
client wrapper / json interface support.

**required php-extension**
curl
[voltdb](https://github.com/VoltDB/voltdb-client-php)

#install
```json
    "require": {
        "php": ">=5.4.0",
        "ext-curl": "*",
        "ext-voltdb": "*",
        "ytake/voltdb-client-wrapper": "0.*"
    },
```

#usage
##VoltDB json API(simple)
```php
$client = new \Ytake\VoltDB\HttpClient(new \Ytake\VoltDB\Parse);
// get request
$result = $client->request('http://localhost')->get(['Procedure' => 'allUser'])->getResult();
// post request
$result = $client->request('http://localhost')->post([
    'Procedure' => 'addUser',
    'Parameters' => [1, "voltdb"]
])->getResult();
```
###use parameters
[JSON HTTP Interface](http://voltdb.com/docs/UsingVoltDB/ProgLangJson.php)
same arguments
```php
// procedure-name
'Procedure' => null,
// procedure-parameters
'Parameters' => null,
// username for authentication
'User' => null,
// password for authentication
'Password' => null,
// Hashed password for authentication
'Hashedpassword' => null,
// true|false
'admin' => false,
// function-name
'jsonp' => null
```

###get SystemInformation
```php
// default "OVERVIEW"
$client->request('http://localhost')->info()->getResult();
// DEVELOPMENT
$client->request('http://localhost')->info("DEPLOYMENT")->getResult();
```

##VoltClient wrapper
###AdHoc Queries
```php
$connection = new \Ytake\VoltDB\Client(new \VoltClient, new \Ytake\VoltDB\Parse);
$connection->connect()->select("SELECT * FROM users");
```
**not supported prepared statements**
JDBC driver(java) supports
or stored procedure(DDL)

###Stored Procedure
```php
$connection = new \Ytake\VoltDB\Client(new \VoltClient, new \Ytake\VoltDB\Parse);
$connection->connect()->procedure("Procedure-Name");
```

###Async Stored Procedure
```php
$connection = new \Ytake\VoltDB\Client(new \VoltClient, new \Ytake\VoltDB\Parse);
$async = $connection->connect()->asyncProcedure("allUser");
// blocking and get result
$result = $async->drain()->asyncResult();
```