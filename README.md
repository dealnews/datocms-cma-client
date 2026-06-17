# datocms-cma-client

Unofficial PHP client for the [DatoCMS Content Management API](https://www.datocms.com/docs/content-management-api). Provides a structured, type-safe interface for managing records, models, uploads, and other DatoCMS resources.

---

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Records](#records)
  - [Models (Item Types)](#models-item-types)
  - [Model Filters](#model-filters)
  - [Uploads](#uploads)
  - [Upload Collections](#upload-collections)
  - [Upload Tags](#upload-tags)
  - [Webhooks](#webhooks)
  - [Other Endpoints](#other-endpoints)
- [DataTypes](#datatypes)
- [Testing](#testing)
- [License](#license)

---

## Features

- Full coverage of the DatoCMS CMA: records, models, uploads, webhooks, environments, fields, plugins, and more
- Multiple independent client instances — each with its own API token (no global singleton)
- Sync and async upload helpers: `uploadFileAndWait()` / `uploadFile()`, `uploadFromUrlAndWait()` / `uploadFromUrl()`
- Typed DataType classes for structured field values (Color, Location, Asset, SEO, ExternalVideo, Scalar)
- Localization support on all DataType fields via `addLocale()`
- Automatic rate-limit retry on HTTP 429
- PSR-3 logger support

---

## Requirements

- PHP 8.2+
- Composer

---

## Installation

```bash
composer require dealnews/datocms-cma-client
```

---

## Quick Start

```php
use DealNews\DatoCMS\CMA\Client;

$client = new Client('your-api-token', 'your-environment');

// List records
$records = $client->record->list();

// Upload a file and wait for processing to finish
$upload = $client->upload->uploadFileAndWait('/path/to/image.jpg');
echo $upload['data']['id'];
```

---

## Configuration

### Constructor parameters

```php
$client = new Client(
    apiToken: 'your-api-token',      // Required (or set DN_DATOCMS_API_TOKEN env var)
    environment: 'sandbox',          // Optional: DatoCMS environment name
    logger: $psrLogger,              // Optional: PSR-3 logger
    log_level: \Psr\Log\LogLevel::DEBUG, // Optional: default 'info'
    base_url: 'https://proxy.example.com' // Optional: custom base URL for proxies
);
```

### Environment variables

If constructor arguments are omitted, these environment variables are read on instantiation:

| Variable | Description |
|----------|-------------|
| `DN_DATOCMS_API_TOKEN` | API token for authentication |
| `DN_DATOCMS_ENVIRONMENT` | DatoCMS environment name |
| `DN_DATOCMS_BASE_URL` | Custom base URL (useful for proxies) |
| `DN_DATOCMS_LOG_LEVEL` | PSR-3 log level (default: `info`) |

### Multiple clients

Each `Client` instance is fully independent, so you can use different tokens simultaneously:

```php
$client_a = new Client('token-for-project-a');
$client_b = new Client('token-for-project-b');

$client_a->record->list(); // uses token-for-project-a
$client_b->record->list(); // uses token-for-project-b
```

---

## Usage

### Records

```php
// List all records
$records = $client->record->list();

// Filter records
use DealNews\DatoCMS\CMA\Parameters\Record as RecordParams;

$params = new RecordParams();
$params->filter->type = ['blog_post'];
$params->filter->ids = ['id-1', 'id-2'];
$params->order_by->addOrderByField('created_at', 'DESC');
$params->page->limit = 50;
$records = $client->record->list($params);

// Retrieve a single record
$record = $client->record->retrieve('record-id');

// Create a record
use DealNews\DatoCMS\CMA\Input\Record as RecordInput;
use DealNews\DatoCMS\CMA\DataTypes\Scalar;

$input = new RecordInput('item-type-id');
$input->attributes['title'] = Scalar::init()->set('Hello World');
$result = $client->record->create($input);

// Update a record
$input = new RecordInput();
$input->attributes['title'] = Scalar::init()->set('Updated Title');
$result = $client->record->update('record-id', $input);

// Delete a record
$client->record->delete('record-id');

// Publish / Unpublish
$client->record->publish('record-id');
$client->record->unpublish('record-id');

// Bulk operations
$client->record->publishBulk(['id-1', 'id-2']);
$client->record->unpublishBulk(['id-1', 'id-2']);
$client->record->deleteBulk(['id-1', 'id-2']);
```

#### Creating records with plain arrays

If you don't need DataType validation, you can pass a plain array:

```php
$result = $client->record->create([
    'type' => 'item',
    'attributes' => [
        'title' => 'Hello World',
        'body'  => 'Content here',
    ],
    'relationships' => [
        'item_type' => ['data' => ['type' => 'item_type', 'id' => 'item-type-id']],
    ],
]);
```

---

### Models (Item Types)

```php
// List all models
$models = $client->model->list();

// Retrieve a model
$model = $client->model->retrieve('model-id');

// Create a model
use DealNews\DatoCMS\CMA\Input\Model as ModelInput;

$input = new ModelInput();
$input->attributes['name']               = 'Blog Post';
$input->attributes['api_key']            = 'blog_post';
$input->attributes['singleton']          = false;
$input->attributes['sortable']           = true;
$input->attributes['draft_mode_active']  = true;
$result = $client->model->create($input);

// Update a model
$input = new ModelInput();
$input->attributes['name'] = 'Updated Name';
$client->model->update('model-id', $input);

// Delete / Duplicate
$client->model->delete('model-id');
$client->model->duplicate('model-id');
```

**Common model attributes:**

| Attribute | Type | Description |
|-----------|------|-------------|
| `name` | string | Human-readable name |
| `api_key` | string | Machine-friendly identifier |
| `singleton` | bool | Single-instance model |
| `sortable` | bool | Allow manual sorting |
| `modular_block` | bool | Is a modular block |
| `tree` | bool | Hierarchical records |
| `draft_mode_active` | bool | Enable draft/publish workflow |
| `all_locales_required` | bool | Require all locales to be filled |

---

### Model Filters

Saved searches that help editors quickly filter records within a model.

```php
// List filters
$filters = $client->model_filter->list();

// Create a filter
use DealNews\DatoCMS\CMA\Input\ModelFilter;

$filter = new ModelFilter('model-id');
$filter->attributes['name'] = 'Draft posts';
$filter->attributes['filter'] = [
    'fields' => ['_status' => ['eq' => 'draft']],
];
$filter->attributes['shared'] = true;
$result = $client->model_filter->create($filter);

// Update / Delete
$client->model_filter->update('filter-id', $filter);
$client->model_filter->delete('filter-id');
```

---

### Uploads

#### Sync upload (recommended)

Returns the completed upload once DatoCMS finishes processing. Polls internally until done or timeout.

```php
// Upload a local file (waits up to 30 seconds by default)
$upload = $client->upload->uploadFileAndWait('/path/to/image.jpg');
echo $upload['data']['id'];

// Upload with metadata and custom timeout
$upload = $client->upload->uploadFileAndWait(
    '/path/to/image.jpg',
    ['author' => 'Jane Doe', 'tags' => ['hero']],
    'collection-id',  // optional upload collection ID
    60                // timeout in seconds
);

// Upload from URL
$upload = $client->upload->uploadFromUrlAndWait('https://example.com/image.jpg');
```

#### Async upload

Returns a job payload immediately. Poll `$client->job->retrieve($job_id)` until complete.

```php
$job = $client->upload->uploadFile('/path/to/image.jpg');
$job_id = $job['data']['id'];
// poll $client->job->retrieve($job_id) ...

$job = $client->upload->uploadFromUrl('https://example.com/image.jpg', 'custom-name.jpg');
```

#### List and filter uploads

```php
use DealNews\DatoCMS\CMA\Parameters\Upload as UploadParams;

$params = new UploadParams();
$params->filter->type  = 'image';
$params->filter->query = 'banner';
$params->filter->tags  = ['hero'];
$params->filter->fields->addField('width', 1920, 'gte');
$params->order_by->addOrderByField('created_at', 'DESC');
$params->page->limit = 25;

$uploads = $client->upload->list($params);
```

#### Other upload operations

```php
// Retrieve, update, delete
$upload = $client->upload->retrieve('upload-id');
$client->upload->update('upload-id', $input);
$client->upload->delete('upload-id');

// Bulk operations
$client->upload->deleteBulk(['upload-1', 'upload-2']);
$client->upload->updateBulk(['upload-1', 'upload-2'], ['author' => 'Updated Author']);
```

---

### Upload Collections

Organize uploads into folders.

```php
use DealNews\DatoCMS\CMA\Input\UploadCollection;

// List / retrieve
$collections = $client->upload_collection->list();
$collection  = $client->upload_collection->retrieve('collection-id');

// Create a folder
$input = new UploadCollection();
$input->attributes['label'] = 'Product Images';
$result = $client->upload_collection->create($input);

// Create a nested folder
$sub = new UploadCollection();
$sub->attributes['label'] = 'Thumbnails';
$sub->parent_id = $result['data']['id'];
$client->upload_collection->create($sub);

// Delete
$client->upload_collection->delete('collection-id');
```

---

### Upload Tags

```php
// List / retrieve
$tags = $client->upload_tag->list();
$tag  = $client->upload_tag->retrieve('tag-id');

// Create / delete
$tag = $client->upload_tag->create('featured');
$client->upload_tag->delete($tag['data']['id']);

// Smart tags (auto-detected, read-only)
$smart_tags = $client->upload_smart_tag->list();
```

---

### Webhooks

```php
use DealNews\DatoCMS\CMA\Input\Webhook;

// List / retrieve
$webhooks = $client->webhook->list();
$webhook  = $client->webhook->retrieve('webhook-id');

// Create
$input = new Webhook();
$input->attributes['name']       = 'My Webhook';
$input->attributes['url']        = 'https://example.com/hook';
$input->attributes['http_basic_user'] = null;
$result = $client->webhook->create($input);

// Update / delete
$client->webhook->update('webhook-id', $input);
$client->webhook->delete('webhook-id');

// Webhook call logs
$calls = $client->webhook_call->list();
$call  = $client->webhook_call->retrieve('call-id');
```

---

### Other Endpoints

```php
// Fields (within a model)
$fields = $client->field->list('model-id');
$client->field->retrieve('field-id');
$client->field->create('model-id', $input);
$client->field->update('field-id', $input);
$client->field->delete('field-id');

// Fieldsets
$client->fieldset->list('model-id');
$client->fieldset->create('model-id', $input);

// Environments
$client->environment->list();
$client->environment->retrieve('env-id');

// Site settings
$site = $client->site->retrieve();
$client->site->update($input);

// Jobs (poll async operation results)
$job = $client->job->retrieve('job-id');

// Maintenance mode
$client->maintenance->retrieve();
$client->maintenance->activate();
$client->maintenance->deactivate();

// Plugins
$client->plugin->list();
$client->plugin->retrieve('plugin-id');
$client->plugin->create($input);
$client->plugin->update('plugin-id', $input);
$client->plugin->delete('plugin-id');

// Record versions
$versions = $client->record_version->list('record-id');
$version  = $client->record_version->retrieve('version-id');

// Scheduled publication / unpublishing
$client->scheduled_publication->create('record-id', $input);
$client->scheduled_publication->delete('record-id');
$client->scheduled_unpublishing->create('record-id', $input);
$client->scheduled_unpublishing->delete('record-id');
```

---

## DataTypes

Typed wrappers for DatoCMS field values. All DataTypes support localization via `addLocale()`.

```php
use DealNews\DatoCMS\CMA\DataTypes\Scalar;
use DealNews\DatoCMS\CMA\DataTypes\Color;
use DealNews\DatoCMS\CMA\DataTypes\Location;
use DealNews\DatoCMS\CMA\DataTypes\Asset;
use DealNews\DatoCMS\CMA\DataTypes\SEO;
use DealNews\DatoCMS\CMA\DataTypes\ExternalVideo;

// Single value
$title = Scalar::init()->set('Hello World');

// Localized value
$title = Scalar::init()
    ->addLocale('en', 'Hello World')
    ->addLocale('es', 'Hola Mundo');

// Color (RGBA, 0-255 per channel)
$color = Color::init()->setColor(255, 128, 64, 255);

// Geographic coordinates
$location = Location::init()->setLocation(40.7128, -74.0060);

// File asset
$asset = Asset::init()->setAsset('upload-id', 'Image Title', 'Alt text');

// SEO fields
$seo = SEO::init()->setSEO('Page Title', 'Description', 'image-upload-id', 'summary', false);

// External video
$video = ExternalVideo::init()->setExternalVideo(
    'youtube', 'dQw4w9WgXcQ',
    'https://youtu.be/dQw4w9WgXcQ',
    1280, 720,
    'https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg',
    'Never Gonna Give You Up'
);
```

Attach DataTypes to a record input:

```php
use DealNews\DatoCMS\CMA\Input\Record as RecordInput;

$record = new RecordInput('item-type-id');
$record->attributes['title']       = Scalar::init()->set('Hello');
$record->attributes['brand_color'] = Color::init()->setColor(255, 128, 64, 255);
$record->attributes['location']    = Location::init()->setLocation(40.71, -74.00);

$result = $client->record->create($record);
```

> **Note:** Structured Text fields are not yet supported by the DataTypes layer. Pass structured text as a plain PHP array matching the DatoCMS structured text format.

---

## Testing

```bash
# Install dependencies
composer install

# Run all tests
composer test

# Run a single test file
composer test -- tests/API/RecordTest.php

# Run lint check
composer lint

# Fix code style
composer fix
```

---

## License

BSD-3-Clause. See [LICENSE](LICENSE) for details.
