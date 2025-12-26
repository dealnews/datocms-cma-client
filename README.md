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

---

### Uploads

The library provides a complete Upload API for managing media assets in DatoCMS.

#### Upload a File

The simplest way to upload a file is with the `uploadFile()` helper method:

```php
// Upload a local file
$upload = $client->upload->uploadFile('/path/to/image.jpg');

// Upload with metadata
$upload = $client->upload->uploadFile('/path/to/image.jpg', [
    'author'    => 'John Doe',
    'copyright' => '© 2025 Company',
    'notes'     => 'Internal notes about this file',
    'tags'      => ['banner', 'hero', 'featured'],
    'default_field_metadata' => [
        'en' => ['alt' => 'Hero banner image', 'title' => 'Main Banner'],
        'es' => ['alt' => 'Imagen de banner', 'title' => 'Banner Principal'],
    ],
]);

// Upload to a specific collection (folder)
$upload = $client->upload->uploadFile('/path/to/image.jpg', null, 'collection-id');
```

#### Upload from URL

Upload a file directly from a remote URL:

```php
// Upload from URL
$upload = $client->upload->uploadFromUrl('https://example.com/image.jpg');

// With custom filename and metadata
$upload = $client->upload->uploadFromUrl(
    'https://example.com/image.jpg',
    'custom-filename.jpg',
    ['author' => 'Jane Doe']
);
```

#### List and Filter Uploads

```php
use DealNews\DatoCMS\CMA\Parameters\Upload;

$params = new Upload();
$params->filter->type = 'image';           // Filter by type: image, video, audio, etc.
$params->filter->query = 'banner';          // Full-text search
$params->filter->tags = ['hero', 'featured']; // Filter by tags
$params->filter->upload_collection_id = 'collection-123'; // Filter by folder
$params->order_by->addOrderByField('created_at', 'DESC');
$params->page->limit = 25;

$uploads = $client->upload->list($params);
```

#### Upload Collections (Folders)

Organize uploads into folders:

```php
use DealNews\DatoCMS\CMA\Input\UploadCollection;

// List collections
$collections = $client->upload_collection->list();

// Create a collection
$collection = new UploadCollection();
$collection->attributes['label'] = 'Product Images';
$result = $client->upload_collection->create($collection);

// Create a nested collection
$subcollection = new UploadCollection();
$subcollection->attributes['label'] = 'Thumbnails';
$subcollection->parent_id = $result['data']['id'];
$client->upload_collection->create($subcollection);

// Delete a collection
$client->upload_collection->delete($collection_id);
```

#### Upload Tags

Manage user-defined tags for uploads:

```php
// List all tags
$tags = $client->upload_tag->list();

// Create a tag
$tag = $client->upload_tag->create('featured');

// Delete a tag
$client->upload_tag->delete($tag['data']['id']);
```

#### Smart Tags

List auto-detected smart tags (read-only):

```php
$smart_tags = $client->upload_smart_tag->list();
```

#### Bulk Operations

```php
// Bulk delete uploads
$client->upload->deleteBulk(['upload-1', 'upload-2', 'upload-3']);

// Bulk update metadata
$client->upload->updateBulk(
    ['upload-1', 'upload-2'],
    ['author' => 'Updated Author', 'copyright' => '© 2025']
);
```

#### Manual Upload Flow

For advanced use cases, you can use the low-level upload flow:

```php
use DealNews\DatoCMS\CMA\Input\Upload;

// Step 1: Request upload permission
$request = $client->upload_request->create('image.jpg');
$s3_url = $request['data']['attributes']['url'];
$s3_headers = $request['data']['attributes']['request_headers'];

// Step 2: Upload to S3 (using your own HTTP client)
// PUT $s3_url with file contents and $s3_headers

// Step 3: Register the upload in DatoCMS
$upload = new Upload();
$upload->attributes->path = $request['data']['id'];
$upload->attributes->author = 'John Doe';
$result = $client->upload->create($upload);
```
