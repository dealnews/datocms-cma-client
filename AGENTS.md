# AGENTS.md — DatoCMS CMA Client

> **Purpose**: This document provides AI agents with comprehensive context about this PHP library. Use it to understand architecture, conventions, and how to make effective contributions.

---

## Project Overview

**What it is**: An unofficial PHP client library for the [DatoCMS Content Management API](https://www.datocms.com/docs/content-management-api). It provides a structured, type-safe way to interact with DatoCMS for managing records/items.

**Namespace**: `DealNews\DatoCMS\CMA`

**PHP Version**: Requires PHP 8.4+

**Key Dependencies**:
- `guzzlehttp/guzzle` ^7.10 — HTTP client
- `moonspot/value-objects` ^2.3 — Base class for value objects with `toArray()` support
- `psr/log` ^3.0 — PSR-3 logging interface

---

## Directory Structure

```
src/
├── API/                    # API endpoint handlers
│   ├── Base.php           # Abstract base for all API classes
│   ├── Model.php          # Model/item-type CRUD operations (6 methods)
│   └── Record.php         # Record/item CRUD operations (13 methods)
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
│   └── Unknown.php        # Unexpected errors
├── HTTP/
│   └── Handler.php        # Guzzle wrapper with auto-retry on 429
├── Input/                 # Objects for create/update operations
│   ├── Model.php          # Main input object for models
│   ├── Record.php         # Main input object for records
│   └── Parts/             # Sub-components
│       ├── Meta.php       # Record metadata (created_at, stage, etc.)
│       ├── Relationships.php
│       └── Relationships/
│           ├── Creator.php
│           └── ItemType.php
├── Parameters/            # Query parameter objects for API filtering
│   ├── Common.php         # Abstract base with pagination
│   ├── CommonWithLocale.php
│   ├── Model.php          # Parameters for listing models
│   ├── Record.php         # Parameters for listing records
│   └── Parts/
│       ├── Filter.php
│       ├── FilterFields.php
│       ├── OrderBy.php
│       └── Page.php
├── Client.php             # Main entry point
└── Config.php             # Singleton configuration

tests/
├── API/                   # Unit tests for API classes
│   ├── BaseTest.php       # Tests Base constructor with mocked Handler
│   ├── ModelTest.php      # Tests all 6 Model API methods
│   └── RecordTest.php     # Tests all 13 Record API methods
├── DataTypes/             # Unit tests for DataType classes
│   └── CommonTest.php     # Tests abstract Common class edge cases
├── Exception/             # Unit tests for exception classes
│   ├── APITest.php
│   ├── DecodeTest.php
│   └── UnknownTest.php
├── HTTP/                  # Unit tests for HTTP layer
│   └── HandlerTest.php    # Tests execute(), retry logic, caching
├── Input/                 # Unit tests for Input classes
│   ├── ModelTest.php      # Tests Model input serialization
│   └── RecordTest.php     # Tests Record input serialization
├── Parameters/            # Unit tests for Parameter classes
│   ├── ModelTest.php      # Tests Model parameters
│   └── RecordTest.php     # Tests Record parameters with version validation
├── ClientTest.php         # Tests Client constructor and config integration
├── ConfigTest.php         # Tests singleton, env vars, magic methods
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

### Configuration: `Config` (Singleton)

Configuration is managed via a singleton that reads from environment variables:
- `DN_DATOCMS_API_TOKEN` — API token
- `DN_DATOCMS_ENVIRONMENT` — DatoCMS environment name
- `DN_DATOCMS_BASE_URL` — Custom base URL (for proxies)
- `DN_DATOCMS_LOG_LEVEL` — PSR-3 log level

### API Layer

All API classes extend `API\Base`, which initializes the HTTP handler.

**`API\Record`** — Record/item operations:
- `list()`, `retrieve()`, `create()`, `update()`, `delete()`, `duplicate()`
- `publish()`, `unpublish()`, `references()`
- Bulk operations: `publishBulk()`, `unpublishBulk()`, `deleteBulk()`, `moveToStageBulk()`

**`API\Model`** — Model/item-type operations:
- `list()`, `retrieve()`, `create()`, `update()`, `delete()`, `duplicate()`

### HTTP Handler

`HTTP\Handler` wraps Guzzle with:
- Automatic retry on HTTP 429 (rate limit) with configurable delay
- Request/response logging via PSR-3 logger
- JSON encoding/decoding with custom exceptions
- Instance caching via `init()` (keyed by token, environment, base_url)

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

1. **Brace style**: 1TBS (opening brace on same line)
2. **Variables/properties**: `snake_case`
3. **Line length**: Should not exceed 80 characters
4. **Arrays**: Short syntax only (`[]` not `array()`)
5. **Type declarations**: Use for all parameters and return types
6. **Visibility**: Use `protected` over `private` unless instructed otherwise
7. **Single return point**: Prefer one return statement per method
8. **No pass-by-reference**: Avoid `&$param` in function signatures

### PHPDoc Requirements

All classes and public methods should have docblocks. Include:
- `@param` with type and description
- `@return` with type and description
- `@throws` for any exceptions
- `@see` for external documentation links

### Testing Patterns

Tests use PHPUnit 11 with attributes:
- `#[Group('unit')]` — Unit tests (always run)
- `#[Group('functional')]` — Functional tests (excluded by default)
- `#[DataProvider('providerName')]` — Data-driven tests

Test files mirror `src/` structure under `tests/`.

### Singleton Reset Methods

Both `Config` and `HTTP\Handler` use singleton/instance caching patterns. For testing, use the `reset()` methods to clear cached state between tests:

```php
protected function setUp(): void {
    parent::setUp();
    Config::reset();      // Clears Config singleton
    Handler::reset();     // Clears Handler instance cache
}

protected function tearDown(): void {
    parent::tearDown();
    Config::reset();
    Handler::reset();
}
```

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

As of 2025-12-22, test coverage is at:

| Metric | Coverage |
|--------|----------|
| **Lines** | 98.08% (459/468) |
| **Methods** | 93.85% (61/65) |
| **Classes** | 88.00% (22/25) |

**Classes at 100% coverage**:
- `API\Base`, `API\Record`
- `Client`, `Config`
- All `DataTypes\*` classes
- All `Exception\*` classes
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
2. **Upload API**: Not implemented (only record management)
3. **Fields API**: Not implemented (cannot manage fields within models)
4. **Webhooks**: Not implemented
5. **Protected methods in Handler**: `autoRetry()` and `httpLogger()` are protected and cannot be directly unit tested; they are covered indirectly via integration-style tests

---

## Bug Fixes Made During Testing

### Handler::init() Parameter Mismatch (Fixed)

The `Handler::init()` static method was passing `$base_url` as the 3rd constructor argument, but the constructor expects `$logger` as the 3rd argument. This caused a `TypeError` when using `init()` with a custom base URL.

**Fix**: Updated `init()` to pass parameters in correct order:
```php
// Before (broken)
new self($apiToken, $environment, $base_url);

// After (fixed)
new self($apiToken, $environment, null, LogLevel::INFO, $base_url);
```

---

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

---

## Changelog Integration

When making changes, update the README.md if:
- Adding new DataTypes
- Adding new API endpoints
- Changing public interfaces

No separate CHANGELOG file exists; version history is tracked via git.
