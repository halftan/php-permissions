<?php
/**
 * Created by PhpStorm.
 * User: halftan
 * Date: 15/七月/15
 * Time: 18:14
 */

namespace PermissionsTest;


use Permissions\Authorizer;

class AuthorizerTest extends \PHPUnit_Framework_TestCase
{

    protected static $allPermits;

    public static function setUpBeforeClass()
    {
        self::$allPermits = require __DIR__ . '/fixtures/permits_fixture.php';
    }

    /**
     * @dataProvider stringProvider
     */
    public function testDecamelize($sCamel, $sUnderscore)
    {
        // No config and no permits. Only for test.
        $obj = new Authorizer([], []);
        $this->assertEquals($sUnderscore, $obj->decamelize($sCamel));
    }

    public function testRuleParse()
    {
        $obj = new Authorizer([], self::$allPermits);
        // var_dump($obj->getAllPermitItems());
        $this->assertArraySubset(array(
            [
                'url'    => ['one'],
                'method' => 'ALL',
                'id'     => 'one'
            ],
            [
                'url'    => ['one', 'two'],
                'method' => 'ALL',
                'id'     => 'one.two'
            ],
            [
                'url'    => ['one', 'two'],
                'method' => 'POST',
                'id'     => 'one.two_POST'
            ],
            [
                'url'    => ['one', 'two', 'postonly'],
                'method' => 'POST',
                'id'     => 'one.two.postonly_POST'
            ],
        ), $obj->getAllPermitItems(), 'Rules subset not match.');
    }

    public function stringProvider()
    {
        return [
            ['OneTwoThree', 'one_two_three'],
            ['oneTwo_Three', 'one_two_three'],
            ['oneTwo__POST', 'one_two_post'],
            ['one_two_three', 'one_two_three'],
        ];
    }
}
