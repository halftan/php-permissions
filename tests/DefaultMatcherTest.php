<?php
namespace PermissionsTest;


use Permissions\Authorizer;
use Permissions\Matcher\DefaultMatcher;

class DefaultMatcherTest extends \PHPUnit_Framework_TestCase
{

    protected static $allPermits;
    protected static $aRules;

    public static function setUpBeforeClass()
    {
        self::$allPermits = require __DIR__ . '/fixtures/permits_fixture.php';
        self::$aRules = (new Authorizer([], self::$allPermits))->getAllPermitItems();
    }

    /**
     * @dataProvider urlProvider
     */
    public function testBasicPermitMatch($sUrl, $sMethod, $aDesired)
    {
        $matcher = new DefaultMatcher($sUrl, $sMethod, self::$aRules);
        $this->assertEquals($aDesired, $matcher->match());
    }

    public function urlProvider()
    {
        return [
            [
                'one/two?param=test',
                'GET',
                ['one.two'],
            ],
            [
                'one/two?param=test',
                'POST',
                ['one.two_POST'],
            ],
            [
                'one/two/three',
                'GET',
                [],
            ],
            [
                'one/two/getonly',
                'GET',
                ['one.two.getonly_GET'],
            ],
            [
                'one/two/getonly',
                'POST',
                [],
            ],
            [
                'one/two/postonly',
                'POST',
                ['one.two.postonly_POST'],
            ],
            [
                'one/two/postonly',
                'GET',
                [],
            ]
        ];
    }
}
