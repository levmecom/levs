<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-04 08:59
 *
 * 项目：upload  -  $  - LevHelper.php
 *
 * 作者：liwei 
 */

//此类存放常用方法

namespace lev;

use Lev;
use lev\base\Modulesv;
use lev\base\Requestv;
use lev\base\Viewv;
use lev\ext\LevMobileDetect;
use lev\helpers\cacheFileHelpers;
use lev\helpers\ModulesHelper;
use lev\helpers\RewriteHelper;
use modules\levs\helpers\siteHelper;

require_once __DIR__ . '/BaseLev.php';

!defined('INLEV') && exit('Access Denied LEV');

class LevHelper extends \lev\BaseLev
{

    public static function SiteIden() {
        return ($iden = cacheFileHelpers::getc('SiteIndex')) && ModulesHelper::isInstallModule($iden) && !Lev::GPv('r') ? $iden : APPVIDEN;
    }

    public static function getCsrf($hash = '') {
        return function_exists ('formhash') ? formhash($hash) :
            md5(substr(Lev::$app['timestamp'], 0, -7).Lev::$app['uid'].Lev::$app['authkey'].Lev::$app['cookies']['pre'].Requestv::getUserAgent().$hash);
    }

    public static function strlenv($str) {
        if(strtolower(Lev::$app['charset']) != 'utf-8') {
            return strlen($str);
        }
        $count = 0;
        for($i = 0; $i < strlen($str); $i++){
            $value = ord($str[$i]);
            if($value > 127) {
                $count++;
                if($value >= 192 && $value <= 223) $i++;
                elseif($value >= 224 && $value <= 239) $i = $i + 2;
                elseif($value >= 240 && $value <= 247) $i = $i + 3;
            }
            $count++;
        }
        return $count;
    }

    public static function objectToArray($object) {
        is_object($object) && $object = (array)$object;
        if (is_array($object)) {
            foreach ($object as $k =>  $v) {
                $object[$k] = static::objectToArray($v);
            }
        }
        return $object;
    }

    public static function checkBaiduboxappUserAgent($userAgent = '') {
        return static::ckUserAgent('baiduboxapp/', $userAgent);
    }
    public static function checkAlipayUserAgent($userAgent = '') {
        return static::ckUserAgent('AlipayClient/', $userAgent);
    }
    public static function checkQqUserAgent($userAgent = '') {
        return static::ckUserAgent('MQQBrowser/', $userAgent) && static::ckUserAgent('QQ/', $userAgent);
    }
    public static function checkWxUserAgent($userAgent = '', $wechat = true) {
        return $wechat ? static::ckUserAgent('MicroMessenger/', $userAgent) && static::ckUserAgent('WeChat/', $userAgent) :
            static::ckUserAgent('MicroMessenger/', $userAgent);
    }
    public static function ckUserAgent($needle, $userAgent = '') {
        return stripos($userAgent ?: Requestv::getUserAgent(), $needle);
    }

    public static function marketsFooter() {
        return Lev::actionObjectMethodIden('markets', 'modules\levs\modules\markets\helpers\marketsSetHelper', [], 'marketsFooter');
    }

    public static function getNotices() {
        $str = Lev::$app['notices'];
        return !$str ? '' : <<<html
            <hiddenx><cookie-notices>$str</cookie-notices></hiddenx>
            <div class="LoadPageAjaxJS"><script>jQuery(function (){actionLocalStorage("cookieNotices", jQuery("hiddenx cookie-notices").html());});</script></div>
html;
    }
    public static function setNotices($string, $saveCookie = false) {
        if ($saveCookie) {
            $_COOKIE['_notices'] = $string;
            static::opCookies('_notices', $string, 60, false);
        }
        static::setAppNotices($string);
    }

    public static function setAppNotices($string) {
        Lev::$app['notices'] .= $string;
    }

