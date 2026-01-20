<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Field;

/**
 * A class to describe validators for fields
 *
 * Usage:
 * ```php
 *   $field = new Field();
 *
 *   $attributes = new Attributes();
 *   $attributes->validators = Validators::init()->isRequired()->isUnique();
 *
 *   $field->attributes = $attributes;
 * ```
 *
 * @suppress PhanRedefinedInheritedInterface
 */
class Validators implements \JsonSerializable {


    /**
     * Value for field must be specified or it won't be valid.
     *
     * @var bool
     */
    protected bool $required = false;

    /**
     * The value of the field must be unique across the whole collection of records.
     *
     * @var bool
     */
    protected bool $unique = false;

    /**
     * Accept dates only inside a specified date range.
     *
     * @var array<string, string>
     */
    protected array $date_range = [];

    /**
     * Accept date times only inside a specified date time range.
     *
     * @var array<string, string>
     */
    protected array $date_time_range = [];

    /**
     * Only accept a specific set of values
     *
     * @var string[]
     */
    protected array $enum = [];

    /**
     * Only accept assets with specific file extensions.
     *
     * @var array<string, array<string>|string>
     */
    protected array $extension = [];

    /**
     * Accept assets only inside a specified date range.
     *
     * @var array<string, string|int>
     */
    protected array $file_size = [];

    /**
     * Accepts only strings that match a specified format.
     *
     * @var array<string, string>
     */
    protected array $format = [];

    /**
     * Only accept slugs having a specific format.
     *
     * @var array<string, string>
     */
    protected array $slug_format = [];

    /**
     * Accept assets only within a specified height and width range.
     *
     * @var array<string, int>
     */
    protected array $image_dimensions = [];

    /**
     * Accept assets only within a specified aspect ratio range.
     *
     * @var array<string, int>
     */
    protected array $image_aspect_ratio = [];

    /**
     * Only accept references to records of the specified models. (When field only allows one reference)
     *
     * @var array<string, string|array<string>>
     */
    protected array $item_item_type = [];

    /**
     * Only accept references to records of the specified models. (When field allows multiple references)
     *
     * @var array<string, string|array<string>>
     */
    protected array $items_item_type = [];

    /**
     * Accept strings only with a specified number of characters
     *
     * @var array<string, int>
     */
    protected array $length = [];

    /**
     * Accept numbers only inside a specified range.
     *
     * @var array<string, int>
     */
    protected array $number_range = [];

    /**
     * Assets contained in the field are required to specify custom title or alternate text, or they won't be valid.
     *
     * @var array<string, bool>
     */
    protected array $required_alt_title = [];

    /**
     * SEO field has to specify one or more properties, or it won't be valid.
     *
     * @var array<string, bool>
     */
    protected array $required_seo_fields = [];

    /**
     * Limits the length of the title for a SEO field.
     *
     * @var array<string, int>
     */
    protected array $title_length = [];

    /**
     * Limits the length of the description for a SEO field.
     *
     * @var array<string, int>
     */
    protected array $description_length = [];

    /**
     * Only accept references to block records of the specified block models (Modular Content fields).
     *
     * @var array<string, array<string>>
     */
    protected array $rich_text_blocks = [];

    /**
     * Only accept references to block records of the specified block models (Single Block fields).
     *
     * @var array<string, array<string>>
     */
    protected array $single_block_blocks = [];

    /**
     * Checks for the presence of malicious code in HTML fields.
     *
     * @var array<string, bool>
     */
    protected array $sanitized_html = [];

    /**
     * Only accept references to block records of the specified block models (Structured Text fields).
     *
     * @var array<string, array<string>>
     */
    protected array $structured_text_blocks = [];

    /**
     * Only accept references to inline block records of the specified block models (Structured Text fields).
     *
     * @var array<string, array<string>>
     */
    protected array $structured_text_inline_blocks = [];

    /**
     * Only accept itemLink/inlineItem nodes for records of specified models (Structured Text fields).
     *
     * @var array<string, string|array<string>>
     */
    protected array $structured_text_links = [];

    /**
     * Only accept a number of items within specified range.
     *
     * @var array<string, int>
     */
    protected array $size = [];

    /**
     * Specifies the ID of the Single-line string field used to generate the slug.
     *
     * @var array<string, string>
     */
    protected array $slug_title_field = [];

