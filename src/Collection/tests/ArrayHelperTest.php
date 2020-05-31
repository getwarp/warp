<?php

declare(strict_types=1);

namespace spaceonfire\Collection;

use ArrayObject;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

class ArrayHelperTest extends TestCase
{
    public function getValueDataProvider()
    {
        return [
            ['name', 'test'],
            ['noname', null],
            ['noname', 'test', 'test'],
            ['post.id', 5],
            ['post.id', 5, 'test'],
            ['nopost.id', null],
            ['nopost.id', 'test', 'test'],
            ['post.author.name', 'john'],
            ['post.author.noname', null],
            ['post.author.noname', 'test', 'test'],
            ['post.author.profile.title', '1337'],
            ['admin.firstname', 'John'],
            ['admin.firstname', 'John', 'test'],
            ['admin.lastname', 'Doe'],
            [
                static function ($array, $defaultValue) {
                    return $array['date'] . $defaultValue;
                },
                '31-12-2113test',
                'test',
            ],
            [['version', '1.0', 'status'], 'released'],
            [['version', '1.0', 'date'], 'defaultValue', 'defaultValue'],
        ];
    }

    /**
     * @dataProvider getValueDataProvider
     * @param string $key
     * @param mixed $expected
     * @param mixed|null $default
     */
    public function testGetValue($key, $expected, $default = null)
    {
        $array = [
            'name' => 'test',
            'date' => '31-12-2113',
            'post' => [
                'id' => 5,
                'author' => [
                    'name' => 'john',
                    'profile' => [
                        'title' => '1337',
                    ],
                ],
            ],
            'admin.firstname' => 'John',
            'admin.lastname' => 'Doe',
            'admin' => [
                'lastname' => 'doe',
            ],
            'version' => [
                '1.0' => [
                    'status' => 'released',
                ],
            ],
        ];

        self::assertEquals($expected, ArrayHelper::getValue($array, $key, $default));
    }

    public function testGetValueFloat()
    {
        $array = [];
        $array[1.0] = 'some value';

        $result = ArrayHelper::getValue($array, 1.0);

        self::assertEquals('some value', $result);
    }


    public function testGetValueObjects()
    {
        $arrayObject = new ArrayObject(['id' => 23], ArrayObject::ARRAY_AS_PROPS);
        self::assertEquals(23, ArrayHelper::getValue($arrayObject, 'id'));

        $object = new stdClass();
        $object->id = 23;
        self::assertEquals(23, ArrayHelper::getValue($object, 'id'));
    }

    public function testGetValueNonexistingProperties1()
    {
        $this->expectNotice();
        $object = new class {
        };
        ArrayHelper::getValue($object, 'nonExisting');
    }

    public function testGetValueFromArrayAccess()
    {
        $arrayAccessibleObject = new ArrayObject([
            'one' => 1,
            'two' => 2,
            'three' => 3,
            'key.with.dot' => 'dot',
            'null' => null,
        ], ArrayObject::ARRAY_AS_PROPS);

        self::assertEquals(1, ArrayHelper::getValue($arrayAccessibleObject, 'one'));
    }

    public function testGetValueWithDotsFromArrayAccess()
    {
        $arrayAccessibleObject = new ArrayObject([
            'one' => 1,
            'two' => 2,
            'three' => 3,
            'key.with.dot' => 'dot',
            'null' => null,
        ], ArrayObject::ARRAY_AS_PROPS);

        self::assertEquals('dot', ArrayHelper::getValue($arrayAccessibleObject, 'key.with.dot'));
    }

    public function testGetValueNonexistingArrayAccess()
    {
        $arrayAccessibleObject = new ArrayObject([
            'one' => 1,
            'two' => 2,
            'three' => 3,
            'key.with.dot' => 'dot',
            'null' => null,
        ], ArrayObject::ARRAY_AS_PROPS);

        self::assertEquals(null, ArrayHelper::getValue($arrayAccessibleObject, 'four'));
    }