    /**
     * @param string $key
     * @param null $val
     * @param int $expire
     * @param bool $pre
     * @param bool $secure
     * @param bool $httpOnly
     * @return bool|mixed
     */
    public static function opCookies($key, $val = null, $expire = 60, $pre = true, $secure = false, $httpOnly = false) {

        $key = ($pre ? Lev::$app['cookies']['pre'] : '').$key;
        if ($val !== null) {
            $_COOKIE[$key] = $val;
            return static::setcookiev($key, $val, Lev::$app['timestamp'] + $expire, $secure, $httpOnly);
        }else if (isset($_COOKIE[$key])) {//var_dump($key);var_dump($expire);var_dump($secure);var_dump($val);print_r($_COOKIE);
            $valr = $_COOKIE[$key];
            if ($expire <0) {
                static::setcookiev($key, $val, Lev::$app['timestamp'] - 31536000, $secure, $httpOnly);
                unset($_COOKIE[$key]);
            }//print_r($_COOKIE);
            return  $valr;
        }
        return '';
    }
    public static function setcookiev($key, $val = null, $expire = 0, $secure = false, $httpOnly = false, $sameSite = null) {
        $secure = Requestv::getIsSecureConnection();

        $path =  Lev::$app['cookies']['path'];
        $domain = Lev::$app['cookies']['domain'];
        if (PHP_VERSION_ID >= 70300) {
            return setcookie($key, $val, [
                'expires' => $expire,
                'path' => $path,
                'domain' => $domain,
                'secure' => $secure,
                'httpOnly' => $httpOnly,
                'sameSite' => !empty($sameSite) ? $sameSite : null,
            ]);
        } else {
            // Work around for setting sameSite cookie prior PHP 7.3
            // https://stackoverflow.com/questions/39750906/php-setcookie-samesite-strict/46971326#46971326
            if (!is_null($sameSite)) {
                $path .= '; samesite=' . $sameSite;
            }
            return setcookie($key, $val, $expire, $path, $domain, $secure, $httpOnly);
        }
    }

    /**
     * @param $money
     * @param int $dot
     * @param string $flag
     * @return string
     */
    public static function formatMoney($money, $dot = 0, $flag = '') {
        if (!is_numeric($money)) {
            return $money;
        }
        $money = floatval($money);
        $dot = $dot ?: min(max(strlen(strstr($money, '.')) - 1, 2), 5);
        if ($flag === 'num') {
            $rmb = static::formatNumber(number_format($money, $dot, '.', ''));
        }else {
            $rmb = number_format($money, $dot, '.', $flag);
        }
        return $rmb;
    }

    public static function formatNumber($num) {
        if ($num <1000) $str = $num;
        elseif ($num <10000) $str = round($num/1000, 1).'千';
        elseif ($num <100000000) $str = round($num/10000, 1).'万';
        else $str = round($num/100000000, 2).'亿';
        return $str;
    }

    public static function globs($dir, $exceptDirs = [], $exceptFiles = [], $result = []) {
        $globs = glob(rtrim($dir, '/').'/*');
        foreach ($globs as $v) {
            !isset($exceptFiles[$v]) && $result[$v] = 1;
            if (is_dir($v) && !isset($exceptDirs[$v])) {
                $result = static::globs($v, $exceptDirs, $exceptFiles, $result);
            }
        }
        return $result;
    }

    /**
     * @param string|array $str
     * @param bool $assoc
     * @param int $options
     * @return false|mixed|string
     */
    public static function jsonv($str, $assoc = true, $options = 0) {
        return is_array($str) ? json_encode($str, $options) : json_decode($str, $assoc);
    }

    public static function setSettings($settings) {
        is_array($settings) && $settings = serialize($settings);
        return $settings;
    }
    public static function getSettings($_settings, $key = false, $val = false) {
        $settings = $_settings && !is_array($_settings) ? static::unserializev($_settings) : $_settings;
        if ($val !== false && $key !== false) {
            $settings[$key] = $val;
            return $settings;
        }else if ($key === false) {
            return $settings ?: [];
        }
        return Lev::arrv($key, $settings, '');
    }
    public static function unserializev($string) {
        if(($res = unserialize($string)) === false) {
            $res = unserialize(stripslashes($string));
        }
        return $res;
    }

    public static function getRouteModule($route = '') {
        !$route && $route = Lev::stripTags(static::GPv('r'));
        if ($route) {
            return explode('/', $route)[0];
        }
        return false;
    }

