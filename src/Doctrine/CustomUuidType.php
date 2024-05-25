<?php

namespace App\Doctrine;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\Uid\Uuid;

class CustomUuidType extends Type
{


    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    /**
     * @throws Exception
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }
        if (is_string($value)) {
            return Uuid::fromRfc4122($value);
        }
        if (!$value instanceof Uuid) {
            throw new Exception('Expected Uuid instance.');
        }
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }
        return $value instanceof Uuid ? $value->toRfc4122() : $value;
    }

    public function getName(): string
    {
        return 'custom_uuid';
    }
}