    public function setValueDataProvider()
    {
        return [
            [
                [
                    'key1' => 'val1',
                    'key2' => 'val2',
                ],
                'key', 'val',
                [
                    'key1' => 'val1',
                    'key2' => 'val2',
                    'key' => 'val',
                ],
            ],
            [
                [
                    'key1' => 'val1',
                    'key2' => 'val2',
                ],
                'key2', 'val',
                [
                    'key1' => 'val1',
                    'key2' => 'val',
                ],
            ],

            [
                [
                    'key1' => 'val1',
                ],
                'key.in', 'val',
                [
                    'key1' => 'val1',
                    'key' => ['in' => 'val'],
                ],
            ],
            [
                [
                    'key' => 'val1',
                ],
                'key.in', 'val',
                [
                    'key' => [
                        'val1',
                        'in' => 'val',
                    ],
                ],
            ],
            [
                [
                    'key' => 'val1',
                ],
                'key', ['in' => 'val'],
                [
                    'key' => ['in' => 'val'],
                ],
            ],

            [
                [
                    'key1' => 'val1',
                ],
                'key.in.0', 'val',
                [
                    'key1' => 'val1',
                    'key' => [
                        'in' => ['val'],
                    ],
                ],
            ],

            [
                [
                    'key1' => 'val1',
                ],
                'key.in.arr', 'val',
                [
                    'key1' => 'val1',
                    'key' => [
                        'in' => [
                            'arr' => 'val',
                        ],
                    ],
                ],
            ],
            [
                [
                    'key1' => 'val1',
                ],
                'key.in.arr', ['val'],
                [
                    'key1' => 'val1',
                    'key' => [
                        'in' => [
                            'arr' => ['val'],
                        ],
                    ],
                ],
            ],
            [
                [
                    'key' => [
                        'in' => ['val1'],
                    ],
                ],
                'key.in.arr', 'val',
                [
                    'key' => [
                        'in' => [
                            'val1',
                            'arr' => 'val',
                        ],
                    ],
                ],
            ],

            [
                [
                    'key' => ['in' => 'val1'],
                ],
                'key.in.arr', ['val'],
                [
                    'key' => [
                        'in' => [
                            'val1',
                            'arr' => ['val'],
                        ],
                    ],
                ],
            ],
            [
                [
                    'key' => [
                        'in' => [
                            'val1',
                            'key' => 'val',
                        ],
                    ],
                ],
                'key.in.0', ['arr' => 'val'],
                [
                    'key' => [
                        'in' => [
                            ['arr' => 'val'],
                            'key' => 'val',
                        ],
                    ],
                ],
            ],
            [
                [
                    'key' => [
                        'in' => [
                            'val1',
                            'key' => 'val',
                        ],
                    ],
                ],
                'key.in', ['arr' => 'val'],
                [
                    'key' => [
                        'in' => ['arr' => 'val'],
                    ],
                ],
            ],
            [
                [
                    'key' => [
                        'in' => [
                            'key' => 'val',
                            'data' => [
                                'attr1',
                                'attr2',
                                'attr3',
                            ],
                        ],
                    ],
                ],
                'key.in.schema', 'array',
                [
                    'key' => [
                        'in' => [
                            'key' => 'val',
                            'schema' => 'array',
                            'data' => [
                                'attr1',
                                'attr2',
                                'attr3',
                            ],
                        ],
                    ],
                ],
            ],
            [
                [
                    'key' => [
                        'in.array' => [
                            'key' => 'val',
                        ],
                    ],
                ],
                ['key', 'in.array', 'ok.schema'], 'array',
                [
                    'key' => [
                        'in.array' => [
                            'key' => 'val',
                            'ok.schema' => 'array',
                        ],
                    ],
                ],
            ],
            [
                [
                    'key' => ['val'],
                ],
                null, 'data',
                'data',
            ],
        ];
    }

    /**
     * @dataProvider setValueDataProvider
     *
     * @param array $array_input
     * @param string|array|null $key
     * @param mixed $value
     * @param mixed $expected
     */
    public function testSetValue($array_input, $key, $value, $expected)
    {
        ArrayHelper::setValue($array_input, $key, $value);
        self::assertEquals($expected, $array_input);
    }

