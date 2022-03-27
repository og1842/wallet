<?php declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Uid\UuidV4;

class IdGenerator
{
    public static function generate(): string
    {
        return str_replace('-', '', (string)UuidV4::v4());
    }
}