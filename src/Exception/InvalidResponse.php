<?php

namespace Transloyd\Services\ESign\Exception;

use RuntimeException;
use stdClass;
use Throwable;

/**
 * Exception thrown when an unexpected response is returned from the http client provider
 */
class InvalidResponse extends RuntimeException implements Exception
{
    public const INVALID_JSON_STRUCTURE_MESSAGE = 'Failed to parse JSON response: %s.';

    private $response;

    public function __construct(stdClass $response, string $message = '', int $code = 0, Throwable $previous = null)
    {
        $this->response = $response;

        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): stdClass
    {
        return $this->response;
    }
}