    public function testMultisort()
    {
        // empty key
        $dataEmpty = [];
        ArrayHelper::multisort($dataEmpty, '');
        self::assertEquals([], $dataEmpty);

        // single key
        $array = [
            ['name' => 'b', 'age' => 3],
            ['name' => 'a', 'age' => 1],
            ['name' => 'c', 'age' => 2],
        ];
        ArrayHelper::multisort($array, 'name');
        self::assertEquals(['name' => 'a', 'age' => 1], $array[0]);
        self::assertEquals(['name' => 'b', 'age' => 3], $array[1]);
        self::assertEquals(['name' => 'c', 'age' => 2], $array[2]);

        // multiple keys
        $array = [
            ['name' => 'b', 'age' => 3],
            ['name' => 'a', 'age' => 2],
            ['name' => 'a', 'age' => 1],
        ];
        ArrayHelper::multisort($array, ['name', 'age']);
        self::assertEquals(['name' => 'a', 'age' => 1], $array[0]);
        self::assertEquals(['name' => 'a', 'age' => 2], $array[1]);
        self::assertEquals(['name' => 'b', 'age' => 3], $array[2]);

        // case-insensitive
        $array = [
            ['name' => 'a', 'age' => 3],
            ['name' => 'b', 'age' => 2],
            ['name' => 'B', 'age' => 4],
            ['name' => 'A', 'age' => 1],
        ];

        ArrayHelper::multisort($array, ['name', 'age'], SORT_ASC, [SORT_STRING, SORT_REGULAR]);
        self::assertEquals(['name' => 'A', 'age' => 1], $array[0]);
        self::assertEquals(['name' => 'B', 'age' => 4], $array[1]);
        self::assertEquals(['name' => 'a', 'age' => 3], $array[2]);
        self::assertEquals(['name' => 'b', 'age' => 2], $array[3]);

        ArrayHelper::multisort($array, ['name', 'age'], SORT_ASC, [SORT_STRING | SORT_FLAG_CASE, SORT_REGULAR]);
        self::assertEquals(['name' => 'A', 'age' => 1], $array[0]);
        self::assertEquals(['name' => 'a', 'age' => 3], $array[1]);
        self::assertEquals(['name' => 'b', 'age' => 2], $array[2]);
        self::assertEquals(['name' => 'B', 'age' => 4], $array[3]);
    }

    public function testMultisortNestedObjects()
    {
        $obj1 = new stdClass();
        $obj1->type = 'def';
        $obj1->owner = $obj1;

        $obj2 = new stdClass();
        $obj2->type = 'abc';
        $obj2->owner = $obj2;

        $obj3 = new stdClass();
        $obj3->type = 'abc';
        $obj3->owner = $obj3;

        $models = [
            $obj1,
            $obj2,
            $obj3,
        ];

        self::assertEquals($obj2, $obj3);

        ArrayHelper::multisort($models, 'type', SORT_ASC);
        self::assertEquals($obj2, $models[0]);
        self::assertEquals($obj3, $models[1]);
        self::assertEquals($obj1, $models[2]);

        ArrayHelper::multisort($models, 'type', SORT_DESC);
        self::assertEquals($obj1, $models[0]);
        self::assertEquals($obj2, $models[1]);
        self::assertEquals($obj3, $models[2]);
    }

    public function testMultisortClosure()
    {
        $changelog = [
            '- Enh #123: test1',
            '- Bug #125: test2',
            '- Bug #123: test2',
            '- Enh: test3',
            '- Bug: test4',
        ];
        $i = 0;
        ArrayHelper::multisort($changelog, static function ($line) use (&$i) {
            if (preg_match('/^- (Enh|Bug)( #\d+)?: .+$/', $line, $m)) {
                $o = ['Bug' => 'C', 'Enh' => 'D'];
                return $o[$m[1]] . ' ' . (!empty($m[2]) ? $m[2] : 'AAAA' . $i++);
            }

            return 'B' . $i++;
        }, SORT_ASC, SORT_NATURAL);
        self::assertEquals([
            '- Bug #123: test2',
            '- Bug #125: test2',
            '- Bug: test4',
            '- Enh #123: test1',
            '- Enh: test3',
        ], $changelog);
    }

    public function testMultisortInvalidParamExceptionDirection()
    {
        $this->expectException(InvalidArgumentException::class);
        $data = ['foo' => 'bar'];
        ArrayHelper::multisort($data, ['foo'], []);
    }

    public function testMultisortInvalidParamExceptionSortFlag()
    {
        $this->expectException(InvalidArgumentException::class);
        $data = ['foo' => 'bar'];
        ArrayHelper::multisort($data, ['foo'], ['foo'], []);
    }

    public function testMap()
    {
        $array = [
            ['id' => '123', 'name' => 'aaa', 'class' => 'x'],
            ['id' => '124', 'name' => 'bbb', 'class' => 'x'],
            ['id' => '345', 'name' => 'ccc', 'class' => 'y'],
        ];

        $result = ArrayHelper::map($array, 'id', 'name');
        self::assertEquals([
            '123' => 'aaa',
            '124' => 'bbb',
            '345' => 'ccc',
        ], $result);

        $result = ArrayHelper::map($array, 'id', 'name', 'class');
        self::assertEquals([
            'x' => [
                '123' => 'aaa',
                '124' => 'bbb',
            ],
            'y' => [
                '345' => 'ccc',
            ],
        ], $result);
    }

    public function testIsArrayAssoc()
    {
        self::assertFalse(ArrayHelper::isArrayAssoc('test'));
        self::assertFalse(ArrayHelper::isArrayAssoc([]));
        self::assertFalse(ArrayHelper::isArrayAssoc([1, 2, 3]));
        self::assertFalse(ArrayHelper::isArrayAssoc([1]));
        self::assertTrue(ArrayHelper::isArrayAssoc(['name' => 1, 'value' => 'test']));
        self::assertTrue(ArrayHelper::isArrayAssoc(['name' => 1, 'value' => 'test', 3]));
    }

