<?php
namespace Permissions\Matcher;

use Permissions\IMatcher;

/**
 *
 * Default permit matcher class.
 * Provides default matcher function.
 *
 * @author Andy Zhang
 */
class DefaultMatcher implements IMatcher
{
    /**
     * @var string
     */
    protected $sMethod;

    /**
     * @var array[string]
     */
    protected $aUrl;

    /**
     * @var string Imploded url string. Match with permit ids.
     */
    protected $sUrl;

    /**
     * @var array  $aQuery URL Query String，eg: ['service_id'=>'1', 'cmd_id'=>'5']
     */
    protected $aQuery;

    /**
     * @var array
     */
    protected $aRules;

    /**
     * @param string
     * @param string
     * @param array
     */
    /**
     * 权限匹配函数说明
     *
     * @param array  $aUrl 请求的URL地址，以'/'分片 eg: ['op', 'content']
     * @param string $sMethod HTTP请求动作 eg: GET, POST, PUT ...
     * @param array  $aRules 由数据库表permit_item读取到的权限id，解析为匹配规则。
     */
    public function __construct($sUrl, $sMethod, $aRules)
    {
        $aUrlInfo = parse_url($sUrl);
        $sUrl     = $aUrlInfo['path'];

        $this->aUrl    = explode('/', $sUrl);
        $this->sUrl    = implode('.', $this->aUrl);
        $this->sMethod = $sMethod;
        $this->aRules  = $aRules;

        if (isset($aUrlInfo['query'])) {
            $sQuery = $aUrlInfo['query'];
            parse_str($sQuery, $this->aQuery);
        } else {
            $this->aQuery = array();
        }
    }

    /**
     * Returns the first node of request uri
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->aUrl[0];
    }

    /**
     * Returns the url query parameter in an array.
     *
     * @return array
     */
    public function getQuery()
    {
        return $this->aQuery;
    }

    /**
     * 这个Matcher只会返回最长的id串，也就是说只匹配颗粒度最细的一项权限
     *
     * @return array 权限id数组，id必须与数据库permit_item表中的id一致
     *       数组index从0开始
     */
    public function match()
    {
        $aResult = array_values(array_map(function($var) {
            return $var['id'];
        }, array_filter($this->aRules, function($var) {
            if ($var['method'] !== "ALL"
                && $var['method'] !== $this->sMethod) {
                return false;
            }
            $sTestUrl = implode('.', $var['url']);
            if ($sTestUrl !== $this->sUrl) {
                return false;
            }
            return true;
        })));
        $aResult = array_reduce($aResult, function ($carry, $item) {
            if (strlen($carry) < strlen($item)) {
                return $item;
            } else {
                return $carry;
            }
        });
        if (empty($aResult)) {
            return array();
        } else {
            return array($aResult);
        }
    }
}
