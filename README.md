PHP VoltDB Client Wrapper
=========================

[![License](http://img.shields.io/packagist/l/ytake/voltdb-client-wrapper.svg?style=flat)](https://packagist.org/packages/ytake/voltdb-client-wrapper)
[![Latest Version](http://img.shields.io/packagist/v/ytake/voltdb-client-wrapper.svg?style=flat)](https://packagist.org/packages/ytake/voltdb-client-wrapper)
[![Total Downloads](http://img.shields.io/packagist/dt/ytake/voltdb-client-wrapper.svg?style=flat)](https://packagist.org/packages/ytake/voltdb-client-wrapper)
[![Dependency Status](https://www.versioneye.com/user/projects/541d94a53a1a2cd567000171/badge.svg?style=flat)](https://www.versioneye.com/user/projects/541d94a53a1a2cd567000171)

[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/ytake/VoltDB.PHPClientWrapper.svg?style=flat)](https://scrutinizer-ci.com/g/ytake/VoltDB.PHPClientWrapper/?branch=master)
[![Code Coverage](http://img.shields.io/scrutinizer/coverage/g/ytake/VoltDB.PHPClientWrapper/master.svg?style=flat)](https://scrutinizer-ci.com/g/ytake/VoltDB.PHPClientWrapper/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/ytake/VoltDB.PHPClientWrapper/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ytake/VoltDB.PHPClientWrapper/build-status/master)

voltdb client wrapper / json interface support.

**required extension**  
curl  
**suggest**  
[voltdb-client-php(native branch)](https://github.com/VoltDB/voltdb-client-php/tree/native)

#Install
```json
    "require": {
        "php": ">=5.4.0",
        "ext-curl": "*",
        "ext-voltdb": "*",
        "ytake/voltdb-client-wrapper": "0.*"
    },
```

#Usage
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
$connection->connect()->excute("SELECT * FROM users");
```
**not supported prepared statements**  
JDBC driver(java) supports or stored procedure(DDL)

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
