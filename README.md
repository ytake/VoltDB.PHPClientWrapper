PHP VoltDB Client Wrapper
=========================
client wrapper / json interface support.

**required php-extension**  
curl  
[voltdb](https://github.com/VoltDB/voltdb-client-php)

**future**  
async

#install
```json

```

#usage
##VoltDB json API(simple)
```php
$client = new \Ytake\VoltDB\Client(new \Ytake\VoltDB\Parse);
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
$connection = new \Ytake\VoltDB\Connection(new \Ytake\VoltDB\Parse);
$connection->select("SELECT * FROM users")
```
**not supported prepared statements**  
JDBC driver(java) supports  
or stored procedure(DDL)

###Stored Procedure
```php
$connection = new \Ytake\VoltDB\Connection(new \Ytake\VoltDB\Parse);
$connection->procedure("Procedure-Name"));
```

