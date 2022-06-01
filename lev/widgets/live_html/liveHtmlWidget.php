<?php
/**
 * Copyright (c) 2022-2222   All rights reserved.
 *
 * 创建时间：2022-04-15 17:14
 *
 * 项目：levs  -  $  - liveHtmlWidget.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');


namespace lev\widgets\live_html;

use Lev;
use lev\base\Controllerv;
use lev\base\Widgetv;
use lev\helpers\cacheFileHelpers;

class liveHtmlWidget extends Widgetv
{
    public static function dirRule($dir = null) {
        $baseDir = Lev::$app['iden'].'/'.Controllerv::$route;
        $dir && $baseDir .= '/' . $dir;
        return $baseDir;
    }

    /**
     * ajax 动态生成html
     * @param $htmlcontent
     * @param string $param
     * @param string $dir
     * @param int $sec
     */
    public static function createLiveHtml($htmlcontent, $param = '', $dir = null, $sec = 10) {
        if ($htmlcontent) {
            static::createHtml($htmlcontent . static::liveHtmlJS($sec), $param, $dir);
        }
    }

    /**
     * 创建静态的HTML访问路径 延迟3秒
     * @param string $htmlcontent
     * @param string|array $param
     * @param string $dir
     * @param int $sec
     */
    public static function createHtml($htmlcontent, $param = '', $dir = null, $sec = 3) {
        $dir = static::dirRule($dir);

        $rootdir = Lev::$aliases['@htmlroot'].'/'.$dir;
        cacheFileHelpers::mkdirv($rootdir, 0777, true);
        $rootfile = rtrim($rootdir, '/').DIRECTORY_SEPARATOR.static::getFilename($param);
        if (static::createTime($rootfile, $sec)) {
            file_put_contents($rootfile, $htmlcontent, LOCK_EX);
        }
    }

    /**
     * 获取已创建html文件内容
     * @param string $param
     * @param null $dir
     * @param bool $live
     * @param int $sec
     * @return string
     */
    public static function getCreateHtml($param = '', $dir = null, $live = true, $sec = 3) {
        $dir = static::dirRule($dir);

        $rootdir = Lev::$aliases['@htmlroot'].'/'.$dir;
        $rootfile = rtrim($rootdir, '/').DIRECTORY_SEPARATOR.static::getFilename($param);
        if (is_file($rootfile)) {
            if ($_sec = floatval(Lev::GPv('doCreateHtml'))) {
                $_sec >3 && $sec = $_sec;
                if (static::createTime($rootfile, $sec)) {
                    return '';
                }
            }

            $html = file_get_contents($rootfile);
            return $live ? $html : explode('<doCreateHtmlBox', $html)[0];
        }
        return '';
    }

    /**
     * 文件每次更新间隔3秒，到时间可更新html内容 返回true
     * @param $rootfile
     * @param int $sec
     * @return bool
     */
    public static function createTime($rootfile, $sec = 3) {
        return (!is_file($rootfile) || filemtime($rootfile) < Lev::$app['timestamp'] - $sec);
    }

    /**
     * 根据参数创建文件名
     * @param string|array $param
     * @return string
     */
    public static function getFilename($param = '') {
        $extension = '.html';
        $filename = 'index';
        if ($param) {
            if (is_array($param)) {
                $file = [];
                foreach ($param as $k => $v) {
                    $file[] = $k.'-'.$v;
                }
                $filename = implode('-', $file);
            }else {
                $filename = $param;
            }
        }
        return $filename . $extension;
    }

    /**
     * 创建强制更新url
     * @param array $pm
     * @param int $sec
     * @return bool|mixed|string
     */
    public static function toCurrentCreate($sec = 1, $pm = []) {
        unset($_GET['_']);
        $pm += ['doCreateHtml'=>$sec];
        return Lev::toCurrent($pm, false, false);
    }

    /**
     * 嵌入html内的更新js 延迟1 - 10秒后发送请求
     * @param int $sec
     * @return string
     */
    public static function liveHtmlJS($sec = 10) {
        $sec *= 1000;
        $createLiveHtmlUrl = static::toCurrentCreate();
        $timestamp = Lev::$app['timestamp']*1000;
        $md5u = md5($createLiveHtmlUrl);
        return <<<script
<doCreateHtmlBox class="hiddenx">
<script>
    jQuery(function() {
        
        doCreateAjax();
        function doCreateAjax() {
            var createTimestamp = $timestamp;
            var sec = $sec;
            var createLiveHtmlUrl = '$createLiveHtmlUrl';
            var md5u = '$md5u';
            
            var nowTime = new Date().getTime();
            createTimestamp < nowTime - sec && 
            !(typeof unsetDoCreateHtml !== "undefined" && unsetDoCreateHtml) &&
            Levme.setTimeout(function() {
                if (Levme.tempDatas['doCreateAjaxing__'] && Levme.tempDatas['doCreateAjaxing__'] > nowTime - 60*1000) {
                    doCreateAjax();
                    return;
                }else if (Levme.tempDatas['doCreateAjaxing__'+ md5u]) {//页面未刷新只请求一次
                    return;
                }
                Levme.tempDatas['doCreateAjaxing__'] = nowTime;
                jQuery.get(createLiveHtmlUrl, function(data) {
                    Levme.tempDatas['doCreateAjaxing__'] = 0;
                    Levme.tempDatas['doCreateAjaxing__'+ md5u] = createLiveHtmlUrl;
                    if (data && data.message) {
                        Levme.showNotices(data.message);
                    }
                    jQuery('doCreateHtmlBox').remove();
                }, 'json');
            }, 1000+levrandom(1000, sec));
        }
    })
</script>
</doCreateHtmlBox>
script;

    }

}