    /**
     * Factory method for creating a new instance
     *
     * @return static New instance of the concrete class
     */
    public static function init(): static {
        return new static();
    }

    /**
     * Call this method when you want to require the field
     *
     * @return static
     */
    public function isRequired(): static {
        $this->required = true;

        return $this;
    }

    /**
     * Call this method when you want to set the field as "unique"
     *
     * Unique means: The value of the field must be unique across the whole collection of records.
     *
     * @return static
     */
    public function isUnique(): static {
        $this->unique = true;

        return $this;
    }

    /**
     * Set a date range that the field's value must be inside
     *
     * At least one of the parameters must be specified.
     *
     * @param   string|null     $min    ISO 8601 minimum date (Optional)
     * @param   string|null     $max    ISO 8601 maximum date (Optional)
     *
     * @return  static
     */
    public function setDateRange(?string $min = null, ?string $max = null): static {
        if (!empty($min)) {
            $this->date_range['min'] = $min;
        }
        if (!empty($max)) {
            $this->date_range['max'] = $max;
        }

        return $this;
    }

    /**
     * Set a date-time range that the field's value must be inside
     *
     * At least one of the parameters must be specified.
     *
     * @param   string|null     $min    ISO 8601 minimum date-time (Optional)
     * @param   string|null     $max    ISO 8601 maximum date-time (Optional)
     *
     * @return  static
     */
    public function setDateTimeRange(?string $min = null, ?string $max = null): static {
        if (!empty($min)) {
            $this->date_time_range['min'] = $min;
        }
        if (!empty($max)) {
            $this->date_time_range['max'] = $max;
        }

        return $this;
    }

    /**
     * Set an enum of allowed values
     *
     * @param   string[]    $enum   Allowed values
     *
     * @return  static
     */
    public function setEnum(array $enum): static {
        $this->enum = $enum;

        return $this;
    }


    /**
     * Set an allowed list of file extensions that this field will
     * allow for assets.
     *
     * Notice: Setting a list of file extensions will override any "FileType" validations you may have set.
     *
     * @param   string[]    $extension    Set of allowed file extensions
     *
     * @return  static
     */
    public function setFileExtensions(array $extension): static {
        $this->extension               = [];
        $this->extension['extensions'] = $extension;

        return $this;
    }

    /**
     * Set a specific file type that this field will allow for assets.
     *
     * In DatoCMS documentation, this is the same as setting a "predefined list" of allowed file extensions.
     *
     * Notice: Setting a file type will override any "FileExtensions" validations you may have set.
     *
     * @param   string  $type
     *
     * @return  static
     */
    public function setFileType(string $type): static {
        $allowed_types = ['image', 'transformable_image', 'video', 'document'];
        if (!in_array($type, $allowed_types)) {
            throw new \InvalidArgumentException('Invalid file type');
        }
        $this->extension                    = [];
        $this->extension['predefined_list'] = $type;

        return $this;
    }


    /**
     * Set a minimum file size that this field will allow for assets.
     *
     * @param   int     $min    Minimum file size
     * @param   string  $unit   Unit of measurement for minimum file size (B, KB, or MB)
     *
     * @return  static
     */
    public function setFileSizeMin(int $min, string $unit): static {
        $allowed_units = ['B', 'KB', 'MB'];
        if (!in_array($unit, $allowed_units)) {
            throw new \InvalidArgumentException('Invalid file size unit');
        }
        $this->file_size['min_value'] = $min;
        $this->file_size['min_unit']  = $unit;

        return $this;
    }

    /**
     * Set a maximum file size that this field will allow for assets.
     *
     * @param   int     $max    Maximum file size
     * @param   string  $unit   Unit of measurement for maximum file size (B, KB, or MB)
     *
     * @return  static
     */
    public function setFileSizeMax(int $max, string $unit): static {
        $allowed_units = ['B', 'KB', 'MB'];
        if (!in_array($unit, $allowed_units)) {
            throw new \InvalidArgumentException('Invalid file size unit');
        }
        $this->file_size['max_value'] = $max;
        $this->file_size['max_unit']  = $unit;

        return $this;
    }


