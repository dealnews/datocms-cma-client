<?php

namespace DealNews\DatoCMS\CMA\Input\Parts\Site;

use Moonspot\ValueObjects\ValueObject;
use DealNews\DatoCMS\CMA\Input\Parts\Relationships\Role;

/**
 * Represents the relationships between a site and other entities.
 *
 * Little to no documentation is provided for this feature in DatoCMS
 */
class Relationships extends ValueObject {

    /**
     * @var Role
     */
    public Role $sso_default_role;

    public function __construct() {
        $this->sso_default_role = new Role();
    }
}