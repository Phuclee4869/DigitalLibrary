<?php

namespace App\Exceptions;

use Exception;

/**
 * Lỗi vi phạm quy tắc nghiệp vụ (V3).
 */
class BusinessException extends Exception
{
    protected string $errorCode;
    protected int $httpStatus;
    protected ?array $details;

    public function __construct(
        string $message,
        string $errorCode = 'BUSINESS_RULE_VIOLATION',
        int $httpStatus = 422,
        ?array $details = null
    ) {
        parent::__construct($message);
        $this->errorCode = $errorCode;
        $this->httpStatus = $httpStatus;
        $this->details = $details;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function getDetails(): ?array
    {
        return $this->details;
    }
}
