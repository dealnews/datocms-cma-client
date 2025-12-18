<?php

namespace DealNews\DatoCMS\CMA\Exception;

class Decode extends \RuntimeException {

    protected ?string $raw_json = null;

    public function __construct(string $message, int $code = 0, ?\Throwable $previous = null, ?string $raw_json = null) {
        $this->raw_json = $raw_json;
        parent::__construct($message, $code, $previous);
    }

    public function getRawJson(): ?string {
        return $this->raw_json;
    }

}