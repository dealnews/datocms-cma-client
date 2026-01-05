<?php

namespace DealNews\DatoCMS\CMA\Input;

use Moonspot\ValueObjects\ValueObject;

/**
 * Input object for creating and updating DatoCMS upload collections
 *
 * Upload collections are folders used to organize uploads in the Media Area.
 *
 * Usage:
 * ```php
 * $collection = new UploadCollection();
 * $collection->attributes['label'] = 'Product Images';
 * $result = $client->upload_collection->create($collection);
 * ```
 *
 * @see https://www.datocms.com/docs/content-management-api/resources/upload-collection
 */
class UploadCollection extends ValueObject {

    /**
     * Optional collection ID (used for updates)
     *
     * @var string|null
     */
    public ?string $id = null;

    /**
     * Resource type, always "upload_collection"
     *
     * Enforced by setter - attempting to set any other value throws an exception.
     *
     * @var string
     */
    public string $type = 'upload_collection' {
        set {
            if ($value !== 'upload_collection') {
                throw new \InvalidArgumentException('Type must be "upload_collection"');
            }
            $this->type = $value;
        }
    }

    /**
     * Collection attributes
     *
     * - label: Required. Display name for the collection
     *
     * @var array<string, mixed>
     */
    public array $attributes = [];

    /**
     * Optional parent collection ID for nesting
     *
     * @var string|null
     */
    public ?string $parent_id = null;

    /**
     * Converts the collection to an array for API submission
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed> API-ready array structure
     */
    public function toArray(?array $data = null): array {
        $array = [
            'type'       => $this->type,
            'attributes' => $this->attributes,
        ];

        if (!is_null($this->id)) {
            $array['id'] = $this->id;
        }

        if (!is_null($this->parent_id)) {
            $array['relationships'] = [
                'parent' => [
                    'data' => [
                        'type' => 'upload_collection',
                        'id'   => $this->parent_id,
                    ],
                ],
            ];
        }

        return $array;
    }
}