    /**
     * 加载模块设置项
     * @param $iden
     * @param array|null $settings
     */
    public static function setAppSettings($iden, array $settings = null) {
        Lev::$app['settings'][$iden] = $settings !== null ? $settings : static::loadFileSettings($iden);
    }

    /**
     * 模块配置未找到，前往APPVIDEN主模块查找配置
     * @param $key
     * @param bool $iden
     * @return string
     */
    public static function stgetv($key, $iden = false) {
        $iden === false && $iden = Lev::$app['iden'];
        !isset(Lev::$app['settings'][$iden]) && static::setAppSettings($iden);
        if (!isset(Lev::$app['settings'][$iden][$key])) {
            $iden != APPVIDEN &&
            !isset(Lev::$app['settings'][APPVIDEN]) && static::setAppSettings(APPVIDEN);
            //Lev::$app['settings'][APPVIDEN] = static::loadFileSettings(APPVIDEN);
            $iden = APPVIDEN;
        }
        return isset(Lev::$app['settings'][$iden][$key]) ? Lev::$app['settings'][$iden][$key] : '';
    }

    /**
     * @param $key
     * @param bool $iden
     * @return string|array
     */
    public static function stget($key, $iden = false) {
        $iden === false && $iden = Lev::$app['iden'];
        !isset(Lev::$app['settings'][$iden]) && static::setAppSettings($iden);
        return isset(Lev::$app['settings'][$iden][$key]) ? Lev::$app['settings'][$iden][$key] : '';
    }

    public static function loadFileSettings($iden = 'settings') {
        return is_file($file = Lev::getAlias('@settings').'/'.$iden.'.php') ? include $file : [];
    }

    public static function isAjax() {
        return (Requestv::isAjax() || static::GPv('inajax') || Lev::GPv('ziframescreen') == 5);
    }

    /**
     * @param int $status 正数：成功，负数：失败
     * @param string $message
     * @param array $ext
     * @return array
     */
    public static function responseMsg($status = 1, $message = null, $ext = []) {
        $message === null &&
        $message = $status === -5 ? '抱歉，请先登陆' : ($status <0 ? '操作失败' : '操作成功');
        if ($status <0) {
            $ext['error'] = ['message'=>$message];
        }else {
            $ext['succeed'] = $status;
        }
        $ext['message'] = $message;
        $ext['status'] = $status;
        return $ext;
    }

    public static function csrfValidation() {
        return static::$app['_csrf'] === static::GPv('_csrf');
    }

    public static function dataUpload() {
        return '/data/ups/';
    }

    public static function uploadRealSrc($src) {
        if (strpos($src, '#')    === 0) return $src;
        if (strpos($src, 'http') === 0) return $src;
        if (strpos($src, '@')    === 0) return Lev::getAlias($src);

        $dataUpload = static::dataUpload();
        $appweb = static::$aliases['@web'].static::$aliases['@appweb'];
        return (strpos($src, static::$aliases['@appweb'].$dataUpload) === 0 ? $appweb : $appweb.$dataUpload) . ltrim($src, '/');
    }
    public static function uploadRootSrc($src) {
        if (strpos($src, '#')    === 0) return $src;
        if (strpos($src, 'http') === 0) return $src;
        if (strpos($src, '@')    === 0) return Lev::getAlias($src);

        $dataUpload = static::dataUpload();
        $appweb = static::$aliases['@webroot'].static::$aliases['@appweb'];
        return (strpos($src, static::$aliases['@appweb'].$dataUpload) === 0 ? $appweb : $appweb.$dataUpload) . ltrim($src, '/');
    }
    public static function rootsrcToWebsrc($src) {
        return strpos($src, static::$aliases['@webroot']) === 0 ? substr($src, strlen(static::$aliases['@webroot'])) : $src;
    }

    /**
     * url转换。eg: data-list -> DataList
     * @param $r
     * @return string
     */
    public static function ucfirstv($r) {
        return strpos($r, '-') !== false ? implode('', array_map(function ($val) { return ucfirst($val); }, explode('-', $r))) : ucfirst($r);
    }

