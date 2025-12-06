<?php

declare(strict_types=1);

namespace Src\Auth\Domain\User\Exceptions;

use Src\Shared\Domain\Exceptions\DomainException;

final class InvalidRoleTypeException extends DomainException
{
    public function __construct()
    {
        parent::__construct('messages.user.INVALID_ROLE_TYPE');
    }
}
