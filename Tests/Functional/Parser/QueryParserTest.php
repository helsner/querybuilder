<?php

namespace T3G\Querybuilder\Tests\Functional\Parser;

use T3G\Querybuilder\Parser\QueryParser;
use T3G\Querybuilder\Tests\Functional\FunctionalTestCase;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Test case.
 */
class QueryParserTest extends FunctionalTestCase
{
    /** @var  QueryParser */
    protected $subject;

    /**
     * @var string the database table
     */
    protected $table = 'pages';

    /**
     *
     */
    protected function setUp()
    {
        $this->subject = new QueryParser();
        parent::setUp();
    }

    /**
     * @return array
     */
    public function parseReturnsValidWhereClauseForSimpleEqualQueryDataProvider() : array
    {
        return [
            'integer value as type string' => [42, 'string', ' ( `title` = \'42\' ) '],
            'string as number value as type string' => ['42', 'string', ' ( `title` = \'42\' ) '],
            'float value as type string' => [42.5, 'string', ' ( `title` = \'42.5\' ) '],
            'string float value as type string' => ['42.5', 'string', ' ( `title` = \'42.5\' ) '],
            'comma value as type string' => ['42,5', 'string', ' ( `title` = \'42,5\' ) '],
            'string as string value as type string' => ['foo', 'string', ' ( `title` = \'foo\' ) '],

            'integer value as type integer' => [42, 'integer', ' ( `title` = \'42\' ) '],
            'string as number value as type integer' => ['42', 'integer', ' ( `title` = \'42\' ) '],
            'integer(negative) value as type integer' => [-5, 'integer', ' ( `title` = \'-5\' ) '],
            'string(negative) as number value as type integer' => ['-5', 'integer', ' ( `title` = \'-5\' ) '],

            'integer(1) value as type boolean' => [[1], 'boolean', ' ( `title` = \'1\' ) '],
            'string(1) as number value as type boolean' => [['1'], 'boolean', ' ( `title` = \'1\' ) '],
            'integer(0) value as type boolean' => [[0], 'boolean', ' ( `title` = \'0\' ) '],
            'string(0) as number value as type boolean' => [['0'], 'boolean', ' ( `title` = \'0\' ) '],

            'integer value as type double' => [42, 'double', ' ( `title` = \'42\' ) '],
            'string as number value as type double' => ['42', 'double', ' ( `title` = \'42\' ) '],
            'integer(negative)value as type double' => [-5, 'double', ' ( `title` = \'-5\' ) '],
            'string(negative) as number value as type double' => ['-5', 'double', ' ( `title` = \'-5\' ) '],
            'float value as type double' => [42.5, 'double', ' ( `title` = \'42.5\' ) '],
            'string float value as type double' => ['42.5', 'double', ' ( `title` = \'42.5\' ) '],
            'float value (2 decimal w 00) as type double' => [42.00, 'double', ' ( `title` = \'42\' ) '],
            'float value (2 decimal w 50) as type double' => [42.50, 'double', ' ( `title` = \'42.5\' ) '],
            'float value (2 decimal w 55) as type double' => [42.55, 'double', ' ( `title` = \'42.55\' ) '],
            'string float value (2 decimal) as type double' => ['42.50', 'double', ' ( `title` = \'42.50\' ) '],
            'comma value as type double' => ['42,50', 'double', ' ( `title` = \'42.50\' ) '],
            'comma value (2 decimal) as type double' => ['42,50', 'double', ' ( `title` = \'42.50\' ) '],

            'comma value as type date' => ['2017-06-26', 'date', ' ( `title` = \'2017-06-26\' ) '],

            'comma value as type time' => ['18:30', 'time', ' ( `title` = \'18:30\' ) '],

            'string as number value as type datetime' => ['2017-06-26 17:55', 'datetime', ' ( `title` = \'2017-06-26 17:55\' ) '],
        ];
    }

