<?php

declare(strict_types=1);

namespace Src\Auth\Domain\User\Exceptions;

use Src\Shared\Domain\Exceptions\DomainException;

final class EmptyRolesCollectionException extends DomainException
{
    public function __construct()
    {
        parent::__construct('messages.user.ROLES_REQUIRED');
    }
}
