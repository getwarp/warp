<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Schema;

use Cycle\Schema\Definition\Field;

/**
 * @see Field
 * @phpstan-type FieldOptionsShape array{default?:mixed,castDefault?:mixed,nullable?:boolean}
 * @phpstan-type FieldShape=array{name:string,column?:string,type?:string,primary?:boolean,typecast?:array<array-key,mixed>|string,referenced?:boolean,options?:FieldOptionsShape}
 */
final class FieldDto
{
    public const NAME = 'name';

    public const COLUMN = 'column';

    public const TYPE = 'type';

    public const PRIMARY = 'primary';

    public const TYPECAST = 'typecast';

    public const REFERENCED = 'referenced';

    public const OPTIONS = 'options';

    public const OPTION_DEFAULT = 'default';

    public const OPTION_CAST_DEFAULT = 'castDefault';

    public const OPTION_NULLABLE = 'nullable';

    /**
     * @param FieldShape $data
     * @return Field
     */
    public static function makeSchema(array $data): Field
    {
        $field = new Field();

        // TODO: assert argument types
        $field->setColumn($data[self::COLUMN] ?? $data[self::NAME]);

        if (isset($data[self::TYPE])) {
            $field->setType($data[self::TYPE]);
        }
        if (isset($data[self::PRIMARY])) {
            $field->setPrimary($data[self::PRIMARY]);
        }
        if (isset($data[self::TYPECAST])) {
            $field->setTypecast($data[self::TYPECAST]);
        }
        if (isset($data[self::REFERENCED])) {
            $field->setReferenced($data[self::REFERENCED]);
        }

        foreach ($data[self::OPTIONS] ?? [] as $option => $value) {
            $field->getOptions()->set($option, $value);
        }

        return $field;
    }
}
