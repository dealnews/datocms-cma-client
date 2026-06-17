# AGENTS.md — DatoCMS CMA Client

> **Purpose**: This document provides AI agents with comprehensive context about this PHP library. Use it to understand architecture, conventions, and how to make effective contributions.

---

## Project Overview

**What it is**: An unofficial PHP client library for the [DatoCMS Content Management API](https://www.datocms.com/docs/content-management-api). It provides a structured, type-safe way to interact with DatoCMS for managing records/items.

**Namespace**: `DealNews\DatoCMS\CMA`

**PHP Version**: Requires PHP 8.2+

**Key Dependencies**:
- `guzzlehttp/guzzle` ^7.10 — HTTP client
- `moonspot/value-objects` ^2.3 — Base class for value objects with `toArray()` support
- `psr/log` ^3.0 — PSR-3 logging interface

---

## Key Commands

- **Test all**: `composer test`
- **Test single file**: `composer test -- tests/API/RecordTest.php`
- **Lint**: `composer lint`
- **Lint fixer**: `composer fix`

---

## Workflow

- Run `composer fix` after modifying any PHP files to apply code style fixes.
- All tests must pass (`composer test`) after code changes with no PHPUnit deprecation warnings.

---

## Directory Structure

```
src/
├── API/                    # API endpoint handlers (all extend Base.php)
│   ├── Base.php           # Abstract base: accepts Handler or Config, initializes HTTP handler
│   ├── Environment.php    # Environment CRUD operations
│   ├── Field.php          # Field CRUD operations
│   ├── FieldSet.php       # Fieldset CRUD operations
│   ├── Job.php            # Job result retrieval
│   ├── Maintenance.php    # Maintenance mode operations
│   ├── Model.php          # Model/item-type CRUD operations (6 methods)
│   ├── ModelFilter.php    # Model filter CRUD operations (5 methods)
│   ├── Plugin.php         # Plugin CRUD operations
│   ├── Record.php         # Record/item CRUD operations (13 methods)
│   ├── RecordVersion.php  # Record version retrieval
│   ├── ScheduledPublication.php   # Scheduled publication operations
│   ├── ScheduledUnpublishing.php  # Scheduled unpublishing operations
│   ├── Site.php           # Site/project settings operations
│   ├── Upload.php         # Upload CRUD + sync/async helper methods
│   ├── UploadCollection.php # Upload folder CRUD operations
│   ├── UploadRequest.php  # S3 upload permission requests
│   ├── UploadSmartTag.php # Auto-detected smart tags (read-only)
│   ├── UploadTag.php      # User-defined upload tags CRUD
│   ├── Webhook.php        # Webhook CRUD operations
│   └── WebhookCall.php    # Webhook call log retrieval
├── DataTypes/             # Value objects for DatoCMS field types
│   ├── Common.php         # Abstract base with localization support
│   ├── Scalar.php         # Simple string/int/float/bool values
│   ├── Color.php          # RGBA color (0-255 per channel)
│   ├── Location.php       # Geographic coordinates (lat/long)
│   ├── Asset.php          # File uploads with metadata
│   ├── SEO.php            # SEO metadata fields
│   └── ExternalVideo.php  # External video embeds
├── Exception/             # Custom exceptions
│   ├── API.php            # API errors (stores response body)
│   ├── Decode.php         # JSON decode failures
│   ├── S3Upload.php       # S3 upload failures
│   ├── Timeout.php        # Job polling timeouts
│   └── Unknown.php        # Unexpected errors
├── HTTP/
│   └── Handler.php        # Guzzle wrapper with auto-retry on 429
├── Input/                 # Objects for create/update operations
│   ├── Model.php          # Main input object for models
│   ├── ModelFilter.php    # Main input object for model filters
│   ├── Record.php         # Main input object for records
│   ├── Upload.php         # Input for upload create/update
│   ├── UploadCollection.php # Input for collection create/update
│   └── Parts/             # Sub-components
│       ├── Meta.php       # Record metadata (created_at, stage, etc.)
│       ├── Relationships.php
│       ├── Relationships/
│       │   ├── Creator.php
│       │   └── ItemType.php
│       └── Upload/
│           ├── Attributes.php        # Upload attributes (path, author, etc.)
│           └── DefaultFieldMetadata.php # Localized alt/title per locale
├── Parameters/            # Query parameter objects for API filtering
│   ├── Common.php         # Abstract base with pagination
│   ├── CommonWithLocale.php
│   ├── Model.php          # Parameters for listing models
│   ├── Record.php         # Parameters for listing records
│   ├── Upload.php         # Parameters for listing uploads
│   ├── UploadCollection.php # Parameters for listing collections
│   └── Parts/
│       ├── Filter.php
│       ├── FilterFields.php
│       ├── OrderBy.php
│       ├── Page.php
│       └── UploadFilter.php # Upload-specific filter options
├── Client.php             # Main entry point
└── Config.php             # Per-instance configuration (reads env vars on construct)

tests/
├── API/                   # Unit tests for API classes
│   ├── BaseTest.php       # Tests Base constructor with mocked Handler
│   ├── ModelTest.php      # Tests all 6 Model API methods
│   ├── ModelFilterTest.php # Tests all 5 ModelFilter API methods
│   ├── RecordTest.php     # Tests all 13 Record API methods
│   ├── UploadTest.php     # Tests Upload API + helper methods
│   ├── UploadCollectionTest.php
│   ├── UploadRequestTest.php
│   ├── UploadSmartTagTest.php
│   └── UploadTagTest.php
├── DataTypes/             # Unit tests for DataType classes
│   └── CommonTest.php     # Tests abstract Common class edge cases
├── Exception/             # Unit tests for exception classes
│   ├── APITest.php
│   ├── DecodeTest.php
│   ├── S3UploadTest.php
│   ├── TimeoutTest.php
│   └── UnknownTest.php
├── HTTP/                  # Unit tests for HTTP layer
│   └── HandlerTest.php    # Tests execute(), retry logic, caching
├── Input/                 # Unit tests for Input classes
│   ├── ModelTest.php      # Tests Model input serialization
│   ├── ModelFilterTest.php # Tests ModelFilter input serialization
│   ├── RecordTest.php     # Tests Record input serialization
│   ├── UploadTest.php
│   ├── UploadCollectionTest.php
│   └── Parts/
│       └── Upload/
│           ├── AttributesTest.php
│           └── DefaultFieldMetadataTest.php
├── Parameters/            # Unit tests for Parameter classes
│   ├── ModelTest.php      # Tests Model parameters
│   ├── RecordTest.php     # Tests Record parameters with version validation
│   ├── UploadTest.php
│   ├── UploadCollectionTest.php
│   └── Parts/
│       └── UploadFilterTest.php
├── ClientTest.php         # Tests Client constructor and per-instance config
├── ConfigTest.php         # Tests instance-based config, env vars, magic methods
└── bootstrap.php          # Autoloader setup
```

---

## Architecture Patterns

### Entry Point: `Client`

The `Client` class is the main entry point. It accepts configuration via constructor or environment variables and provides access to API endpoints.

```php
$client = new Client($apiToken, $environment);
$records = $client->record->list();
```

### Configuration: `Config`

`Config` is a plain instantiable class — each `Client` creates its own instance, so multiple clients with different tokens coexist without interference. On construction it reads from environment variables:
- `DN_DATOCMS_API_TOKEN` — API token
- `DN_DATOCMS_ENVIRONMENT` — DatoCMS environment name
- `DN_DATOCMS_BASE_URL` — Custom base URL (for proxies)
- `DN_DATOCMS_LOG_LEVEL` — PSR-3 log level

Constructor arguments override env vars. There is no `Config::init()` or `Config::reset()` static method.

### API Layer

All API classes extend `API\Base`, which initializes the HTTP handler. The following API classes are implemented:

**Record API** (`API\Record`):
- `list()`, `retrieve()`, `create()`, `update()`, `delete()`, `duplicate()`
- `publish()`, `unpublish()`, `references()`
- Bulk operations: `publishBulk()`, `unpublishBulk()`, `deleteBulk()`, `moveToStageBulk()`

**Model API** (`API\Model`):
- `list()`, `retrieve()`, `create()`, `update()`, `delete()`, `duplicate()`

**Model Filter API** (`API\ModelFilter`):
- `list()`, `retrieve()`, `create()`, `update()`, `delete()`
- Saved searches to help editors quickly find records within a model

**Upload API** (`API\Upload`):
- `list()`, `retrieve()`, `create()`, `update()`, `delete()`, `references()`
- Bulk operations: `deleteBulk()`, `updateBulk()`
- **Async helpers**: `uploadFile()`, `uploadFromUrl()` — return job payloads
- **Sync helpers**: `uploadFileAndWait()`, `uploadFromUrlAndWait()` — poll until complete

**Upload Support APIs**:
- `API\UploadRequest` — Request S3 upload permissions (`create()`)
- `API\UploadCollection` — Folder management (`list()`, `retrieve()`, `create()`, `update()`, `delete()`)
- `API\UploadTag` — User tag management (`list()`, `retrieve()`, `create()`, `delete()`)
- `API\UploadSmartTag` — Auto-detected tags (`list()` only)

### HTTP Handler

`HTTP\Handler` wraps Guzzle with:
- Automatic retry on HTTP 429 (rate limit) with configurable delay
- Request/response logging via PSR-3 logger
- JSON encoding/decoding with custom exceptions
- Instance caching via `init()` (keyed by token, environment, base_url, log_level, and logger class name)

**Constructor signature**:
```php
public function __construct(
    string $apiToken,
    ?string $environment = null,
    ?LoggerInterface $logger = null,
    string $log_level = LogLevel::INFO,
    ?string $base_url = null,
    ?Client $client = null  // For testing with mock Guzzle client
)
```

**Heads-up**: The `$client` parameter allows injecting a mock Guzzle client for unit testing.

### Value Objects (DataTypes)

All DataType classes extend `DataTypes\Common` and implement `JsonSerializable`. They support:
- **Single values**: `set($value)` — sets a non-localized value
- **Localized values**: `addLocale($locale, $value)` — adds locale-specific values
- **Validation**: Each subclass implements `validateValue()` with specific rules
- **Serialization**: `jsonSerialize()` returns the appropriate format for the API

**Pattern**: Use `::init()` static factory, chain `set()` or `addLocale()`, then pass to `Input\Record->attributes`.

```php
$color = Color::init()->setColor(255, 128, 64, 200);
$record->attributes['brand_color'] = $color;
```

### Input Objects

`Input\Record` represents data for creating/updating records. It extends `Moonspot\ValueObjects\ValueObject` and uses:
- `$type` — Always `'item'` (enforced via setter)
- `$id` — Optional record ID
- `$attributes` — Associative array of field values (can be scalars, arrays, or DataType objects)
- `$meta` — `Parts\Meta` object for metadata
- `$relationships` — `Parts\Relationships` object for item_type and creator

**Serialization**: `toArray()` recursively converts DataType objects via `Export` or `JsonSerializable` interfaces.

`Input\Model` represents data for creating/updating models. It extends `Moonspot\ValueObjects\ValueObject` and uses:
- `$type` — Always `'item_type'` (enforced via setter)
- `$id` — Optional model ID
- `$attributes` — Associative array of model configuration (name, api_key, singleton, etc.)

`Input\ModelFilter` represents data for creating/updating model filters. It extends `Moonspot\ValueObjects\ValueObject` and uses:
- `$type` — Always `'item_type_filter'` (enforced via readonly)
- `$id` — Optional filter ID
- `$attributes` — Filter configuration (name, filter, columns, order_by, shared)
- `$item_type` — `Parts\Relationships\ItemType` object for the associated model

**Note**: `Input\ModelFilter::toArray()` guards against recursive calls from parent by checking if `$data !== null`.

### Parameter Objects

Used for filtering/sorting/paginating API requests.

`Parameters\Record` extends `CommonWithLocale` and includes:
- `$nested` — Include nested data structures
- `$version` — `'published'` or `'current'`
- `$order_by` — `Parts\OrderBy` object
- `$filter` — `Parts\Filter` object (ids, type, query, fields, only_valid)
- `$page` — `Parts\Page` object (offset, limit)

`Parameters\Model` extends `Common` and includes:
- `$page` — `Parts\Page` object (offset, limit)

---

## Coding Conventions

### Style Rules (Must Follow)

- 1TBS bracing style
- snake_case variables
- Protected visibility by default
- Single return point preference
- Class-based API (no bare functions)
- Dependency injection is handled by optional parameters passed to class constructors (unless specified, otherwise)
- Complete PHPDoc coverage

### Testing Patterns

Tests use PHPUnit 11 with attributes:
- `#[Group('unit')]` — Unit tests (always run)
- `#[Group('functional')]` — Functional tests (excluded by default)
- `#[DataProvider('providerName')]` — Data-driven tests

Test files mirror `src/` structure under `tests/`.

### Handler Instance Caching

`HTTP\Handler` caches instances keyed by token, environment, base_url, log_level, and logger class name (sha256 of all five). Tests that construct a `Handler` directly should call `Handler::reset()` in setUp/tearDown to avoid cross-test contamination:

```php
protected function setUp(): void {
    parent::setUp();
    Handler::reset();
}

protected function tearDown(): void {
    parent::tearDown();
    Handler::reset();
}
```

`Config` is a plain class with no static state — just instantiate `new Config()` in tests; no reset needed.

### Mocking HTTP Requests

Use Guzzle's `MockHandler` for testing API operations without hitting real endpoints:

```php
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

$mock = new MockHandler([
    new Response(200, [], '{"data": {"id": "123"}}'),
]);
$handlerStack = HandlerStack::create($mock);
$client = new Client([
    'handler' => $handlerStack,
    'http_errors' => false,  // Important: matches production config
]);

$handler = new Handler('token', null, null, LogLevel::INFO, null, $client);
```

**Heads-up**: Always set `'http_errors' => false` on mock clients to match the production `Handler` configuration.

### Mocking the Handler for API Tests

When testing `API\Record` methods, mock the `Handler` class directly:

```php
$mock_handler = $this->createMock(Handler::class);
$mock_handler->expects($this->once())
    ->method('execute')
    ->with('GET', '/items', [], [])
    ->willReturn(['data' => []]);

$record = new Record($mock_handler);
$result = $record->list();
```

---

## Key Implementation Details

### HTTP Rate Limiting

The `HTTP\Handler` automatically retries requests on HTTP 429 responses:
- Max retries: 5 (see `Handler::MAX_RETRIES`)
- Delay between retries: 3 seconds by default
- Uses Guzzle middleware for transparent retry

### DataType Validation

Each DataType validates input in `validateValue()`:

| DataType | Validation Rules |
|----------|------------------|
| `Scalar` | Must be scalar (string, int, float, bool) or null |
| `Color` | Array with `red`, `green`, `blue`, `alpha` keys, each 0-255 |
| `Location` | Array with `latitude` (-90 to 90), `longitude` (-180 to 180) |
| `Asset` | Requires `upload_id`; optional `title`, `alt`, `focal_point`, `custom_data` |
| `SEO` | Requires all of: `title`, `description`, `image`, `twitter_card`, `no_index` |
| `ExternalVideo` | Requires all of: `provider`, `provider_uid`, `url`, `width`, `height`, `thumbnail_url`, `title` |

### Record Input Structure

When serialized, `Input\Record` produces JSON-API compliant structure:

```php
[
    'type' => 'item',
    'id' => 'optional-id',
    'attributes' => [...],
    'meta' => [...],  // Only if non-empty
    'relationships' => [
        'item_type' => ['data' => ['type' => 'item_type', 'id' => '...']],
        'creator' => ['data' => [...]]  // Only if set
    ]
]
```

### Exception Hierarchy

```
RuntimeException
├── API         — HTTP errors; call getResponseBody() for details
├── Decode      — JSON parse failures; call getRawJson() for original
├── S3Upload    — S3 upload failures; call getResponseBody() for S3 error
├── Timeout     — Job polling timeouts; call getJobId() and getElapsedTime()
└── Unknown     — Unexpected errors (wraps original exception)
```

---

## Testing

### Running Tests

```bash
# Install dependencies
composer install

# Run all unit tests
./vendor/bin/phpunit

# Run with coverage
./vendor/bin/phpunit --coverage-text

# Run specific test file
./vendor/bin/phpunit tests/API/RecordTest.php

# Run only unit tests (excludes functional tests)
./vendor/bin/phpunit --group unit
```

### Current Coverage

As of 2026-03-26, test coverage is at:

| Metric | Coverage |
|--------|----------|
| **Lines** | ~89% |
| **Methods** | ~85% |
| **Classes** | ~84% |

**Classes at 100% coverage**:
- `API\Base`, `API\Record`, `API\Upload` (new sync methods)
- `Client`, `Config`
- All `DataTypes\*` classes
- All `Exception\*` classes (including new `Timeout`)
- All `Input\*` classes
- All `Parameters\*` classes
- `HTTP\Handler` (97% lines; protected methods not directly testable)

**Partial coverage**:
- `DataTypes\Common` (92% lines) — `init()` covered indirectly via subclasses
- `Input\Record` (74% lines) — Exception path covered by parent `ValueObject` class

### Test Data Patterns

Tests use `DataProvider` methods returning arrays of test cases:
- Valid cases: `['description' => [$input, $expected]]`
- Invalid cases: `['description' => [$input, $expectedExceptionMessage]]`

### Test File Naming

Test files follow the pattern `{ClassName}Test.php` and mirror the `src/` directory structure:

| Source File | Test File |
|-------------|-----------|
| `src/Client.php` | `tests/ClientTest.php` |
| `src/API/Record.php` | `tests/API/RecordTest.php` |
| `src/HTTP/Handler.php` | `tests/HTTP/HandlerTest.php` |
| `src/DataTypes/Color.php` | `tests/DataTypes/ColorTest.php` |

### Test Method Naming

Test methods follow the pattern `test{MethodName}{Scenario}`:

```php
public function testListWithoutParameters() { }
public function testListWithParameters() { }
public function testRetrieveThrowsOnInvalidVersion() { }
public function testPublishWithSelectivePublishing() { }
```

---

## Known Limitations

1. **Structured Text**: Not yet implemented in DataTypes
2. **Protected methods in Handler**: `autoRetry()` and `httpLogger()` are protected and cannot be directly unit tested; they are covered indirectly via integration-style tests
3. **Model Filter pagination**: The `list()` method does not support pagination parameters (API limitation)

## Extension Points

### Adding New API Endpoints

1. Create new class in `src/API/` extending `Base`
2. Inject into `Client` as a public readonly property
3. Follow patterns in `API\Record` for method signatures

### Adding New DataTypes

1. Create new class in `src/DataTypes/` extending `Common`
2. Implement `validateValue()` with specific validation rules
3. Optionally add helper method (e.g., `setColor()`, `setLocation()`)
4. Add corresponding tests in `tests/DataTypes/`

### Adding New Parameters

1. Create new class in `src/Parameters/` or `src/Parameters/Parts/`
2. Extend `Common`, `CommonWithLocale`, or `ValueObject`
3. Override `toArray()` to handle special serialization needs

---

## Environment Variables

| Variable | Description | Required |
|----------|-------------|----------|
| `DN_DATOCMS_API_TOKEN` | DatoCMS API token | Yes (or via constructor) |
| `DN_DATOCMS_ENVIRONMENT` | Environment name | No |
| `DN_DATOCMS_BASE_URL` | Custom API base URL | No |
| `DN_DATOCMS_LOG_LEVEL` | PSR-3 log level | No (default: `info`) |

---

## Quick Reference: Common Tasks

### Create a Record

```php
use DealNews\DatoCMS\CMA\Client;
use DealNews\DatoCMS\CMA\Input\Record;
use DealNews\DatoCMS\CMA\DataTypes\Scalar;

$client = new Client($token, $env);
$record = new Record('item-type-id');
$record->attributes['title'] = Scalar::init()->set('Hello World');
$result = $client->record->create($record);
```

### Filter Records

```php
use DealNews\DatoCMS\CMA\Parameters\Record as RecordParams;

$params = new RecordParams();
$params->filter->type = ['article', 'page'];
$params->filter->fields->addField('status', 'published', 'eq');
$params->order_by->addOrderByField('created_at', 'DESC');
$params->page->limit = 50;

$records = $client->record->list($params);
```

### Localized Content

```php
$title = Scalar::init()
    ->addLocale('en', 'English Title')
    ->addLocale('es', 'Título en Español');
$record->attributes['title'] = $title;
```

### Create a Model

```php
use DealNews\DatoCMS\CMA\Client;
use DealNews\DatoCMS\CMA\Input\Model;

$client = new Client($token, $env);
$model = new Model();
$model->attributes['name'] = 'Blog Post';
$model->attributes['api_key'] = 'blog_post';
$model->attributes['singleton'] = false;
$result = $client->model->create($model);
```

### List Models

```php
use DealNews\DatoCMS\CMA\Parameters\Model as ModelParams;

$params = new ModelParams();
$params->page->limit = 50;

$models = $client->model->list($params);
```

### Create a Model Filter

```php
use DealNews\DatoCMS\CMA\Client;
use DealNews\DatoCMS\CMA\Input\ModelFilter;

$client = new Client($token, $env);

// Create a saved filter for draft posts
$filter = new ModelFilter('model-id');
$filter->attributes['name'] = 'Draft posts';
$filter->attributes['filter'] = [
    'query'  => 'foo bar',
    'fields' => ['_status' => ['eq' => 'draft']],
];
$filter->attributes['columns'] = [
    ['name' => '_preview', 'width' => 0.6],
    ['name' => '_status', 'width' => 0.4],
];
$filter->attributes['order_by'] = '_updated_at_ASC';
$filter->attributes['shared'] = true;

$result = $client->model_filter->create($filter);

// List all filters
$filters = $client->model_filter->list();

// Update a filter
$filter->attributes['name'] = 'Updated Name';
$client->model_filter->update('filter-id', $filter);

// Delete a filter
$client->model_filter->delete('filter-id');
```

### Upload a File

```php
use DealNews\DatoCMS\CMA\Client;

$client = new Client($token, $env);

// ============================================================
// ASYNC: Returns job payload immediately, poll manually
// ============================================================

// Simple async upload (returns job)
$job = $client->upload->uploadFile('/path/to/image.jpg');
$job_id = $job['data']['id'];
// ... poll $client->job->retrieve($job_id) until complete ...

// Async upload with metadata (returns job)
$job = $client->upload->uploadFile('/path/to/image.jpg', [
    'author'    => 'John Doe',
    'copyright' => '© 2025',
    'tags'      => ['banner', 'hero'],
    'default_field_metadata' => [
        'en' => ['alt' => 'Banner image', 'title' => 'Hero Banner'],
        'es' => ['alt' => 'Imagen de banner', 'title' => 'Banner Principal'],
    ],
]);

// Async upload from URL (returns job)
$job = $client->upload->uploadFromUrl('https://example.com/image.jpg');

// ============================================================
// SYNC: Waits for job completion, returns upload payload
// ============================================================

// Simple sync upload (waits up to 30 seconds by default)
$upload = $client->upload->uploadFileAndWait('/path/to/image.jpg');
$upload_id = $upload['data']['id']; // Ready to use!

// Sync upload with custom timeout
$upload = $client->upload->uploadFileAndWait(
    '/path/to/image.jpg',
    ['author' => 'Jane'],
    'collection-id',
    60  // 60 second timeout
);

// Sync upload from URL
$upload = $client->upload->uploadFromUrlAndWait(
    'https://example.com/image.jpg',
    'custom-name.jpg',  // Optional filename
    ['tags' => ['hero']],
    null,               // No collection
    45                  // 45 second timeout
);

// Handle timeout exceptions
try {
    $upload = $client->upload->uploadFileAndWait('/large-file.mp4', null, null, 30);
} catch (\DealNews\DatoCMS\CMA\Exception\Timeout $e) {
    echo "Timeout after " . $e->getElapsedTime() . " seconds\n";
    echo "Job ID: " . $e->getJobId() . "\n";
    // Could continue polling manually with $client->job->retrieve($e->getJobId())
}
```

### List and Filter Uploads

```php
use DealNews\DatoCMS\CMA\Parameters\Upload as UploadParams;

$params = new UploadParams();

// ============================================================
// Direct property filtering (simple exact matches)
// ============================================================
$params->filter->type = 'image';
$params->filter->query = 'banner';
$params->filter->tags = ['hero', 'featured'];
$params->filter->author = 'John Doe';
$params->filter->copyright = '© 2025';

// ============================================================
// Field-level filtering (with operators)
// ============================================================
$params->filter->fields->addField('width', 1000, 'gte');
$params->filter->fields->addField('height', 500, 'gte');
$params->filter->fields->addField('size', 5000000, 'lt');
$params->filter->fields->addField('author', 'John%', 'matches');
$params->filter->fields->addField('created_at', '2025-01-01', 'gt');
$params->filter->fields->addField('is_image', true, 'eq');

// ============================================================
// Combine both patterns
// ============================================================
$params->filter->type = 'image';  // Direct property
$params->filter->fields->addField('width', 1920, 'gte');  // With operator

// Other parameters
$params->order_by->addOrderByField('created_at', 'DESC');
$params->page->limit = 25;

$uploads = $client->upload->list($params);
```

**Available field operators**:
- `eq` - Equal to
- `neq` - Not equal to
- `lt` - Less than
- `lte` - Less than or equal
- `gt` - Greater than
- `gte` - Greater than or equal
- `matches` - Pattern matching (use `%` as wildcard)
- `exists` - Field exists/not exists
- `in` - Value in list
- `not_in` - Value not in list

### Manage Upload Collections

```php
use DealNews\DatoCMS\CMA\Input\UploadCollection;

// Create a folder
$collection = new UploadCollection();
$collection->attributes['label'] = 'Product Images';
$result = $client->upload_collection->create($collection);

// Upload to a specific folder
$upload = $client->upload->uploadFile('/path/to/product.jpg', null, $result['data']['id']);
```
```

---

## Changelog Integration

When making changes, update the README.md if:
- Adding new DataTypes
- Adding new API endpoints
- Changing public interfaces

No separate CHANGELOG file exists; version history is tracked via git.
