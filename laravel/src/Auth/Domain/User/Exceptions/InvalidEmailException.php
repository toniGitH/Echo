<?php

declare(strict_types=1);

namespace Src\Auth\Domain\User\Exceptions;

use Src\Shared\Domain\Exceptions\InvalidValueObjectException;

final class InvalidEmailException extends InvalidValueObjectException
{
    public function __construct()
    {
        parent::__construct('messages.user.INVALID_EMAIL_FORMAT');
    }
}