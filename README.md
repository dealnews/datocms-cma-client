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

#### Creating and Updating Records

You can create/update records in three ways:

**Option 1: Using the Record Input Class with DataTypes**

This approach provides type safety and validation, but it is incomplete. Structured-Text is the big missing piece.

```php
use DealNews\DatoCMS\CMA\Input\Record;
use DealNews\DatoCMS\CMA\DataTypes\Scalar;
use DealNews\DatoCMS\CMA\DataTypes\Color;
use DealNews\DatoCMS\CMA\DataTypes\Location;
use DealNews\DatoCMS\CMA\DataTypes\Asset;
use DealNews\DatoCMS\CMA\DataTypes\SEO;

// Create a new record with the item_type_id
$record = new Record('DxMaW10UQiCmZcuuA-IkkA');

// Use Scalar DataType for simple values
$title = Scalar::init()->set('Hello World');
$record->attributes['title'] = $title;

// Or use DataType helper methods for complex types
$color = Color::init()->setColor(255, 128, 64, 200);
$record->attributes['brand_color'] = $color;

$location = Location::init()->setLocation(40.7128, -74.0060);
$record->attributes['coordinates'] = $location;

$asset = Asset::init()->setAsset('upload_123', 'Image Title', 'Alt Text');
$record->attributes['image'] = $asset;

$seo = SEO::init()->setSEO('Page Title', 'Page Description', 'image_id', 'summary', false);
$record->attributes['seo'] = $seo;

// Convert to array and create the record
$result = $client->record->create($record->toArray());
```

**Option 2: Using the Record Input Class with Plain Arrays**

You can use the `Record` class but populate attributes with arrays:

```php
use DealNews\DatoCMS\CMA\Input\Record;

$record = new Record('DxMaW10UQiCmZcuuA-IkkA');
$record->attributes = [
    'title' => 'Hello World',
    'body' => 'This is the body',
    'brand_color' => [
        'red' => 255,
        'green' => 128,
        'blue' => 64,
        'alpha' => 200
    ]
];

$result = $client->record->create($record->toArray());
```

**Option 3: Using a Plain Array**

You can still pass a plain array directly:

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

#### Available DataTypes Classes

The library provides the following DataTypes classes for structured field values:

- `Scalar` - For simple string, integer, float, or boolean values
- `Color` - For RGBA color values (red, green, blue, alpha: 0-255)
- `Location` - For geographic coordinates (latitude: -90 to 90, longitude: -180 to 180)
- `Asset` - For file uploads with metadata (upload_id, title, alt, focal_point, custom_data)
- `ExternalVideo` - For external video embeds (YouTube, Vimeo, Facebook)
- `SEO` - For SEO metadata (title, description, image, twitter_card, no_index)

All DataTypes support localization via the `addLocale()` method for multilingual content.