    public static function decodeHtml($str, $decode = true, $all = true) {
        if (is_array($str)) {
            foreach ($str as $k => $v) {
                $str[$k] = static::decodeHtml($v, $decode, $all);
            }
        }else {
            if ($all) {
                $decodes = ['&', '"', "'", '>', '<', '(', ')'];
                $encodes = ['&0amp;', '&0quot;', '&0apos;', '&0gt;', '&0lt;', '&99z;', '&00z;'];
            }else {
                $decodes = ['"', "'", '>', '<', '(', ')', '&'];
                $encodes = ['&0quot;', '&0apos;', '&0gt;', '&0lt;', '&99z;', '&00z;', '&0amp;'];
            }
            return $decode ? str_replace($encodes, $decodes, $str) : str_replace($decodes, $encodes, $str);
        }
        return $str;
    }

    /**
     * 移除 script 脚 本
     *  .* 不能匹配换行
     *  [\s\S]* 支持匹配换行
     *  ? 表示匹配最近的一个结束标签</script>
     *  i 不区分大小写
     * $string = preg_replace('/<script[\s\S]*?<\/script>/i', '', $string);
     * @param $string
     * @return string|string[]|null
     */
    public static function removeScript($string){
        if ($string) {
            $pregfind = array("/<script.*>.*<\/script>/siU", '/on(mousewheel|mouseover|click|load|onload|submit|focus|blur)="[^"]*"/i');
            $pregreplace = array('', '');
            $string = preg_replace($pregfind, $pregreplace, $string);
        }
        return $string;
    }
    public static function removeCodeHtmlScript($string) {
        return $string ? Lev::decodeHtml(Lev::removeScript(Lev::decodeHtml($string)), false) : $string;
    }

    public static function toSetRoute(array $params = [], $scheme = true) {
        isset($params['iden']) || $params['iden'] = Lev::$app['iden'];
        $params[0] = 'superman/settings';
        $params['id'] = APPVIDEN;
        return static::toReRoute($params, $scheme);
    }

    public static function toReWrRoute(array $params = [], $scheme = true) {
        if (RewriteHelper::checkOpen()) {
            return RewriteHelper::getRewriteRoute($params, $scheme);
        }
        return static::toReRoute($params, $scheme);
    }

    public static function toReRoute(array $params = [], $scheme = true) {
        $params['id'] = Modulesv::getIdenRouteId(isset($params['id']) ? $params['id'] : Lev::$app['iden']);
        if (!isset($params[0])) {
            $params[0] = '';
        }else if (strpos($params[0], 'http') === 0 || stripos($params[0], '.php') !== false) {
        }else if (Lev::$app['homeFile'] && stripos(Lev::$app['homeFile'], '.php') !== false) {
            $params[0] = '/'.Lev::$app['homeFile'].(stripos(Lev::$app['homeFile'], '.php?') !== false ? '&':'?')
                .($params[0] !='/' ? 'r='.trim($params[0], '/') : '');
        }
        return static::toRoute($params, $scheme);
    }

    public static function toPgRoute($pm = []) {
        $pm[0] = '/plugin.php?id='.(isset($pm['id']) ? $pm['id'] : Lev::$app['iden']).(!empty($pm[0]) ? ':'.$pm[0] : '');
        unset($pm['id']);
        return static::toRoute($pm);
    }

    public static function toCurrent(array $params = [], $scheme = true, $rewrite = true)
    {
        $route = $_GET;
        $route[0] = Requestv::getScriptUrl();
        //$route = array_replace_recursive($route, $params);
        $params && $route = $params + $route;
        if (strpos($route[0], 'levs_rewrite.php') !== false) {
            if ($rewrite) {
                unset($route[0]);
                return static::toReWrRoute($route, $scheme);
            }
            foreach ($route as $k => $v) {
                if (RewriteHelper::tempKey($k)) unset($route[$k]);
            }
            $route[0] = str_replace('levs_rewrite.php', 'levs.php', $route[0]);
        }
        return static::toRoute($route, $scheme);
    }

