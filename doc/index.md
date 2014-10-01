WarGaming API Documentation
===========================

Before send API request to WarGaming server, your must create client and configure it.

Base create API client:

```php
use WarGaming\Api\Client;
// Create client
$client = Client::createDefault();

// Set application id
$applicationId = '...';
$client->setApplicationId($applicationId);
```

After create client, your can send any methods.

Each method - is object, and can have many parameters (id, types as example). All methods
defined in `WarGaming\Api\Method` namespace.

Example of send method:

```php
use WarGaming\Api\Method\WoT\GlobalWar\Clans;

$method = new Clans();
$clans = $client->request($method);
```