<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    public function __construct(
        private readonly int $errorCode = 1001001,
        string $message = '业务异常',
    ) {
        parent::__construct($message);
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }
}