    /**
     * Set a custom regex pattern that a field's value must match (its format).
     *
     * Notice: If you set a custom regex pattern, it will override any pre-defined format (email or url) you may have set on this field.
     *
     * @param   string       $pattern       Custom regular expression for validation
     * @param   string|null  $description   Can be provided to serve as a hint for the user instead of the default auto-generated hint
     *
     * @return  static
     */
    public function setCustomFormat(string $pattern, ?string $description = null): static {
        $this->format                   = [];
        $this->format['custom_pattern'] = $pattern;
        if (!empty($description)) {
            $this->format['description'] = $description;
        }

        return $this;
    }


    /**
     * Specifies a pre-defined format (email or URL)
     *
     * Notice: If you set a predefined pattern/format, it will override any custom regex pattern you may have set on this field.
     *
     * @param   string  $format    Pre-defined format type (email or url)
     *
     * @return  static
     */
    public function setPredefinedFormat(string $format): static {
        if ($format !== 'email' && $format !== 'url') {
            throw new \InvalidArgumentException('Invalid format');
        }
        $this->format                       = [];
        $this->format['predefined_pattern'] = $format;

        return $this;
    }


    /**
     * Set a custom regex pattern for a slug format
     *
     * Notice: If you set a custom regex pattern, it will override any pre-defined format (webpage_slug) you may have set on this field.
     *
     * @param   string       $pattern       Custom regular expression for validation
     *
     * @return  static
     */
    public function setSlugCustomFormat(string $pattern): static {
        $this->slug_format                   = [];
        $this->slug_format['custom_pattern'] = $pattern;

        return $this;
    }

    /**
     * Set a predefined slug format
     *
     * @param   string       $format        Pre-defined format type (webpage_slug)
     *
     * @return  static
     */
    public function setSlugPredefinedFormat(string $format): static {
        if ($format !== 'webpage_slug') {
            throw new \InvalidArgumentException('Invalid format');
        }
        $this->slug_format                       = [];
        $this->slug_format['predefined_pattern'] = $format;

        return $this;
    }

    /**
     * Accept assets only within a specified height and width range.
     *
     * At least one pair of height/width parameters must be specified.
     *
     * @param   int|null     $width_min_value       Minimum width value
     * @param   int|null     $width_max_value       Maximum width value
     * @param   int|null     $height_min_value      Minimum height value
     * @param   int|null     $height_max_value      Maximum height value
     *
     * @return  static
     */
    public function setImageDimensions(?int $width_min_value = null, ?int $width_max_value = null, ?int $height_min_value = null, ?int $height_max_value = null): static {
        if ($width_min_value !== null) {
            $this->image_dimensions['width_min_value'] = $width_min_value;
        }
        if ($width_max_value !== null) {
            $this->image_dimensions['width_max_value'] = $width_max_value;
        }
        if ($height_min_value !== null) {
            $this->image_dimensions['height_min_value'] = $height_min_value;
        }
        if ($height_max_value !== null) {
            $this->image_dimensions['height_max_value'] = $height_max_value;
        }

        return $this;
    }

    /**
     * Accept assets only within a specified aspect ratio range.
     *
     * At least one pair of numerator/denominator must be specified.
     *
     * @param   int|null     $min_ar_numerator        Numerator part of the minimum aspect ratio
     * @param   int|null     $min_ar_denominator      Denominator part of the minimum aspect ratio
     * @param   int|null     $eq_ar_numerator         Numerator part for the required aspect ratio
     * @param   int|null     $eq_ar_denominator       Denominator part for the required aspect ratio
     * @param   int|null     $max_ar_numerator        Numerator part of the maximum aspect ratio
     * @param   int|null     $max_ar_denominator      Denominator part of the maximum aspect ratio
     *
     * @return  static
     */
    public function setImageAspectRatio(?int $min_ar_numerator = null, ?int $min_ar_denominator = null, ?int $eq_ar_numerator = null, ?int $eq_ar_denominator = null, ?int $max_ar_numerator = null, ?int $max_ar_denominator = null): static {
        if ($min_ar_numerator !== null) {
            $this->image_aspect_ratio['min_ar_numerator'] = $min_ar_numerator;
        }
        if ($min_ar_denominator !== null) {
            $this->image_aspect_ratio['min_ar_denominator'] = $min_ar_denominator;
        }
        if ($eq_ar_numerator !== null) {
            $this->image_aspect_ratio['eq_ar_numerator'] = $eq_ar_numerator;
        }
        if ($eq_ar_denominator !== null) {
            $this->image_aspect_ratio['eq_ar_denominator'] = $eq_ar_denominator;
        }
        if ($max_ar_numerator !== null) {
            $this->image_aspect_ratio['max_ar_numerator'] = $max_ar_numerator;
        }
        if ($max_ar_denominator !== null) {
            $this->image_aspect_ratio['max_ar_denominator'] = $max_ar_denominator;
        }

        return $this;
    }

