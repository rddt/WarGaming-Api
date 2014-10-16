WarGaming API Documentation
===========================

Before send API request to WarGaming server, your must create client and configure it.

Create API Client
-----------------

Base create API client:

```php
use WarGaming\Api\Client;
// Create client
$client = Client::createDefault();

// Set application id
$applicationId = '...';
$client->setApplicationId($applicationId);
```

And your can configure host, SSL, default language and region for client:

```php
$client
    ->setRegion(Client::REGION_RUSSIA) // Please see Client::REGION_* constants for available regions
    ->setRequestSecure(true) // If use SSL for connection
    ->setDefaultLanguage('ru')
;

// Attention: change the host is a dangerous operation
$client
    ->setHost('custom-host.com')
    ->setCustomHost(true) // If you can use custom host.
;
```

Use client
----------

After create client, your can send any methods.

Each method - is object, and can have many parameters (id, types as example). All methods
defined in `WarGaming\Api\Method` namespace.

Example of send method:

```php
use WarGaming\Api\Method\WoT\GlobalWar\Clans;

$method = new Clans();
$clans = $client->request($method);
```