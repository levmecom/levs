<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-11-08 14:21
 *
 * 项目：rm  -  $  - RewriteHelper.php
 *
 * 作者：liwei 
 */

namespace lev\helpers;

!defined('INLEV') && exit('Access Denied LEV');


use Lev;
use lev\base\Controllerv;
use lev\base\Modulesv;
use lev\base\Rewritev;

class RewriteHelper
{

    public static function checkOpen() {
        return is_file(Lev::$aliases['@webroot'] . '/levs_rewrite.php') && cacheFileHelpers::getc('levs_rewrite', false);
    }

    public static function toReRoute(array $params = [], $scheme = false)
    {
        if (!static::checkOpen()) {
            return Lev::toReRoute($params, $scheme);
        }
        return static::getRewriteRoute($params, $scheme);
    }

    /**
     * rewrite开启 URL美化，替换掉【&?】号。后台管理地址不建议使用
     * 注意 中划线【-】【.html】为保留使用符号以及后缀，$_GET参数键与值均不能包含。否则会出现意想不到的结果
     * @param array $params
     * @param bool $scheme
     * @return string
     */
    public static function getRewriteRoute(array $params = [], $scheme = false) {
        $iden = Modulesv::getIdenRouteId(isset($params['id']) ? $params['id'] : Lev::$app['iden']);
        $uri = Rewritev::getBaseUrl().'/'.str_replace(':', '-', $iden);

        if (!empty($params[0])) {
            if (UrlHelper::check($params[0]) || ($params[0] != '/' && strpos($params[0], '/') === 0)) {
                return $params[0];
            }
            parse_str('r='.$params[0], $result);
            !empty($result) && $params += $result;
        }

        $route = !empty($params['r']) && $params['r'] != '/' ? $params['r'] : '';

        $opid = empty($params['opid']) ? '/' : '-'.$params['opid'].'.html';
        unset($params[0], $params['id'], $params['r'], $params['opid']);
        if (!empty($params)) {
            $uriPm = [];
            foreach ($params as $k => $v) {
                $v !== null && !static::tempKey($k) && $uriPm[] = $v.'-'.$k;
            }
        }
        if (empty($uriPm)) {
            $route && $uri .= '/'. $route;
        }else {
            if ($route) {
                $uri .= '/' . implode('/', Controllerv::getPageRoute($route)) . '/' . implode('-', $uriPm);
            }else {
                $uri .= (strpos($iden, ':') !== false ? '' : '-'.$iden) . '-' . implode('-', $uriPm);
            }
        }
        $scheme && $uri = Lev::$aliases['@hostinfo'] . $uri;
        return rtrim($uri.$opid, '/');
    }

    public static function tempKey($key) {
        return strpos($key, '##') === 0;
    }
}