    /**
     * Set of allowed model IDs when only accepting references to records of the specified models. (When field only allows one reference)
     *
     * @param   string[]    $item_types     Set of allowed model IDs
     *
     * @return  static
     */
    public function setItemTypesSingle(array $item_types): static {
        $this->item_item_type['item_types'] = $item_types;

        return $this;
    }

    /**
     * Set strategy to apply when a publishing is requested and this field references some unpublished records (When field only allows one reference)
     *
     * Notice: Must have item_types set with setItemTypesSingle() method
     *
     * Possible strategy values:
     *  - "fail": Fail the operation and notify the user
     *  - "publish_references": Publish also the referenced records
     *
     * @param   string      $strategy       Strategy to use when publishing with unpublished references (fail or publish_references)
     *
     * @return  static
     */
    public function setItemTypesSingleOnPublishStrategy(string $strategy): static {
        if ($strategy !== 'fail' && $strategy !== 'publish_references') {
            throw new \InvalidArgumentException('Invalid strategy');
        }
        $this->item_item_type['on_publish_with_unpublished_references_strategy'] = $strategy;

        return $this;
    }

    /**
     * Set strategy to apply when unpublishing is requested for a record referenced by this field (When field only allows one reference)
     *
     * Notice: Must have item_types set with setItemTypesSingle() method
     *
     * Possible strategy values:
     *  - "fail": Fail the operation and notify the user
     *  - "unpublish": Unpublish also this record
     *  - "delete_references": Try to remove the reference to the unpublished record (if the field has a required validation it will fail)
     *
     * @param   string      $strategy       Strategy to use when publishing with unpublished references (fail, unpublish or delete_references)
     *
     * @return  static
     */
    public function setItemTypesSingleOnUnpublishStrategy(string $strategy): static {
        if ($strategy !== 'fail' && $strategy !== 'unpublish' && $strategy !== 'delete_references') {
            throw new \InvalidArgumentException('Invalid strategy');
        }
        $this->item_item_type['on_reference_unpublish_strategy'] = $strategy;

        return $this;
    }

    /**
     * Set strategy to apply when deletion is requested for a record referenced by this field (When field only allows one reference)
     *
     * Notice: Must have item_types set with setItemTypesSingle() method
     *
     * Possible strategy values:
     *  - "fail": Fail the operation and notify the user
     *  - "delete_references": Try to remove the reference to the deleted record (if the field has a required validation it will fail)
     *
     * @param   string      $strategy       Strategy to use when deleting references (fail or delete_references)
     *
     * @return  static
     */
    public function setItemTypesSingleOnDeleteStrategy(string $strategy): static {
        if ($strategy !== 'fail' && $strategy !== 'delete_references') {
            throw new \InvalidArgumentException('Invalid strategy');
        }
        $this->item_item_type['on_reference_delete_strategy'] = $strategy;

        return $this;
    }

    /**
     * Set of allowed model IDs when only accepting references to records of the specified models. (When field allows multiple references)
     *
     * @param   string[]    $item_types     Set of allowed model IDs
     *
     * @return  static
     */
    public function setItemTypesMultiple(array $item_types): static {
        $this->items_item_type['item_types'] = $item_types;

        return $this;
    }

    /**
     * Set strategy to apply when a publishing is requested and this field references some unpublished records (When field allows multiple references)
     *
     * Notice: Must have item_types set with setItemTypesMultiple() method
     *
     * Possible strategy values:
     *  - "fail": Fail the operation and notify the user
     *  - "publish_references": Publish also the referenced records
     *
     * @param   string      $strategy       Strategy to use when publishing with unpublished references (fail or publish_references)
     *
     * @return  static
     */
    public function setItemTypesMultipleOnPublishStrategy(string $strategy): static {
        if ($strategy !== 'fail' && $strategy !== 'publish_references') {
            throw new \InvalidArgumentException('Invalid strategy');
        }
        $this->items_item_type['on_publish_with_unpublished_references_strategy'] = $strategy;

        return $this;
    }

