<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Exceptions;

final class MultipleDomainException extends DomainException
{
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Multiple domain validation errors');
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
