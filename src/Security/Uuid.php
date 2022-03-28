<?php declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Uid\UuidV4;

class Uuid
{
    private const VALID_PATTERN = '^[0-9a-f]{32}$';

    public static function generate(): string
    {
        return str_replace('-', '', (string)UuidV4::v4());
    }

    public static function isValid(string $id): bool
    {
        return (bool)preg_match('/' . self::VALID_PATTERN . '/', $id);
    }
}