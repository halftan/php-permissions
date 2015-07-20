<?php
namespace Permissions;

/**
 *
 * Authorizer helper.
 * Inflates request's necessary permit_items from url & HTTP verb.
 * Also provides method to fetch all permit_items available.
 *
 * @author Andy Zhang
 */
class Authorizer
{
    /** @var array **/
    private $aAllPermits;

    /** @var array **/
    private $aConfig;

    /** @var array $aRules 所有权限项规则 **/
    private static $aRules;

    /**
     * 初始化时传入两个参数 aConfig, aAllPermits
     *
     * @param array $aConfig 配置参数。至少含有 '__default' => "Namespace\\Matcher"
     * @param array $aAllPermits 所有权限项
     *
     */
    public function __construct(array $aConfig, array $aAllPermits)
    {
        $this->aAllPermits = $aAllPermits;
    }

    /**
     *
     * 返回当前访问的URI所需要的所有权限项
     *
     * @param string
     * @param string
     *
     * @return array
     */
    public function inflatePermitItems($sUri, $sMethod)
    {
        $aRules   = $this->getAllPermitItems();
        $sUri     = substr($sUri, 1);
        $sService = explode('/', $sUri)[0];
        if (isset($this->aConfig[$sService])) {
            $MatcherClass = $this->aConfig[$sService];
        } else {
            $MatcherClass = $this->aConfig['__default'];
        }
        /** @var IMatcher $Matcher **/
        $Matcher = new $MatcherClass($sUri, $sMethod, $aRules);

        $aResult = $Matcher->match();
        return $aResult;
    }

    /**
     * @param string $input
     * @return string string
     */
    public function decamelize($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    public function getAllPermitItems()
    {
        if (empty(self::$aRules)) {
            self::$aRules = array();
            foreach ($this->aAllPermits as $perm) {
                $sPermit    = is_array($perm) ? $perm['id'] : $perm;
                $aUrl       = explode('.', $sPermit);
                $sLastBlock = array_pop($aUrl);

                if (strpos($sLastBlock, '_')) {
                    list($sSLastBlock, $sMethod) = explode('_', $sLastBlock);
                    $sMethod = strtoupper($sMethod);
                    if (in_array($sMethod, array("GET", "POST", "PUT", "DELETE"))) {
                        $sLastBlock = $sSLastBlock;
                    } else {
                        $sMethod = 'ALL';
                    }
                } else {
                    $sMethod = 'ALL';
                }
                array_push($aUrl, $sLastBlock);
                self::$aRules[] = array(
                    'url'    => array_map([$this, 'decamelize'], $aUrl),
                    'method' => $sMethod,
                    'id'     => $sPermit,
                );
            }
        }
        return self::$aRules;
    }
}
