WarGaming API Documentation
===========================

Before sending API requests to the WarGaming API server, you must create a client and configure the required properties.

Create API Client
-----------------

Create base API client:

```php
use WarGaming\Api\Client;
// Create client
$client = Client::createDefault();

// Set application id
// ApplicationIds can be obtained from: https://ru.wargaming.net/developers/ and are region specific.
$applicationId = '...';
$client->setApplicationId($applicationId);
```

Client requires a valid: applicationId, apiMode and region to operate.

apiMode must come before region.

`setRegion` method will use the official wargaming api host for the supplied apiMode and region.

You can configure: apimode, region, SSL and default language for the client:

```php
$client
    ->setApiMode(Client::TANKS) // Constants Client::TANKS and Client::PLANES supported
    ->setRegion(Client::REGION_RUSSIA) // Please see Client::REGION_* constants for available regions
    ->setRequestSecure(true) // Use SSL for connection
    ->setDefaultLanguage('ru')
;
```

If you need to use an api host that does not conform to the wargaming expected format:
`api.apimode.region` (example: `api.worldoftanks.ru`)
then use the `setHost` method to specify a custom host and do not use `setRegion`.

```php
// Attention: setting the host directly is an unsupported mode of operation.
$client
    ->setHost('custom-host.example.com')
;
```

Use client
----------

After creating a client, you can send any defined methods.

Each method - is an object, and can have many parameters (id, types as example). 
All methods are defined in `WarGaming\Api\Method` namespace.

Example of sending WoT\GlobalWar\Clans method:

```php
use WarGaming\Api\Method\WoT\GlobalWar\Clans;

$method = new Clans();
$method->map = 1; // Use Clan Wars map
$clans = $client->request($method);

```