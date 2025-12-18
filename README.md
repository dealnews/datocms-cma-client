# datocms-cma-client
Unofficial DatoCMS API Client for the Content Management API

## Usage

Initialize the client:
```php
use DealNews\DatoCMS\CMA\Client;

$client = new Client('<your-api-token>', '<your-env-name>');
```

### Records/Items

Get a list of records/items
```php
$list = $client->record->list();
```

Get a filtered list of records/items
```php
use DealNews\DatoCMS\CMA\Parameters\Record;

$parameters = new Record();
$parameters->filter->ids = ['abcd', 'efgh'];

$list = $client->record->list($parameters);
```

Create a new record
```php

$new_record = [
    'type' => 'item',
    'attributes' => [
        'title' => 'Hello World',
        'body' => 'This is the body'
    ],
    'relationships' => [
        'item_type' => [
            'data' => [
                'type' => 'item_type',
                'id' => 'DxMaW10UQiCmZcuuA-IkkA'
            ],
        ]       
    ]
];

$result = $client->record->create($new_record);
```