    public static function toRoute($pm = [], $scheme = true) {
        $link = empty($pm[0]) ? '' : $pm[0];
        if (!$link) {
            return $link;
        }
        unset($pm[0]);

        strpos($link, '@') === 0 && $link = Lev::getAlias($link);

        if (strpos($link, '/') === 0) {
            $web = Lev::getAlias('@web');
            if (!$web || strpos($link, $web) !== 0) {
                $link = Lev::getAlias('@siteurl').$link;
            }else {
                $link = Lev::getAlias('@hostinfo').$link;
            }
        }
        $pm += static::getUrlParam($link);
        $link = $pm[0];
        unset($pm[0]);
        $pm && $link .= '?'.urldecode(http_build_query($pm));

        !$scheme && strpos($link, $hostinfo = Lev::getAlias('@hostinfo')) === 0 && $link = substr($link, strlen($hostinfo));
        return $link;
    }

    public static function getUrlParam($url) {
        if (strpos($url, '?') !== false) {
            $pstr = explode('?', $url);
            $param[0] = $pstr[0];
            $arr = explode('&', $pstr[1]);
            foreach ($arr as $v) {
                $one = Lev::stripTags(explode('=', $v));
                isset($one[1]) && $param[$one[0]] = $one[1];
            }
        }else {
            $param[0] = $url;
        }
        return $param;
    }