    /**
     * Set strategy to apply when unpublishing is requested for a record referenced by this field (When field allows multiple references)
     *
     * Notice: Must have item_types set with setItemTypesMultiple() method
     *
     * Possible strategy values:
     *  - "fail": Fail the operation and notify the user
     *  - "unpublish": Unpublish also this record
     *  - "delete_references": Try to remove the reference to the unpublished record (if the field has a required validation it will fail)
     *
     * @param   string      $strategy       Strategy to use when publishing with unpublished references (fail, unpublish or delete_references)
     *
     * @return  static
     */
    public function setItemTypesMultipleOnUnpublishStrategy(string $strategy): static {
        if ($strategy !== 'fail' && $strategy !== 'unpublish' && $strategy !== 'delete_references') {
            throw new \InvalidArgumentException('Invalid strategy');
        }
        $this->items_item_type['on_reference_unpublish_strategy'] = $strategy;

        return $this;
    }

    /**
     * Set strategy to apply when deletion is requested for a record referenced by this field (When field allows multiple references)
     *
     * Notice: Must have item_types set with setItemTypesMultiple() method
     *
     * Possible strategy values:
     *  - "fail": Fail the operation and notify the user
     *  - "delete_references": Try to remove the reference to the deleted record (if the field has a required validation it will fail)
     *
     * @param   string      $strategy       Strategy to use when deleting references (fail or delete_references)
     *
     * @return  static
     */
    public function setItemTypesMultipleOnDeleteStrategy(string $strategy): static {
        if ($strategy !== 'fail' && $strategy !== 'delete_references') {
            throw new \InvalidArgumentException('Invalid strategy');
        }
        $this->items_item_type['on_reference_delete_strategy'] = $strategy;

        return $this;
    }

    /**
     * Accept strings only with a specified number of characters
     *
     * @param   int|null    $min            Minimum length
     * @param   int|null    $max            Maximum length
     *
     * @return  static
     */
    public function setLengthRange(?int $min = null, ?int $max = null): static {
        if ($min !== null) {
            $this->length['min'] = $min;
        }
        if ($max !== null) {
            $this->length['max'] = $max;
        }

        return $this;
    }

    /**
     * Accept strings only with a specified number of characters
     *
     * @param   int     $equal            Exact length
     *
     * @return  static
     */
    public function setLength(int $equal): static {
        $this->length['eq'] = $equal;

        return $this;
    }

    /**
     * Accept numbers only inside a specified range.
     *
     * @param   int|null    $min            Minimum value
     * @param   int|null    $max            Maximum value
     *
     * @return  static
     */
    public function setNumberRange(?int $min = null, ?int $max = null): static {
        if ($min !== null) {
            $this->number_range['min'] = $min;
        }
        if ($max !== null) {
            $this->number_range['max'] = $max;
        }

        return $this;
    }

    /**
     * Whether the alternate text for the asset must be specified. Calling this method will set it to "true"
     *
     * @return  static
     */
    public function requiresAlt(): static {
        $this->required_alt_title['alt'] = true;

        return $this;
    }

    /**
     * Whether the title for the asset must be specified. Calling this method will set it to "true"
     *
     * @return static
     */
    public function requiresTitle(): static {
        $this->required_alt_title['title'] = true;

        return $this;
    }

    /**
     * Whether the meta title must be specified. Calling this method will set it to "true"
     *
     * @return static
     */
    public function requiresSEOTitle(): static {
        $this->required_seo_fields['title'] = true;

        return $this;
    }

    /**
     * Whether the meta description must be specified. Calling this method will set it to "true"
     *
     * @return static
     */
    public function requiresSEODescription(): static {
        $this->required_seo_fields['description'] = true;

        return $this;
    }

    /**
     * Whether the social sharing image must be specified. Calling this method will set it to "true"
     *
     * @return static
     */
    public function requiresSEOImage(): static {
        $this->required_seo_fields['image'] = true;

        return $this;
    }

    /**
     * Whether the Twitter card type must be specified. Calling this method will set it to "true"
     *
     * @return static
     */
    public function requiresSEOTwitterCard(): static {
        $this->required_seo_fields['twitter_card'] = true;

        return $this;
    }

    /**
     * Set title length limits for SEO field
     *
     * Search engines usually truncate title tags to 60 characters.
     *
     * @param   int|null    $min    Minimum value
     * @param   int|null    $max    Maximum value
     *
     * @return  static
     */
    public function setTitleLength(?int $min = null, ?int $max = null): static {
        if ($min !== null) {
            $this->title_length['min'] = $min;
        }
        if ($max !== null) {
            $this->title_length['max'] = $max;
        }

        return $this;
    }

