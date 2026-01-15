<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\UploadCollection;

use DealNews\DatoCMS\CMA\Input\Parts\Relationships\UploadCollection as UploadCollectionRelationship;
use Moonspot\ValueObjects\ValueObject;

/**
 * Relationships container for UploadCollection Input
 *
 * Contains parent and children collection relationships.
 * According to DatoCMS API:
 * - parent: Can be set when creating, optional when updating
 * - children: Optional when updating only, not when creating
 */
class Relationships extends ValueObject {

    /**
     * Parent collection relationship (optional)
     *
     * @var UploadCollectionRelationship|null
     */
    public ?UploadCollectionRelationship $parent = null;

    /**
     * Children collection relationships (array)
     *
     * @var array<UploadCollectionRelationship>
     */
    public array $children = [];

    /**
     * Constructor - initializes parent relationship
     */
    public function __construct() {
        $this->parent = new UploadCollectionRelationship();
    }

    /**
     * Convert relationships to API array format
     *
     * @param array<string, mixed>|null $data Optional data override
     *
     * @return array<string, mixed>
     */
    public function toArray(?array $data = null): array {
        $array = [];

        // Add parent if id is set
        if (!empty($this->parent->id)) {
            $array['parent'] = $this->parent->toArray();
        }

        // Add children if any exist
        if (!empty($this->children)) {
            $array['children'] = [
                'data' => array_map(function ($child) {
                    return $child->toArray()['data'];
                }, $this->children),
            ];
        }

        return $array;
    }
}