    /**
     * @test
     * @dataProvider parseReturnsValidWhereClauseForSimpleEqualQueryDataProvider
     *
     * @param $number
     * @param $type
     * @param $expectedResult
     */
    public function parseReturnsValidWhereClauseForSimpleEqualQuery($number, $type, $expectedResult)
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "text",
              "operator": "equal",
              "value": "foo"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $query->rules[0]->value = $number;
        $query->rules[0]->type = $type;
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @return array
     */
    public function parseReturnsValidWhereClauseForSimpleNotEqualQueryDataProvider() : array
    {
        return [
            'integer value as type string' => [42, 'string', ' ( `title` <> \'42\' ) '],
            'string as number value as type string' => ['42', 'string', ' ( `title` <> \'42\' ) '],
            'float value as type string' => [42.5, 'string', ' ( `title` <> \'42.5\' ) '],
            'string float value as type string' => ['42.5', 'string', ' ( `title` <> \'42.5\' ) '],
            'comma value as type string' => ['42,5', 'string', ' ( `title` <> \'42,5\' ) '],
            'string as string value as type string' => ['foo', 'string', ' ( `title` <> \'foo\' ) '],

            'integer value as type integer' => [42, 'integer', ' ( `title` <> \'42\' ) '],
            'string as number value as type integer' => ['42', 'integer', ' ( `title` <> \'42\' ) '],
            'integer(negative) value as type integer' => [-5, 'integer', ' ( `title` <> \'-5\' ) '],
            'string(negative) as number value as type integer' => ['-5', 'integer', ' ( `title` <> \'-5\' ) '],

            'integer(1) value as type boolean' => [[1], 'boolean', ' ( `title` <> \'1\' ) '],
            'string(1) as number value as type boolean' => [['1'], 'boolean', ' ( `title` <> \'1\' ) '],
            'integer(0) value as type boolean' => [[0], 'boolean', ' ( `title` <> \'0\' ) '],
            'string(0) as number value as type boolean' => [['0'], 'boolean', ' ( `title` <> \'0\' ) '],

            'integer value as type double' => [42, 'double', ' ( `title` <> \'42\' ) '],
            'string as number value as type double' => ['42', 'double', ' ( `title` <> \'42\' ) '],
            'integer(negative)value as type double' => [-5, 'double', ' ( `title` <> \'-5\' ) '],
            'string(negative) as number value as type double' => ['-5', 'double', ' ( `title` <> \'-5\' ) '],
            'float value as type double' => [42.5, 'double', ' ( `title` <> \'42.5\' ) '],
            'string float value as type double' => ['42.5', 'double', ' ( `title` <> \'42.5\' ) '],
            'float value (2 decimal w 00) as type double' => [42.00, 'double', ' ( `title` <> \'42\' ) '],
            'float value (2 decimal w 50) as type double' => [42.50, 'double', ' ( `title` <> \'42.5\' ) '],
            'float value (2 decimal w 55) as type double' => [42.55, 'double', ' ( `title` <> \'42.55\' ) '],
            'string float value (2 decimal) as type double' => ['42.50', 'double', ' ( `title` <> \'42.50\' ) '],
            'comma value as type double' => ['42,50', 'double', ' ( `title` <> \'42.50\' ) '],
            'comma value (2 decimal) as type double' => ['42,50', 'double', ' ( `title` <> \'42.50\' ) '],

            'comma value as type date' => ['2017-06-26', 'date', ' ( `title` <> \'2017-06-26\' ) '],

            'comma value as type time' => ['18:30', 'time', ' ( `title` <> \'18:30\' ) '],

            'string as number value as type datetime' => ['2017-06-26 17:55', 'datetime', ' ( `title` <> \'2017-06-26 17:55\' ) '],
        ];
    }