    /**
     * Set description length limits for SEO field
     *
     * Search engines usually truncate description tags to 160 characters.
     *
     * @param   int|null    $min    Minimum value
     * @param   int|null    $max    Maximum value
     *
     * @return  static
     */
    public function setDescriptionLength(?int $min = null, ?int $max = null): static {
        if ($min !== null) {
            $this->description_length['min'] = $min;
        }
        if ($max !== null) {
            $this->description_length['max'] = $max;
        }

        return $this;
    }

    /**
     * Set allowed block model IDs for Modular Content field
     *
     * @param   string[]    $item_types     Set of allowed Block Model IDs
     *
     * @return  static
     */
    public function setRichTextBlocks(array $item_types): static {
        $this->rich_text_blocks['item_types'] = $item_types;

        return $this;
    }

    /**
     * Set allowed block model IDs for Single Block field
     *
     * @param   string[]    $item_types     Set of allowed Block Model IDs
     *
     * @return  static
     */
    public function setSingleBlockBlocks(array $item_types): static {
        $this->single_block_blocks['item_types'] = $item_types;

        return $this;
    }

    /**
     * Enable HTML sanitization validator
     *
     * Checks for malicious code in HTML fields.
     *
     * @param   bool    $sanitize_before_validation     Content is actively sanitized before validation
     *
     * @return  static
     */
    public function setSanitizedHtml(bool $sanitize_before_validation): static {
        $this->sanitized_html['sanitize_before_validation'] = $sanitize_before_validation;

        return $this;
    }

    /**
     * Set allowed block model IDs for Structured Text field blocks
     *
     * @param   string[]    $item_types     Set of allowed Block Model IDs
     *
     * @return  static
     */
    public function setStructuredTextBlocks(array $item_types): static {
        $this->structured_text_blocks['item_types'] = $item_types;

        return $this;
    }

    /**
     * Set allowed block model IDs for Structured Text inline blocks
     *
     * @param   string[]    $item_types     Set of allowed Block Model IDs
     *
     * @return  static
     */
    public function setStructuredTextInlineBlocks(array $item_types): static {
        $this->structured_text_inline_blocks['item_types'] = $item_types;

        return $this;
    }

    /**
     * Set allowed model IDs for Structured Text links
     *
     * @param   string[]    $item_types     Set of allowed Model IDs
     *
     * @return  static
     */
    public function setStructuredTextLinks(array $item_types): static {
        $this->structured_text_links['item_types'] = $item_types;

        return $this;
    }

    /**
     * Set strategy to apply when publishing with unpublished references (Structured Text links)
     *
     * @param   string      $strategy       Strategy (fail or publish_references)
     *
     * @return  static
     */
    public function setStructuredTextLinksOnPublishStrategy(string $strategy): static {
        if ($strategy !== 'fail' && $strategy !== 'publish_references') {
            throw new \InvalidArgumentException('Invalid strategy');
        }
        $this->structured_text_links['on_publish_with_unpublished_references_strategy'] = $strategy;

        return $this;
    }

    /**
     * Set strategy to apply when unpublishing referenced records (Structured Text links)
     *
     * @param   string      $strategy       Strategy (fail, unpublish or delete_references)
     *
     * @return  static
     */
    public function setStructuredTextLinksOnUnpublishStrategy(string $strategy): static {
        if ($strategy !== 'fail' && $strategy !== 'unpublish' && $strategy !== 'delete_references') {
            throw new \InvalidArgumentException('Invalid strategy');
        }
        $this->structured_text_links['on_reference_unpublish_strategy'] = $strategy;

        return $this;
    }

    /**
     * Set strategy to apply when deleting referenced records (Structured Text links)
     *
     * @param   string      $strategy       Strategy (fail or delete_references)
     *
     * @return  static
     */
    public function setStructuredTextLinksOnDeleteStrategy(string $strategy): static {
        if ($strategy !== 'fail' && $strategy !== 'delete_references') {
            throw new \InvalidArgumentException('Invalid strategy');
        }
        $this->structured_text_links['on_reference_delete_strategy'] = $strategy;

        return $this;
    }