    /**
     * 将多维追加到一维
     * @param $delimiter
     * @param $array
     * @param bool $unique
     * @return string
     */
    public static function implodev($delimiter, $array, $unique = true) {
        $arr = &$array;
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                unset($arr[$k]);
                foreach ($v as $r) {
                    $arr[] = $r;
                }
            }
        }
        $unique && $arr = array_unique($arr);
        return implode($delimiter, $arr);
    }

    public static function explodev($string, $delimiter = ',', $trim = true, $skipEmpty = true)
    {
        $result = explode($delimiter, $string);
        if ($trim !== false) {
            if ($trim === true) {
                $trim = 'trim';
            } elseif (!is_callable($trim)) {
                $trim = function ($v) use ($trim) {
                    return trim($v, $trim);
                };
            }
            $result = array_map($trim, $result);
        }
        if ($skipEmpty) {
            // Wrapped with array_values to make array keys sequential after empty values removing
            $result = array_values(array_filter($result, function ($value) {
                return $value !== '';
            }));
        }

        return $result;
    }

    /**
     * 加前缀0 01 001 ...
     * @param $noarr
     * @param int $dnum
     * @param bool $intval
     * @return array|string
     */
    public static function addx0($noarr, $dnum = 2, $intval = true) {
        if (is_array($noarr)) {
            foreach ($noarr as $k => $no) {
                $noarr[$k] = sprintf('%0' . $dnum . 'd', $intval ? intval($no) : trim($no));
            }
        }else {
            $noarr = sprintf('%0' . $dnum . 'd', $intval ? intval($noarr) : trim($noarr));
        }
        return $noarr;
    }

    public static function GPv($key) {
        return isset($_GET[$key]) ? $_GET[$key] : (isset($_POST[$key]) ? $_POST[$key] : null);
    }
    public static function GETv($key) {
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }
    public static function POSTv($key) {
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }

    /**
     * @param string|array|integer $value
     * @param array $ext
     * @return array|string
     */
    public static function stripTags($value, $ext = []) {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $ext && in_array($k, $ext) ? $v : self::stripTags($v, $ext);
            }
        }else {
            return trim(strip_tags($value));
        }
        return $value;
    }

    public static function ckmobile() {
        static $ck;
        !isset($ck) && $ck = Lev::checkMobile();
        return $ck;
    }
    public static function checkMobile() {
        if (!empty($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'Electron/') !== false) {
            return true;
        }
        if (!isset(Lev::$app['isMobile'])) {
            $mobile = new LevMobileDetect();
            Lev::$app['isMobile'] = $mobile->isMobile() ? 1 : 0;
            Lev::$app['isTablet'] = $mobile->isTablet() ? 1 : 0;
        }
        return Lev::$app['isMobile'];
    }

    /**
     * @param $string
     * @param $length
     * @param string $dot
     * @return mixed|string
     */
    public static function cutString($string, $length, $dot = ' ...', $charset = '') {
        if(strlen($string) <= $length) {
            return $string;
        }

        $string = trim(strip_tags($string));

        $pre = chr(1);
        $end = chr(1);
        $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

        $strcut = '';
        $charset = $charset ?: Lev::$app['charset'];
        if(strtolower($charset) == 'utf-8') {

            $n = $tn = $noc = 0;
            while($n < strlen($string)) {

                $t = ord($string[$n]);
                if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                    $tn = 1; $n++; $noc++;
                } elseif(194 <= $t && $t <= 223) {
                    $tn = 2; $n += 2; $noc += 2;
                } elseif(224 <= $t && $t <= 239) {
                    $tn = 3; $n += 3; $noc += 2;
                } elseif(240 <= $t && $t <= 247) {
                    $tn = 4; $n += 4; $noc += 2;
                } elseif(248 <= $t && $t <= 251) {
                    $tn = 5; $n += 5; $noc += 2;
                } elseif($t == 252 || $t == 253) {
                    $tn = 6; $n += 6; $noc += 2;
                } else {
                    $n++;
                }

                if($noc >= $length) {
                    break;
                }

            }
            if($noc > $length) {
                $n -= $tn;
            }

            $strcut = substr($string, 0, $n);

        } else {
            $_length = $length - 1;
            for($i = 0; $i < $length; $i++) {
                if(ord($string[$i]) <= 127) {
                    $strcut .= $string[$i];
                } else if($i < $_length) {
                    $strcut .= $string[$i].$string[++$i];
                }
            }
        }

        $strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

        $pos = strrpos($strcut, chr(1));
        if($pos !== false) {
            $strcut = substr($strcut,0,$pos);
        }
        return $strcut.$dot;
    }
    public static function cutString2($str, $length, $preLen = 0, $dot = '***') {
        $preStr = $preLen >0 ? static::cutString($str, $preLen, '') : '';
        if ($length <0) {
            $strlen = strlen($str);
            $cutstr = static::cutString($str, $strlen+$length, '');
            $cutstr = $preStr.$dot.substr($str, strlen($cutstr));
        }else {
            $cutstr = static::cutString($str, $length, $dot);
        }
        return $cutstr;
    }

    public static function arrv($key, $arr = [], $default = null) {
        if (is_array($key)) {
            return static::arrv($key[1], (isset($arr[$key[0]]) ? $arr[$key[0]] : []), $default);
        }
        return isset($arr[$key]) ? $arr[$key] : ($default !== null ? $default : $key);
    }

    public static function base64_encode_url($string) {
        return str_replace(['+','/','='], ['-','_',''], base64_encode(($string)));
    }

    public static function base64_decode_url($string) {
        return (base64_decode(str_replace(['-','_'], ['+','/'], $string)));
    }

    public static function weekDay($timestamp = 0, $name = '周') {
        $week = ['日', '一', '二', '三', '四', '五', '六'];
        $n = date('w', $timestamp ?: time());
        return $name.$week[$n];
    }

    /**
     * 当天0点
     * @return int
     */
    public static function getDayStartTime() {
        return strtotime(date('Y-m-d', Lev::$app['timestamp']));
    }

    /**
     * 最近周的周一0点
     * @return false|int
     */
    public static function getWeekStartTime() {
        $time = Lev::$app['timestamp'];
        $n = date('N', $time);
        return strtotime(date('Y-m-d', $time - ($n-1)*24*3600));
    }

    /**
     * 最近月的1号0点
     * @return false|int
     */
    public static function getMonthStartTime() {
        return strtotime(date('Y-m', Lev::$app['timestamp']).'-01');
    }

    public static function mtrandv($length = 1, $randstr = 'qwertyuioplkjhgfdsazxcvbnm1234567890') {
        $str = '';
        $max = strlen($randstr.=$randstr.$randstr) - 1;
        for ($i=0; $i <$length; $i++) {
            $a = $randstr[mt_rand(0, $max)];
            $str .= !is_numeric($a) && mt_rand(0,1) ? strtoupper($a) : $a;
        }
        return $str;
    }

    public static function authcodev($string, $un = false, $key = '', $expiry = 0) {

        $ckey_length = 4;

        $key = md5($key ? $key : Lev::$app['authkey']);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($un ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);

        $string = $un ? Lev::base64_decode_url(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if($un) {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.str_replace('=', '', Lev::base64_encode_url($result));
        }

    }

    /**
     * 递归数组转换编码
     * @param $str
     * @param string $in_charset
     * @param string $out_charset
     * @return array|string
     */
    public static function iconvs($str, $in_charset = 'UTF-8', $out_charset = 'GBK') {
        //$in_charset = strtoupper($in_charset);
        //$out_charset = strtoupper($out_charset);
        if ($in_charset == $out_charset) {
            return $str;
        }
        if (is_array($str)) {
            foreach ($str as $k => $v) {
                $str[$k] = static::iconvs($v, $in_charset, $out_charset);
            }
        }else {
            $str = mb_convert_encoding($str, $out_charset, $in_charset);
            //$str = iconv($in_charset, $out_charset.'//IGNORE', $str);
        }
        return $str;
    }

    /**
     * @param $timestamp
     * @return string
     */
    public static function dateStr($timestamp) {
        $nowTime = Lev::$app['timestamp'];
        $tod = date('Ymd', $nowTime);
        if ($tod == date('Ymd', $timestamp)) {
            $md = '今天 ';
        }elseif ($tod == date('Ymd', $timestamp - 3600*24)) {
            $md = '明天 ';
        }else {
            $md = date('m月d号 ', $timestamp);
        }
        $md.= Lev::weekDay($timestamp);
        if (date('s', $timestamp) >0) {
            $datestr = $md.date(' H点i.s', $timestamp);
        }else {
            $datestr = $md.date(' H点i分', $timestamp);
        }
        return $datestr;
    }

    public static function asRealTime($timestamp, $default = '') {
        if ($timestamp <1) {
            return $default;
        }
        $_sec = Lev::$app['timestamp'] - $timestamp;
        $sec = abs($_sec);
        if ($sec <2) {
            $tips = '刚刚';
        }elseif ($sec < 60) {
            $tips = $sec.'秒';
        }elseif ($sec < 3600) {
            $tips = round($sec/60).'分';
        }elseif ($sec < 3600 * 24) {
            $tips = round($sec/3600).'时';
        }elseif ($sec < 3600 * 24 * 7) {
            $tips = round($sec/3600/24).'天';
        }elseif ($sec < 3600 * 24 * 31) {
            $tips = round($sec/3600/24/7).'周';
        }elseif ($sec < 3600 * 24 * 365) {
            $tips = round($sec/3600/24/31).'月';
        }else {
            $tips = round($sec/3600/24/365).'年';
        }
        return $tips.($_sec >1 ? '前' : ($_sec <0 ? '后' : ''));
    }

    public static function setCnzzJs($key, $script) {
        Lev::$app['CnzzJs'][$key] = $script;
    }

    /**
     * 递归获取数组指定列的非数组值
     * eg:print_r(Lev::getArrayColumn(['id'=>['id'=>1, 'kk'=>['id'=>'cc']], 'key'=>['id'=>[3]], 'kk'=>['id'=>'kk']], ['id']))
     * 结果：['id'=>1,'id'=>'cc','id'=>'kk',];
     * @param array $array
     * @param array $columnNames
     * @param string $intval
     * @param bool $unique
     * @return array
     */
    public static function getArrayColumn(array $array, array $columnNames, $intval = 'strip', $unique = true) {
        $result = [];
        $keys = array_flip($columnNames);
        if ($array) foreach ($array as $k => $v) {
            !is_array($v) && isset($keys[$k]) && ($result[] = $intval ? ($intval === 'strip' ? Lev::stripTags($v) : intval($v)) : $v);
            is_array($v) && ($_res = static::getArrayColumn($v, $columnNames)) && $result = array_merge($result, $_res);
        }
        if ($result) {
            $unique && $result = array_unique($result);
        }
        return $result;
    }

    /**
     * @param array $array
     * @return array
     */
    public static function idInsql($array) {
        $arr = [];
        foreach ($array as $id) {
            ($id = floatval($id)) && $arr[$id.''] = $id;
        }
        return $arr;
    }

    /**
     * 数组多重排序
     * @param $array
     * @param $key
     * @param int $direction
     * @param int $sortFlag
     */
    public static function arraySorts(&$array, $key, $direction = SORT_ASC, $sortFlag = SORT_REGULAR)
    {
        $keys = is_array($key) ? $key : [$key];
        if (empty($keys) || empty($array)) {
            return;
        }
        $n = count($keys);
        if (is_scalar($direction)) {
            $direction = array_fill(0, $n, $direction);
        } elseif (count($direction) !== $n) {
            throw new \Exception('The length of $direction parameter must be the same as that of $keys.');
        }
        if (is_scalar($sortFlag)) {
            $sortFlag = array_fill(0, $n, $sortFlag);
        } elseif (count($sortFlag) !== $n) {
            throw new \Exception('The length of $sortFlag parameter must be the same as that of $keys.');
        }
        $args = [];
        foreach ($keys as $i => $k) {
            $flag = $sortFlag[$i];
            $args[] = static::getArrayColumn($array, [$k], '', false);
            $args[] = $direction[$i];
            $args[] = $flag;
        }

        // This fix is used for cases when main sorting specified by columns has equal values
        // Without it it will lead to Fatal Error: Nesting level too deep - recursive dependency?
        $args[] = range(1, count($array));
        $args[] = SORT_ASC;
        $args[] = SORT_NUMERIC;

        $args[] = &$array;
        call_user_func_array('array_multisort', $args);
    }

    /**
     * Safely casts a float to string independent of the current locale.
     *
     * The decimal separator will always be `.`.
     * @param float|int $number a floating point number or integer.
     * @return string the string representation of the number.
     */
    public static function floatToString($number)
    {
        // . and , are the only decimal separators known in ICU data,
        // so its safe to call str_replace here
        return str_replace(',', '.', (string) $number);
    }

    /**
     * @param $message
     * @param string|array $tourl //['name'=>url]
     * @param string $name
     * @param string $referer
     * @param int $timeout
     */
    public static function showMessage($message, $tourl = '', $name = '', $referer = '', $timeout = 0, $title = '') {
        include static::$aliases['@layouts'].'/common/message.php';
//        Viewv::render(static::$aliases['@layouts'].'/common/message.php', [
//            'message' => $message,
//            'tourl'   => $tourl,
//            'name'    => $name,
//            'referer' => $referer,
//            'timeout' => $timeout,
//            'title'   => $title,
//        ]);
        exit;
    }
    public static function showMessages($message, $status = -1, $tourl = '', $name = '', $referer = '', $timeout = 0, $title = '') {
        if (Lev::isAjax()) {
            !is_array($message) && $message = Lev::responseMsg($status, $message);
            ob_end_clean();
            echo json_encode($message);
        } else {
            if (is_array($message)) {
                $msg = !empty($message['message']) ? '' : print_r($message, true);
                !$tourl && extract($message);
                $message = is_array($message) ? $msg : $message.$msg;
            }
            static::showMessage($message, $tourl, $name, $referer, $timeout, $title);
        }
        exit;
    }

    public static function tips($msg) {
        include static::$aliases['@layouts'].'/common/tips.php';
    }

    public static function toolbar($toolbarNavs = []) {
        include Lev::$aliases['@layouts'] . '/common/toolbar.php';
    }

    public static function navbar($navbarNavs = [], $inner = false) {
        include Lev::$aliases['@layouts'] . '/common/navbar'.($inner ? '_inner' : '').'.php';
    }

    public static function footer($footerNavs = [], $ad = true) {
        include Lev::$aliases['@layouts'] . '/common/footer.php';
    }

    public static function toolbarAdmin($saveIcon = 0, $trashIcon = 0, $btns = []) {
        include Lev::$aliases['@layouts'] . '/common/toolbar_admin.php';
    }

    public static function navbarAdmin($addurl = '', $srhtitle = '', $srhkey = '', $subnav = 0, $tips = '', $saveIcon = 0, $trashIcon = 0) {
        include Lev::$aliases['@layouts'] . '/common/navbar_admin.php';
    }

}