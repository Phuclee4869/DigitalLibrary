<?php

namespace App\Exceptions;

use Exception;

/**
 * Lỗi vi phạm quy tắc nghiệp vụ.
 * Được bootstrap/app.php chuyển thành JSON thống nhất với các lỗi 401/403/404/422.
 */
class BusinessException extends Exception
{
    public function __construct(
        string $message,
        protected string $errorCode = 'BUSINESS_RULE_VIOLATION',
        protected int $httpStatus = 422,
        protected ?array $details = null
    ) {
        parent::__construct($message);
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