    /**
     * Set size range for number of items
     *
     * @param   int|null    $min            Minimum number of items
     * @param   int|null    $max            Maximum number of items
     *
     * @return  static
     */
    public function setSizeRange(?int $min = null, ?int $max = null): static {
        if ($min !== null) {
            $this->size['min'] = $min;
        }
        if ($max !== null) {
            $this->size['max'] = $max;
        }

        return $this;
    }

    /**
     * Set exact size for number of items
     *
     * @param   int         $equal          Exact number of items required
     *
     * @return  static
     */
    public function setSize(int $equal): static {
        $this->size['eq'] = $equal;

        return $this;
    }

    /**
     * Set size as multiple of a number
     *
     * @param   int         $multiple_of    Number of items must be a multiple of this value
     *
     * @return  static
     */
    public function setSizeMultipleOf(int $multiple_of): static {
        $this->size['multiple_of'] = $multiple_of;

        return $this;
    }

    /**
     * Set the field ID that will be used to generate the slug
     *
     * @param   string      $title_field_id     The field that will be used to generate the slug
     *
     * @return  static
     */
    public function setSlugTitleField(string $title_field_id): static {
        $this->slug_title_field['title_field_id'] = $title_field_id;

        return $this;
    }


    public function jsonSerialize(): array {
        $validators = [];

        // Boolean validators
        if ($this->required) {
            // DatoCMS expects an empty object for required
            $validators['required'] = '{}';
        }
        if ($this->unique) {
            // DatoCMS expects an empty object for unique
            $validators['unique'] = '{}';
        }

        // Array validators (only include if not empty)
        if (!empty($this->date_range)) {
            $validators['date_range'] = $this->date_range;
        }
        if (!empty($this->date_time_range)) {
            $validators['date_time_range'] = $this->date_time_range;
        }
        if (!empty($this->enum)) {
            $validators['enum'] = ['values' => $this->enum];
        }
        if (!empty($this->extension)) {
            $validators['extension'] = $this->extension;
        }
        if (!empty($this->file_size)) {
            $validators['file_size'] = $this->file_size;
        }
        if (!empty($this->format)) {
            $validators['format'] = $this->format;
        }
        if (!empty($this->slug_format)) {
            $validators['slug_format'] = $this->slug_format;
        }
        if (!empty($this->image_dimensions)) {
            $validators['image_dimensions'] = $this->image_dimensions;
        }
        if (!empty($this->image_aspect_ratio)) {
            $validators['image_aspect_ratio'] = $this->image_aspect_ratio;
        }
        if (!empty($this->item_item_type)) {
            $validators['item_item_type'] = $this->item_item_type;
        }
        if (!empty($this->items_item_type)) {
            $validators['items_item_type'] = $this->items_item_type;
        }
        if (!empty($this->length)) {
            $validators['length'] = $this->length;
        }
        if (!empty($this->number_range)) {
            $validators['number_range'] = $this->number_range;
        }
        if (!empty($this->required_alt_title)) {
            $validators['required_alt_title'] = $this->required_alt_title;
        }
        if (!empty($this->required_seo_fields)) {
            $validators['required_seo_fields'] = $this->required_seo_fields;
        }
        if (!empty($this->title_length)) {
            $validators['title_length'] = $this->title_length;
        }
        if (!empty($this->description_length)) {
            $validators['description_length'] = $this->description_length;
        }
        if (!empty($this->rich_text_blocks)) {
            $validators['rich_text_blocks'] = $this->rich_text_blocks;
        }
        if (!empty($this->single_block_blocks)) {
            $validators['single_block_blocks'] = $this->single_block_blocks;
        }
        if (!empty($this->sanitized_html)) {
            $validators['sanitized_html'] = $this->sanitized_html;
        }
        if (!empty($this->structured_text_blocks)) {
            $validators['structured_text_blocks'] = $this->structured_text_blocks;
        }
        if (!empty($this->structured_text_inline_blocks)) {
            $validators['structured_text_inline_blocks'] = $this->structured_text_inline_blocks;
        }
        if (!empty($this->structured_text_links)) {
            $validators['structured_text_links'] = $this->structured_text_links;
        }
        if (!empty($this->size)) {
            $validators['size'] = $this->size;
        }
        if (!empty($this->slug_title_field)) {
            $validators['slug_title_field'] = $this->slug_title_field;
        }

        return $validators;
    }
}
