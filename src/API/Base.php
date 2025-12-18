<?php

namespace DealNews\DatoCMS\CMA\API;

use DealNews\DatoCMS\CMA\Config;
use DealNews\DatoCMS\CMA\HTTP\Handler;

abstract class Base {

    protected Handler $handler;

    public function __construct(?Handler $handler = null) {
        if (!empty($handler)) {
            $this->handler = $handler;
        } else {
            $apiToken = Config::init()->apiToken;
            $environment = Config::init()->environment;
            $base_url = Config::init()->base_url;
            $logger = Config::init()->logger;
            $log_level = Config::init()->log_level;

            $this->handler = new Handler(
                $apiToken,
                $environment,
                $logger,
                $log_level,
                $base_url
            );
        }
    }

}