    public function testMerge()
    {
        $a = [
            'name' => 'Yii',
            'version' => '1.0',
            'options' => [
                'namespace' => false,
                'unittest' => false,
            ],
            'features' => [
                'mvc',
            ],
        ];
        $b = [
            'version' => '1.1',
            'options' => [
                'unittest' => true,
            ],
            'features' => [
                'gii',
            ],
        ];
        $c = [
            'version' => '2.0',
            'options' => [
                'namespace' => true,
            ],
            'features' => [
                'debug',
            ],
            'foo',
        ];

        $result = ArrayHelper::merge($a, $b, $c);
        $expected = [
            'name' => 'Yii',
            'version' => '2.0',
            'options' => [
                'namespace' => true,
                'unittest' => true,
            ],
            'features' => [
                'mvc',
                'gii',
                'debug',
            ],
            'foo',
        ];

        self::assertEquals($expected, $result);
    }

    public function testMergeWithNumericKeys()
    {
        $a = [10 => [1]];
        $b = [10 => [2]];

        $result = ArrayHelper::merge($a, $b);

        $expected = [10 => [1], 11 => [2]];
        self::assertEquals($expected, $result);
    }

    public function testMergeWithNullValues()
    {
        $a = [
            'firstValue',
            null,
        ];
        $b = [
            'secondValue',
            'thirdValue',
        ];

        $result = ArrayHelper::merge($a, $b);
        $expected = [
            'firstValue',
            null,
            'secondValue',
            'thirdValue',
        ];

        self::assertEquals($expected, $result);
    }

    public function testMergeEmpty()
    {
        self::assertEquals([], ArrayHelper::merge([], []));
        self::assertEquals([], ArrayHelper::merge([], [], []));
    }

    public function testGetColumn()
    {
        $array = [
            'a' => ['id' => '123', 'data' => 'abc'],
            'b' => ['id' => '345', 'data' => 'def'],
        ];
        $result = ArrayHelper::getColumn($array, 'id');
        self::assertEquals(['a' => '123', 'b' => '345'], $result);
        $result = ArrayHelper::getColumn($array, 'id', false);
        self::assertEquals(['123', '345'], $result);

        $result = ArrayHelper::getColumn($array, static function ($element) {
            return $element['data'];
        });
        self::assertEquals(['a' => 'abc', 'b' => 'def'], $result);
        $result = ArrayHelper::getColumn($array, static function ($element) {
            return $element['data'];
        }, false);
        self::assertEquals(['abc', 'def'], $result);
    }

    public function testFlatten()
    {
        $unflatten = [
            'name' => 'test',
            'date' => '31-12-2113',
            'post' => [
                'id' => 5,
                'author' => [
                    'name' => 'john',
                    'profile' => [
                        'title' => '1337',
                    ],
                ],
            ],
            'admin.firstname' => 'John',
            'admin.lastname' => 'Doe',
            'admin' => [
                'lastname' => 'doe',
            ],
            'version' => [
                '1.0' => [
                    'status' => 'released',
                ],
            ],
        ];

        $flatten = [
            'name' => 'test',
            'date' => '31-12-2113',
            'post.id' => 5,
            'post.author.name' => 'john',
            'post.author.profile.title' => '1337',
            'admin.firstname' => 'John',
            'admin.lastname' => 'doe',
            'version.1.0.status' => 'released',
        ];

        self::assertEquals($flatten, ArrayHelper::flatten($unflatten));
    }

    public function testUnflatten()
    {
        $unflatten = [
            'name' => 'test',
            'date' => '31-12-2113',
            'post' => [
                'id' => 5,
                'author' => [
                    'name' => 'john',
                    'profile' => [
                        'title' => '1337',
                    ],
                ],
            ],
            'admin' => [
                'firstname' => 'John',
                'lastname' => 'doe',
            ],
            'version' => [
                '1' => [
                    '0' => [
                        'status' => 'released',
                    ],
                ],
            ],
        ];

        $flatten = [
            'name' => 'test',
            'date' => '31-12-2113',
            'post.id' => 5,
            'post.author.name' => 'john',
            'post.author.profile.title' => '1337',
            'admin.firstname' => 'John',
            'admin.lastname' => 'doe',
            'version.1.0.status' => 'released',
        ];

        self::assertEquals($unflatten, ArrayHelper::unflatten($flatten));
    }
}
