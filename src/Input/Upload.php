<?php

namespace DealNews\DatoCMS\CMA\Input;

use DealNews\DatoCMS\CMA\Input\Parts\Upload\Attributes;
use DealNews\DatoCMS\CMA\Input\Parts\Upload\Relationships;
use Moonspot\ValueObjects\ValueObject;

/**
 * Input object for creating and updating DatoCMS uploads
 *
 * Represents the data structure for upload operations. The upload workflow
 * in DatoCMS requires first requesting an upload permission, uploading to S3,
 * then registering the upload with this input object.
 *
 * Usage:
 * ```php
 * $upload = new Upload();
 * $upload->attributes->path = '/45/1496845848-image.jpg';
 * $upload->attributes->author = 'John Doe';
 * $upload->attributes->tags = ['banner', 'hero'];
 * $upload->attributes->default_field_metadata->addLocale('en', 'Alt text', 'Title');
 * $upload->relationships->upload_collection->id = 'collection-id';
 * $result = $client->upload->create($upload);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/upload/create
 */
class Upload extends ValueObject {

    /**
     * Optional upload ID (used for updates)
     *
     * @var string|null
     */
    public ?string $id = null;

    /**
     * Resource type, always "upload"
     *
     * Enforced by setter - attempting to set any other value throws an exception.
     *
     * @var string
     */
    public string $type = 'upload' {
        set {
            if ($value !== 'upload') {
                throw new \InvalidArgumentException('Type must be "upload"');
            }
            $this->type = $value;
        }
    }

    /**
     * Upload attributes (path, copyright, author, notes, tags, metadata)
     *
     * @var Attributes
     */
    public Attributes $attributes;

    /**
     * Upload relationships (upload_collection and creator)
     *
     * @var Relationships
     */
    public Relationships $relationships;

    /**
     * Creates a new Upload input object
     */
    public function __construct() {
        $this->attributes = new Attributes();
        $this->relationships = new Relationships();
    }

    /**
     * Converts the upload to an array for API submission
     *
     * Formats the data according to JSON:API spec with optional relationships.
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> API-ready array structure
     */
    public function toArray(?array $data = null): array {
        $array = [
            'type'       => $this->type,
            'attributes' => $this->attributes->toArray(),
        ];

        if (!is_null($this->id)) {
            $array['id'] = $this->id;
        }

        $relationships = $this->relationships->toArray();
        if (!empty($relationships)) {
            $array['relationships'] = $relationships;
        }

        return $array;
    }
}
