PHP VoltDB Client Wrapper
=========================
json interface support.

**future**  
voltDB SQL support  
voltDB stored procedure  
voltDB schema builder  

#usage
##voltdb json API(simple)
**use curl**
```php
$client = new \Ytake\VoltDB\Client;
// get request
$result = $client->access('http://localhost')->get(['Procedure' => 'allUser']);
// post request
$result = $client->access('http://localhost')->post(['Procedure' => 'addUser', [1, "voltdb"]]);
```

