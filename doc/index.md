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

Client requires a valid: applicationId, apiMode and region to operate.

apiMode must come before region.

`setRegion` will cause the client to the official wargaming api host for the specified apiMode and region.

You can configure: apimode, region, SSL and default language for the client:

```php
$client
    ->setApiMode(Client::TANKS) // Constants Client::TANKS and Client::PLANES supported
    ->setRegion(Client::REGION_RUSSIA) // Please see Client::REGION_* constants for available regions
    ->setRequestSecure(true) // Use SSL for connection
    ->setDefaultLanguage('ru')
;
```
If you need to use an api host that does not conform to the expected format: `api.apimode.region` (eg: `api.worldoftanks.ru`)
then use setHost to specify a custom host and do not use setRegion.

```php
// Attention: setting the host directly is an unsupported operation.
$client
    ->setHost('custom-host.example.com')
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