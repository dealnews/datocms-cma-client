<?php

namespace DealNews\DatoCMS\CMA\Exception;


class API extends \RuntimeException {

    protected ?string $response_body = null;

    public function __construct(string $message, int $code = 0, ?\Throwable $previous = null, ?string $response_body = null) {
        $this->response_body = $response_body;
        parent::__construct($message, $code, $previous);
    }

    public function getResponseBody(): ?string {
        return $this->response_body;
    }
}