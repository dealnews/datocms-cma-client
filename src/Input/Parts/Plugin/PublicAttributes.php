<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Plugin;

use Moonspot\ValueObjects\ValueObject;

class PublicAttributes extends ValueObject {

    /**
     * NPM package name of the public plugin you want to install.
     *
     * For public plugins, that's the only attribute you need to pass.
     *
     * Example: "datocms-plugin-star-rating-editor"
     *
     * @var string
     */
    public string $package_name = '';

}
