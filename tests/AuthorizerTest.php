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
    /**
     * @dataProvider stringProvider
     */
    public function testDecamelize($sCamel, $sUnderscore)
    {
        // No config and no permits. Only for test.
        $obj = new Authorizer([], []);
        $this->assertEquals($sUnderscore, $obj->decamelize($sCamel));
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