    /**
     * @test
     * @dataProvider parseReturnsValidWhereClauseForSimpleNotEqualQueryDataProvider
     *
     * @param $number
     * @param $type
     * @param $expectedResult
     */
    public function parseReturnsValidWhereClauseForSimpleNotEqualQuery($number, $type, $expectedResult)
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "text",
              "operator": "not_equal",
              "value": "foo"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $query->rules[0]->value = $number;
        $query->rules[0]->type = $type;
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }


    /**
     * @return array
     */
    public function parseReturnsValidWhereClauseForSimpleInQueryDataProvider() : array
    {
        return [
            'integer value as type string' => [42, 'string', ' ( `title` IN (\'42\') ) '],
            'string as number value as type string' => ['42', 'string', ' ( `title` IN (\'42\') ) '],
            'float value as type string' => [42.5, 'string', ' ( `title` IN (\'42.5\') ) '],
            'two float values as type string' => ['42.5;50.5', 'string', ' ( `title` IN (\'42.5\',\'50.5\') ) '],
            'comma value as type string' => ['42,5', 'string', ' ( `title` IN (\'42,5\') ) '],
            'two comma values as type string' => ['42,5;5,5', 'string', ' ( `title` IN (\'42,5\',\'5,5\') ) '],
            'string(1 words) as string value as type string' => ['foo', 'string', ' ( `title` IN (\'foo\') ) '],
            'string(2 words) as string value as type string' => ['foo;bar', 'string', ' ( `title` IN (\'foo\',\'bar\') ) '],
            'string(3 words) as string value as type string' => ['foo;bar;dong', 'string', ' ( `title` IN (\'foo\',\'bar\',\'dong\') ) '],
            'mixed values as type string' => ['foo;42,5;dong', 'string', ' ( `title` IN (\'foo\',\'42,5\',\'dong\') ) '],

            'integer value as type integer' => [42, 'integer', ' ( `title` IN (\'42\') ) '],
            'string as number value as type integer' => ['42', 'integer', ' ( `title` IN (\'42\') ) '],

            'float value as type double' => [42.5, 'double', ' ( `title` IN (\'42.5\') ) '],
            'string float value as type double' => ['42.5', 'double', ' ( `title` IN (\'42.5\') ) '],
            'comma value as type double' => ['42,5', 'double', ' ( `title` IN (\'42.5\') ) '],

            'comma value as type date' => ['2017-06-26', 'date', ' ( `title` IN (\'2017-06-26\') ) '],

            'comma value as type time' => ['18:30', 'time', ' ( `title` IN (\'18:30\') ) '],

            'string as number value as type datetime' => ['2017-06-26 17:55', 'datetime', ' ( `title` IN (\'2017-06-26 17:55\') ) '],
        ];
    }

    /**
     * @test
     * @dataProvider parseReturnsValidWhereClauseForSimpleInQueryDataProvider
     *
     * @param $number
     * @param $type
     * @param $expectedResult
     */
    public function parseReturnsValidWhereClauseForSimpleInQuery($number, $type, $expectedResult)
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "text",
              "operator": "in",
              "value": "foo, bar"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $query->rules[0]->value = $number;
        $query->rules[0]->type = $type;
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /*
     %* @test
     */
    public function parseReturnsValidWhereClauseForSimpleNotInQuery()
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "text",
              "operator": "not_in",
              "value": "foo, bar"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $expectedResult = ' ( `title` != \'foo\' || `title` != \'bar\' ) ';
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @test
     */
    public function parseReturnsValidWhereClauseForSimpleBeginsQuery()
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "text",
              "operator": "begins_with",
              "value": "foo"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $expectedResult = ' ( `title` LIKE \'foo%\' ) ';
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @test
     */
    public function parseReturnsValidWhereClauseForSimpleNotBeginsQuery()
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "text",
              "operator": "not_begins_with",
              "value": "foo"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $expectedResult = ' ( `title` NOT LIKE \'foo%\' ) ';
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @test
     */
    public function parseReturnsValidWhereClauseForSimpleContainsQuery()
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "text",
              "operator": "contains",
              "value": "foo"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $expectedResult = ' ( `title` LIKE \'%foo%\' ) ';
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @test
     */
    public function parseReturnsValidWhereClauseForSimpleNotContainsQuery()
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "text",
              "operator": "not_contains",
              "value": "foo"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $expectedResult = ' ( `title` NOT LIKE \'%foo%\' ) ';
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @test
     */
    public function parseReturnsValidWhereClauseForSimpleEndsQuery()
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "text",
              "operator": "ends_with",
              "value": "foo"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $expectedResult = ' ( `title` LIKE \'%foo\' ) ';
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @test
     */
    public function parseReturnsValidWhereClauseForSimpleNotEndsQuery()
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "text",
              "operator": "not_ends_with",
              "value": "foo"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $expectedResult = ' ( `title` NOT LIKE \'%foo\' ) ';
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @test
     */
    public function parseReturnsValidWhereClauseForSimpleEmptyQuery()
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "text",
              "operator": "is_empty"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $expectedResult = ' ( (`title` = \'\') OR (`title` IS NULL) ) ';
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @test
     */
    public function parseReturnsValidWhereClauseForSimpleNotEmptyQuery()
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "text",
              "operator": "is_not_empty"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $expectedResult = ' ( (`title` <> \'\') AND (`title` IS NOT NULL) ) ';
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @test
     */
    public function parseReturnsValidWhereClauseForSimpleNullQuery()
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "text",
              "operator": "is_null"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $expectedResult = ' ( `title` IS NULL ) ';
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @test
     */
    public function parseReturnsValidWhereClauseForSimpleNotNullQuery()
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "text",
              "operator": "is_not_null"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $expectedResult = ' ( `title` IS NOT NULL ) ';
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @return array
     */
    public function parseReturnsValidWhereClauseForSimpleLessQueryDataProvider() : array
    {
        return [
            'integer value as type integer' => [42, 'integer', ' ( `title` < \'42\' ) '],
            'float value as type integer' => [42.5, 'integer', ' ( `title` < \'42.5\' ) '],
            'comma value as type integer' => ['42,5', 'integer', ' ( `title` < \'42,5\' ) '],
            'string as number value as type integer' => ['42', 'integer', ' ( `title` < \'42\' ) '],
            'string as string value as type integer' => ['foo', 'integer', ' ( `title` < \'foo\' ) '],

            'integer value as type string' => [42, 'string', ' ( `title` < \'42\' ) '],
            'float value as type string' => [42.5, 'string', ' ( `title` < \'42.5\' ) '],
            'comma value as type string' => ['42,5', 'string', ' ( `title` < \'42,5\' ) '],
            'string as number value as type string' => ['42', 'string', ' ( `title` < \'42\' ) '],
            'string as string value as type string' => ['foo', 'string', ' ( `title` < \'foo\' ) '],

            'integer value as type double' => [42, 'double', ' ( `title` < \'42\' ) '],
            'float value as type double' => [42.5, 'double', ' ( `title` < \'42.5\' ) '],
            'comma value as type double' => ['42,5', 'double', ' ( `title` < \'42.5\' ) '],
            'string as number value as type double' => ['42', 'double', ' ( `title` < \'42\' ) '],
            'string as string value as type double' => ['foo', 'double', ' ( `title` < \'foo\' ) '],

            'integer value as type date' => [42, 'date', ' ( `title` < \'42\' ) '],
            'float value as type date' => [42.5, 'date', ' ( `title` < \'42.5\' ) '],
            'comma value as type date' => ['42,5', 'date', ' ( `title` < \'42,5\' ) '],
            'string as number value as type date' => ['42', 'date', ' ( `title` < \'42\' ) '],
            'string as string value as type date' => ['foo', 'date', ' ( `title` < \'foo\' ) '],

            'integer value as type time' => [42, 'time', ' ( `title` < \'42\' ) '],
            'float value as type time' => [42.5, 'time', ' ( `title` < \'42.5\' ) '],
            'comma value as type time' => ['42,5', 'time', ' ( `title` < \'42,5\' ) '],
            'string as number value as type time' => ['42', 'time', ' ( `title` < \'42\' ) '],
            'string as string value as type time' => ['foo', 'time', ' ( `title` < \'foo\' ) '],

            'integer value as type datetime' => [42, 'datetime', ' ( `title` < \'42\' ) '],
            'float value as type datetime' => [42.5, 'datetime', ' ( `title` < \'42.5\' ) '],
            'comma value as type datetime' => ['42,5', 'datetime', ' ( `title` < \'42,5\' ) '],
            'string as number value as type datetime' => ['42', 'datetime', ' ( `title` < \'42\' ) '],
            'string as string value as type datetime' => ['foo', 'datetime', ' ( `title` < \'foo\' ) '],
        ];
    }

    /**
     * @test
     * @dataProvider parseReturnsValidWhereClauseForSimpleLessQueryDataProvider
     *
     * @param $number
     * @param $type
     * @param $expectedResult
     */
    public function parseReturnsValidWhereClauseForSimpleLessQuery($number, $type, $expectedResult)
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "integer",
              "operator": "less",
              "value": "42"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $query->rules[0]->value = $number;
        $query->rules[0]->type = $type;
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @return array
     */
    public function parseReturnsValidWhereClauseForSimpleLessOrEqualQueryDataProvider() : array
    {
        return [
            'integer value as integer' => [42, 'integer', ' ( `title` <= \'42\' ) '],
            'float value as integer' => [42.5, 'integer', ' ( `title` <= \'42.5\' ) '],
            'comma value as integer' => ['42,5', 'integer', ' ( `title` <= \'42,5\' ) '],
            'string as number value as integer' => ['42', 'integer', ' ( `title` <= \'42\' ) '],
            'string as string value as integer' => ['foo', 'integer', ' ( `title` <= \'foo\' ) '],

            'integer value as type string' => [42, 'string', ' ( `title` <= \'42\' ) '],
            'float value as type string' => [42.5, 'string', ' ( `title` <= \'42.5\' ) '],
            'comma value as type string' => ['42,5', 'string', ' ( `title` <= \'42,5\' ) '],
            'string as number value as type string' => ['42', 'string', ' ( `title` <= \'42\' ) '],
            'string as string value as type string' => ['foo', 'string', ' ( `title` <= \'foo\' ) '],

            'integer value as type double' => [42, 'double', ' ( `title` <= \'42\' ) '],
            'float value as type double' => [42.5, 'double', ' ( `title` <= \'42.5\' ) '],
            'comma value as type double' => ['42,5', 'double', ' ( `title` <= \'42.5\' ) '],
            'string as number value as type double' => ['42', 'double', ' ( `title` <= \'42\' ) '],
            'string as string value as type double' => ['foo', 'double', ' ( `title` <= \'foo\' ) '],

            'integer value as type date' => [42, 'date', ' ( `title` <= \'42\' ) '],
            'float value as type date' => [42.5, 'date', ' ( `title` <= \'42.5\' ) '],
            'comma value as type date' => ['42,5', 'date', ' ( `title` <= \'42,5\' ) '],
            'string as number value as type date' => ['42', 'date', ' ( `title` <= \'42\' ) '],
            'string as string value as type date' => ['foo', 'date', ' ( `title` <= \'foo\' ) '],

            'integer value as type time' => [42, 'time', ' ( `title` <= \'42\' ) '],
            'float value as type time' => [42.5, 'time', ' ( `title` <= \'42.5\' ) '],
            'comma value as type time' => ['42,5', 'time', ' ( `title` <= \'42,5\' ) '],
            'string as number value as type time' => ['42', 'time', ' ( `title` <= \'42\' ) '],
            'string as string value as type time' => ['foo', 'time', ' ( `title` <= \'foo\' ) '],

            'integer value as type datetime' => [42, 'datetime', ' ( `title` <= \'42\' ) '],
            'float value as type datetime' => [42.5, 'datetime', ' ( `title` <= \'42.5\' ) '],
            'comma value as type datetime' => ['42,5', 'datetime', ' ( `title` <= \'42,5\' ) '],
            'string as number value as type datetime' => ['42', 'datetime', ' ( `title` <= \'42\' ) '],
            'string as string value as type datetime' => ['foo', 'datetime', ' ( `title` <= \'foo\' ) '],
        ];
    }


    /**
     * @test
     * @dataProvider parseReturnsValidWhereClauseForSimpleLessOrEqualQueryDataProvider
     *
     * @param $number
     * @param $type
     * @param $expectedResult
     */
    public function parseReturnsValidWhereClauseForSimpleLessOrEqualQuery($number, $type, $expectedResult)
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "integer",
              "operator": "less_or_equal",
              "value": "42"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $query->rules[0]->value = $number;
        $query->rules[0]->type = $type;
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @return array
     */
    public function parseReturnsValidWhereClauseForSimpleGreaterQueryDataProvider() : array
    {
        return [
            'integer value as integer' => [42, 'integer', ' ( `title` > \'42\' ) '],
            'float value as integer' => [42.5, 'integer', ' ( `title` > \'42.5\' ) '],
            'comma value as integer' => ['42,5', 'integer', ' ( `title` > \'42,5\' ) '],
            'string as number value as integer' => ['42', 'integer', ' ( `title` > \'42\' ) '],
            'string as string value as integer' => ['foo', 'integer', ' ( `title` > \'foo\' ) '],

            'integer value as type string' => [42, 'string', ' ( `title` > \'42\' ) '],
            'float value as type string' => [42.5, 'string', ' ( `title` > \'42.5\' ) '],
            'comma value as type string' => ['42,5', 'string', ' ( `title` > \'42,5\' ) '],
            'string as number value as type string' => ['42', 'string', ' ( `title` > \'42\' ) '],
            'string as string value as type string' => ['foo', 'string', ' ( `title` > \'foo\' ) '],

            'integer value as type double' => [42, 'double', ' ( `title` > \'42\' ) '],
            'float value as type double' => [42.5, 'double', ' ( `title` > \'42.5\' ) '],
            'comma value as type double' => ['42,5', 'double', ' ( `title` > \'42.5\' ) '],
            'string as number value as type double' => ['42', 'double', ' ( `title` > \'42\' ) '],
            'string as string value as type double' => ['foo', 'double', ' ( `title` > \'foo\' ) '],

            'integer value as type date' => [42, 'date', ' ( `title` > \'42\' ) '],
            'float value as type date' => [42.5, 'date', ' ( `title` > \'42.5\' ) '],
            'comma value as type date' => ['42,5', 'date', ' ( `title` > \'42,5\' ) '],
            'string as number value as type date' => ['42', 'date', ' ( `title` > \'42\' ) '],
            'string as string value as type date' => ['foo', 'date', ' ( `title` > \'foo\' ) '],

            'integer value as type time' => [42, 'time', ' ( `title` > \'42\' ) '],
            'float value as type time' => [42.5, 'time', ' ( `title` > \'42.5\' ) '],
            'comma value as type time' => ['42,5', 'time', ' ( `title` > \'42,5\' ) '],
            'string as number value as type time' => ['42', 'time', ' ( `title` > \'42\' ) '],
            'string as string value as type time' => ['foo', 'time', ' ( `title` > \'foo\' ) '],

            'integer value as type datetime' => [42, 'datetime', ' ( `title` > \'42\' ) '],
            'float value as type datetime' => [42.5, 'datetime', ' ( `title` > \'42.5\' ) '],
            'comma value as type datetime' => ['42,5', 'datetime', ' ( `title` > \'42,5\' ) '],
            'string as number value as type datetime' => ['42', 'datetime', ' ( `title` > \'42\' ) '],
            'string as string value as type datetime' => ['foo', 'datetime', ' ( `title` > \'foo\' ) '],
        ];
    }

    /**
     * @test
     * @dataProvider parseReturnsValidWhereClauseForSimpleGreaterQueryDataProvider
     *
     * @param $number
     * @param $type
     * @param $expectedResult
     */
    public function parseReturnsValidWhereClauseForSimpleGreaterQuery($number, $type, $expectedResult)
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "integer",
              "operator": "greater",
              "value": "42"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $query->rules[0]->value = $number;
        $query->rules[0]->type = $type;
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @return array
     */
    public function parseReturnsValidWhereClauseForSimpleGreaterOrEqualQueryDataProvider() : array
    {
        return [
            'integer value as integer' => [42, 'integer', ' ( `title` >= \'42\' ) '],
            'float value as integer' => [42.5, 'integer', ' ( `title` >= \'42.5\' ) '],
            'comma value as integer' => ['42,5', 'integer', ' ( `title` >= \'42,5\' ) '],
            'string as number value as integer' => ['42', 'integer', ' ( `title` >= \'42\' ) '],
            'string as string value as integer' => ['foo', 'integer', ' ( `title` >= \'foo\' ) '],

            'integer value as type string' => [42, 'string', ' ( `title` >= \'42\' ) '],
            'float value as type string' => [42.5, 'string', ' ( `title` >= \'42.5\' ) '],
            'comma value as type string' => ['42,5', 'string', ' ( `title` >= \'42,5\' ) '],
            'string as number value as type string' => ['42', 'string', ' ( `title` >= \'42\' ) '],
            'string as string value as type string' => ['foo', 'string', ' ( `title` >= \'foo\' ) '],

            'integer value as type double' => [42, 'double', ' ( `title` >= \'42\' ) '],
            'float value as type double' => [42.5, 'double', ' ( `title` >= \'42.5\' ) '],
            'comma value as type double' => ['42,5', 'double', ' ( `title` >= \'42.5\' ) '],
            'string as number value as type double' => ['42', 'double', ' ( `title` >= \'42\' ) '],
            'string as string value as type double' => ['foo', 'double', ' ( `title` >= \'foo\' ) '],

            'integer value as type date' => [42, 'date', ' ( `title` >= \'42\' ) '],
            'float value as type date' => [42.5, 'date', ' ( `title` >= \'42.5\' ) '],
            'comma value as type date' => ['42,5', 'date', ' ( `title` >= \'42,5\' ) '],
            'string as number value as type date' => ['42', 'date', ' ( `title` >= \'42\' ) '],
            'string as string value as type date' => ['foo', 'date', ' ( `title` >= \'foo\' ) '],

            'integer value as type time' => [42, 'time', ' ( `title` >= \'42\' ) '],
            'float value as type time' => [42.5, 'time', ' ( `title` >= \'42.5\' ) '],
            'comma value as type time' => ['42,5', 'time', ' ( `title` >= \'42,5\' ) '],
            'string as number value as type time' => ['42', 'time', ' ( `title` >= \'42\' ) '],
            'string as string value as type time' => ['foo', 'time', ' ( `title` >= \'foo\' ) '],

            'integer value as type datetime' => [42, 'datetime', ' ( `title` >= \'42\' ) '],
            'float value as type datetime' => [42.5, 'datetime', ' ( `title` >= \'42.5\' ) '],
            'comma value as type datetime' => ['42,5', 'datetime', ' ( `title` >= \'42,5\' ) '],
            'string as number value as type datetime' => ['42', 'datetime', ' ( `title` >= \'42\' ) '],
            'string as string value as type datetime' => ['foo', 'datetime', ' ( `title` >= \'foo\' ) '],
        ];
    }

    /**
     * @test
     * @dataProvider parseReturnsValidWhereClauseForSimpleGreaterOrEqualQueryDataProvider
     *
     * @param $number
     * @param $type
     * @param $expectedResult
     */
    public function parseReturnsValidWhereClauseForSimpleGreaterOrEqualQuery($number, $type, $expectedResult)
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "integer",
              "operator": "greater_or_equal",
              "value": "42"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $query->rules[0]->value = $number;
        $query->rules[0]->type = $type;
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @return array
     */
    public function parseReturnsValidWhereClauseForSimpleBetweenQueryDataProvider() : array
    {
        return [
            'integer value as integer' => [[42,62], 'integer', ' ( (`title` > \'42\') AND (`title` < \'62\') ) '],
            'float value as integer' => [[42.5, 62.5], 'integer', ' ( (`title` > \'42.5\') AND (`title` < \'62.5\') ) '],
            'comma value as integer' => [['42,5','62,5'], 'integer', ' ( (`title` > \'42,5\') AND (`title` < \'62,5\') ) '],
            'string as number value as integer' => [['42', '62'], 'integer', ' ( (`title` > \'42\') AND (`title` < \'62\') ) '],
            'string as string value as integer' => [['foo','bar'], 'integer', ' ( (`title` > \'foo\') AND (`title` < \'bar\') ) '],

            'integer value as type string' => [[42,62], 'string', ' ( (`title` > \'42\') AND (`title` < \'62\') ) '],
            'float value as type string' => [[42.5,62.5], 'string', ' ( (`title` > \'42.5\') AND (`title` < \'62.5\') ) '],
            'comma value as type string' => [['42,5','62,5'], 'string', ' ( (`title` > \'42,5\') AND (`title` < \'62,5\') ) '],
            'string as number value as type string' => [['42','62'], 'string', ' ( (`title` > \'42\') AND (`title` < \'62\') ) '],
            'string as string value as type string' => [['foo','bar'], 'string', ' ( (`title` > \'foo\') AND (`title` < \'bar\') ) '],

            'integer value as type double' => [[42,62], 'double', ' ( (`title` > \'42\') AND (`title` < \'62\') ) '],
            'float value as type double' => [[42.5,62.5], 'double', ' ( (`title` > \'42.5\') AND (`title` < \'62.5\') ) '],
            'comma value as type double' => [['42,5','62,5'], 'double', ' ( (`title` > \'42.5\') AND (`title` < \'62.5\') ) '],
            'string as number value as type double' => [['42','62'], 'double', ' ( (`title` > \'42\') AND (`title` < \'62\') ) '],
            'string as string value as type double' => [['foo','bar'], 'double', ' ( (`title` > \'foo\') AND (`title` < \'bar\') ) '],

            'integer value as type date' => [[42,62], 'date', ' ( (`title` > \'42\') AND (`title` < \'62\') ) '],
            'float value as type date' => [[42.5,62.5], 'date', ' ( (`title` > \'42.5\') AND (`title` < \'62.5\') ) '],
            'comma value as type date' => [['42,5','62,5'], 'date', ' ( (`title` > \'42,5\') AND (`title` < \'62,5\') ) '],
            'string as number value as type date' => [['42','62'], 'date', ' ( (`title` > \'42\') AND (`title` < \'62\') ) '],
            'string as string value as type date' => [['foo','bar'], 'date', ' ( (`title` > \'foo\') AND (`title` < \'bar\') ) '],

            'integer value as type time' => [[42,62], 'time', ' ( (`title` > \'42\') AND (`title` < \'62\') ) '],
            'float value as type time' => [[42.5,62.5], 'time', ' ( (`title` > \'42.5\') AND (`title` < \'62.5\') ) '],
            'comma value as type time' => [['42,5','62,5'], 'time', ' ( (`title` > \'42,5\') AND (`title` < \'62,5\') ) '],
            'string as number value as type time' => [['42','62'], 'time', ' ( (`title` > \'42\') AND (`title` < \'62\') ) '],
            'string as string value as type time' => [['foo','bar'], 'time', ' ( (`title` > \'foo\') AND (`title` < \'bar\') ) '],

            'integer value as type datetime' => [[42,62], 'datetime', ' ( (`title` > \'42\') AND (`title` < \'62\') ) '],
            'float value as type datetime' => [[42.5,62.5], 'datetime', ' ( (`title` > \'42.5\') AND (`title` < \'62.5\') ) '],
            'comma value as type datetime' => [['42,5','62,5'], 'datetime', ' ( (`title` > \'42,5\') AND (`title` < \'62,5\') ) '],
            'string as number value as type datetime' => [['42','62'], 'datetime', ' ( (`title` > \'42\') AND (`title` < \'62\') ) '],
            'string as string value as type datetime' => [['foo','bar'], 'datetime', ' ( (`title` > \'foo\') AND (`title` < \'bar\') ) '],
        ];
    }

    /**
     * @test
     * @dataProvider parseReturnsValidWhereClauseForSimpleBetweenQueryDataProvider
     *
     * @param $number
     * @param $type
     * @param $expectedResult
     */
    public function parseReturnsValidWhereClauseForSimpleBetweenQuery($number, $type, $expectedResult)
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "integer",
              "operator": "between",
              "value": "42,62"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $query->rules[0]->value = $number;
        $query->rules[0]->type = $type;
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }

    /**
     * @return array
     */
    public function parseReturnsValidWhereClauseForSimpleNotBetweenQueryDataProvider() : array
    {
        return [
            'integer value as integer' => [[42,62], 'integer', ' ( (`title` < \'42\') AND (`title` > \'62\') ) '],
            'float value as integer' => [[42.5, 62.5], 'integer', ' ( (`title` < \'42.5\') AND (`title` > \'62.5\') ) '],
            'comma value as integer' => [['42,5','62,5'], 'integer', ' ( (`title` < \'42,5\') AND (`title` > \'62,5\') ) '],
            'string as number value as integer' => [['42', '62'], 'integer', ' ( (`title` < \'42\') AND (`title` > \'62\') ) '],
            'string as string value as integer' => [['foo','bar'], 'integer', ' ( (`title` < \'foo\') AND (`title` > \'bar\') ) '],

            'integer value as type string' => [[42,62], 'string', ' ( (`title` < \'42\') AND (`title` > \'62\') ) '],
            'float value as type string' => [[42.5,62.5], 'string', ' ( (`title` < \'42.5\') AND (`title` > \'62.5\') ) '],
            'comma value as type string' => [['42,5','62,5'], 'string', ' ( (`title` < \'42,5\') AND (`title` > \'62,5\') ) '],
            'string as number value as type string' => [['42','62'], 'string', ' ( (`title` < \'42\') AND (`title` > \'62\') ) '],
            'string as string value as type string' => [['foo','bar'], 'string', ' ( (`title` < \'foo\') AND (`title` > \'bar\') ) '],

            'integer value as type double' => [[42,62], 'double', ' ( (`title` < \'42\') AND (`title` > \'62\') ) '],
            'float value as type double' => [[42.5,62.5], 'double', ' ( (`title` < \'42.5\') AND (`title` > \'62.5\') ) '],
            'comma value as type double' => [['42,5','62,5'], 'double', ' ( (`title` < \'42.5\') AND (`title` > \'62.5\') ) '],
            'string as number value as type double' => [['42','62'], 'double', ' ( (`title` < \'42\') AND (`title` > \'62\') ) '],
            'string as string value as type double' => [['foo','bar'], 'double', ' ( (`title` < \'foo\') AND (`title` > \'bar\') ) '],

            'integer value as type date' => [[42,62], 'date', ' ( (`title` < \'42\') AND (`title` > \'62\') ) '],
            'float value as type date' => [[42.5,62.5], 'date', ' ( (`title` < \'42.5\') AND (`title` > \'62.5\') ) '],
            'comma value as type date' => [['42,5','62,5'], 'date', ' ( (`title` < \'42,5\') AND (`title` > \'62,5\') ) '],
            'string as number value as type date' => [['42','62'], 'date', ' ( (`title` < \'42\') AND (`title` > \'62\') ) '],
            'string as string value as type date' => [['foo','bar'], 'date', ' ( (`title` < \'foo\') AND (`title` > \'bar\') ) '],

            'integer value as type time' => [[42,62], 'time', ' ( (`title` < \'42\') AND (`title` > \'62\') ) '],
            'float value as type time' => [[42.5,62.5], 'time', ' ( (`title` < \'42.5\') AND (`title` > \'62.5\') ) '],
            'comma value as type time' => [['42,5','62,5'], 'time', ' ( (`title` < \'42,5\') AND (`title` > \'62,5\') ) '],
            'string as number value as type time' => [['42','62'], 'time', ' ( (`title` < \'42\') AND (`title` > \'62\') ) '],
            'string as string value as type time' => [['foo','bar'], 'time', ' ( (`title` < \'foo\') AND (`title` > \'bar\') ) '],

            'integer value as type datetime' => [[42,62], 'datetime', ' ( (`title` < \'42\') AND (`title` > \'62\') ) '],
            'float value as type datetime' => [[42.5,62.5], 'datetime', ' ( (`title` < \'42.5\') AND (`title` > \'62.5\') ) '],
            'comma value as type datetime' => [['42,5','62,5'], 'datetime', ' ( (`title` < \'42,5\') AND (`title` > \'62,5\') ) '],
            'string as number value as type datetime' => [['42','62'], 'datetime', ' ( (`title` < \'42\') AND (`title` > \'62\') ) '],
            'string as string value as type datetime' => [['foo','bar'], 'datetime', ' ( (`title` < \'foo\') AND (`title` > \'bar\') ) '],
        ];
    }

    /**
     * @test
     * @dataProvider parseReturnsValidWhereClauseForSimpleNotBetweenQueryDataProvider
     *
     * @param $number
     * @param $type
     * @param $expectedResult
     */
    public function parseReturnsValidWhereClauseForSimpleNotBetweenQuery($number, $type, $expectedResult)
    {
        $query = '{
          "condition": "AND",
          "rules": [
            {
              "id": "title",
              "field": "title",
              "type": "string",
              "input": "integer",
              "operator": "not_between",
              "value": "42,62"
            }
          ],
          "valid": true
        }';
        $query = json_decode($query);
        $query->rules[0]->value = $number;
        $query->rules[0]->type = $type;
        self::assertEquals($expectedResult, $this->subject->parse($query, $this->table));
    }
}
