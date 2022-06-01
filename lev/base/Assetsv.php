<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-04 20:10
 *
 * 项目：upload  -  $  - Assetsv.php
 *
 * 作者：liwei 
 */

namespace lev\base;

use Lev;
use lev\helpers\cacheFileHelpers;
use lev\helpers\ModulesHelper;

!defined('INLEV') && exit('Access Denied LEV');

class Assetsv
{

    public static $fkv = 1;
    public static $registerJsFiles = [];
    public static $registerCssFiles = [];
    public static $loaded = [];

    public static $cssSrc = [];
    public static $jsSrc  = [];

    public static $mudwebassets = '/data/assets/';

    public static function getAppassets($iden = '') {
        !$iden && $iden = Lev::$app['iden'];
        $idenDir = Modulesv::getIdenDir($iden);
        return !empty(Lev::$app['isDiscuz']) ?
            Lev::$aliases['@web'] . '/source/plugin/' . $idenDir . '/assets' :
            Lev::$aliases['@web'] . static::$mudwebassets . $iden;
    }
    public static function getAppwebassets() {
        return Lev::$aliases['@web'] . Lev::$aliases['@appweb'] . '/assets';
    }

    public static function getAppAssetsRoot($iden) {
         if (is_dir($dir = Lev::$aliases['@modules'] . '/' . ModulesHelper::getIdenDir($iden) . '/assets')) {
             return $dir;
         }elseif (is_dir($dir = Lev::$aliases['@webroot'] . static::$mudwebassets . $iden)) {
             return $dir;
         }
         return '';
    }

    /**
     * @param $iden
     * @param bool $bak
     * @param bool $classdir
     * @return array
     */
    public static function moveMudAssets($iden, $bak = false, $classdir = false) {
        if (empty(Lev::$app['isDiscuz'])) {
            $idenDir =  Modulesv::getIdenDir($iden, $classdir);
            $mudAssetsDir = Lev::$aliases['@modules'] . '/'. $idenDir . '/assets';
            if (is_dir($mudAssetsDir) && $files = glob($mudAssetsDir . '/*')) {
                $webAssetsDir = Lev::$aliases['@webroot'] . static::$mudwebassets . $iden;
                cacheFileHelpers::mkdirv(dirname($webAssetsDir));
                if (Lev::isDeveloper($iden, $classdir)) {
                    if (is_dir($webAssetsDir)) {
                        return Lev::responseMsg(-133, '符号连接名已经存在');
                    }
                    return symlink($mudAssetsDir, $webAssetsDir) ? Lev::responseMsg(3) : Lev::responseMsg(-13, '符号连接建立失败');
                } else {
                    static::removeSymlink($webAssetsDir);
                    if (is_dir($webAssetsDir)) {
                        if ($bak) {
                            cacheFileHelpers::mkdirv($dir = $webAssetsDir . '-bak/');
                            rename($webAssetsDir, $dir . microtime(true));
                        }else {
                            cacheFileHelpers::rmdirv($webAssetsDir);
                        }
                    }
                    return rename($mudAssetsDir, $webAssetsDir) ? Lev::responseMsg(1) : Lev::responseMsg(-11, '移动失败');
                }
            }
            return Lev::responseMsg(-14, '无assets目录');
        }
        return Lev::responseMsg(2, 'DZ无操作');
    }
    public static function removeSymlink($path) {
        if (PHP_SHLIB_SUFFIX === 'dll') {//windows
            return @rmdir($path);
        }
        return @unlink($path);
    }

    public static function getAssetsFileRoot($iden, $fileName = null, $type = 'css') {
        $fileName === null && $fileName = Controllerv::$route;
        return Lev::$aliases['@runtime'] . '/assets_file/' . ModulesHelper::getIdenDir($iden) . '/'.$fileName.'.'.$type.'.php';
    }
    public static function getAssetsJsFile($iden, $fileName = null) {
        $css = '';
        if (is_file($assetsFile = static::getAssetsFileRoot($iden, $fileName, 'js'))) {
            $css = include $assetsFile;
        }
        return $css;
    }
    public static function getAssetsCssFile($iden, $fileName = null) {
        $js = '';
        if (is_file($assetsFile = static::getAssetsFileRoot($iden, $fileName, 'css'))) {
            $js = include $assetsFile;
        }
        return $js;
    }

    public static function writeAssetsToFile($iden, $fileName = null) {
        $access = '!defined(\'INLEV\') && exit(\'Access Denied LEV\');';
        $webroot = Lev::$aliases['@webroot'];
        $web = Lev::$aliases['@web'];
        $webLen = strlen($web);
        if (static::$cssSrc) {
            $css = '';
            foreach (static::$cssSrc as $v) {
                $v = Lev::getAlias($v);
                $v = explode('?', $webroot . substr($v, $webLen))[0];
                $css.= file_get_contents($v)."\r\n";
            }
            $css = '<?php '.$access.' return '.var_export($css, true).';';
            $assetsFile = static::getAssetsFileRoot($iden, $fileName, 'css');
            cacheFileHelpers::mkdirv(dirname($assetsFile));
            file_put_contents($assetsFile, $css, LOCK_EX);
        }
        if (static::$jsSrc) {
            $Js = '';
            foreach (static::$jsSrc as $v) {
                $v = Lev::getAlias($v);
                $v = explode('?', $webroot . substr($v, $webLen))[0];
                $Js.= file_get_contents($v)."\r\n";
            }
            $Js = '<?php '.$access.' return '.var_export($Js, true).';';
            $assetsFile = static::getAssetsFileRoot($iden, $fileName, 'js');
            cacheFileHelpers::mkdirv(dirname($assetsFile));
            file_put_contents($assetsFile, $Js, LOCK_EX);
        }
    }

    public static function loadCssFkv1() {
        static::registerFk7();
        return static::loadCss();
    }

    public static function loadCss() {
        return implode('', array_map(function ($val) {
            return !isset(static::$loaded[$k = md5($val)]) && (static::$loaded[$k] = 1) ? $val : '';
        }, static::$registerCssFiles));
    }

    public static function loadJs() {
        return implode('', array_map(function ($val) {
            return !isset(static::$loaded[$k = md5($val)]) && (static::$loaded[$k] = 1) ? $val : '';
        }, static::$registerJsFiles));
    }

    public static function highlight($load = false) {
        return static::registerCssFile('@assets/statics/editor/highlight/monokai-sublime.css?'.$load, $load)
            .static::registerJsFile('@assets/statics/editor/highlight/highlight.min.js', $load);
    }

    public static function cookieJs($load = false) {
        return static::registerJsFile('@assets/statics/common/jquery.cookie.js', $load);
    }

    public static function ajaxFormJs($load = false) {
        return static::registerJsFile('@assets/statics/common/jquery.form.min.js', $load);
    }

    public static function flipclocks($load = false) {
        return static::registerCssFile('@assets/statics/common/flipclock/flipclock.css', $load)
            .static::registerJsFile('@assets/statics/common/flipclock/flipclock.min.js', $load);
    }

    public static function fontsCss($load = false) {
        return static::registerCssFile('@assets/statics/common/fonts/fonts.css', $load);
    }

    public static function highchartsJs($load = false) {
        return static::registerJsFile('@assets/statics/common/highcharts.js', $load);
    }

    public static function highstockJs($load = false) {
        return static::registerJsFile('@assets/statics/common/highstock.js', $load);
    }

    public static function qrcodeJs($load = false) {
        $src = '@assets/statics/common/qrcode.min.js';
        return $load === 'src' ? Lev::getAlias($src) : static::registerJsFile($src, $load);
    }

    public static function clipboardJs($load = false) {
        $src = '@assets/statics/common/clipboard.min.js';
        return $load === 'src' ? Lev::getAlias($src) : static::registerJsFile($src, $load);
    }

    public static function animateCss($load = false) {
        return static::registerCssFile('@assets/statics/common/animate.min.css', $load);
    }

    public static function Jquery($load = false) {
        return static::registerJsFile('@assets/statics/common/jquery.min.js', $load);
    }

    public static function registerAppMud($iden, $live = '') {
        return static::registerCssFile(static::getAppassets($iden).'/statics/css.css?'.$live).
        static::registerJsFile(static::getAppassets($iden).'/statics/js.js?'.$live);
    }

    public static function registerApp($live = '', $iden = '') {
        static::registerFk7();
        static::registerCssFile(static::getAppassets($iden).'/statics/css.css?'.$live);
        static::registerJsFile(static::getAppassets($iden).'/statics/js.js?'.$live);
    }

    public static function registerSuperman($load = false) {
        return static::ajaxFormJs().
        static::registerCssFile('@assets/statics/superman/css.css?'.Lev::$app['version'], $load).
        static::registerJsFile('@assets/statics/superman/js.js?'.Lev::$app['version'], $load);
    }

    public static function registerFk7() {
        static $loaded; if (isset($loaded)) return; $loaded = 1;

        static::registerCssFile('@assets/statics/fk7/v1/framework7.ios.colors.min.css');
        static::registerCssFile('@assets/statics/fk7/v1/framework7.ios.min.css');
        static::registerCssFile('@assets/statics/fk7/fk7.css?'.Lev::$app['version']);

        static::registerJsFile('@assets/statics/fk7/v1/framework7.min.js');
        static::registerJsFile('@assets/statics/common/iconfont.js?'.Lev::$app['version']);
        static::registerJsFile('@assets/statics/fk7/fk7.init.js?'.Lev::$app['version']);
    }

    public static function registerJsFile($src, $load = false) {
        static::$jsSrc[$src] = $src;

        $src = Lev::getAlias($src);
        $str = '<script type="text/javascript" src="'.$src.'"></script>';
        $load && !isset(static::$registerJsFiles[$src]) && static::$loaded[md5($str)] = 1;
        return (!isset(static::$registerJsFiles[$src]) && static::$registerJsFiles[$src] = $str) ? $str : '';
    }

    public static function registerCssFile($src, $load = false) {
        static::$cssSrc[$src] = $src;

        $src = Lev::getAlias($src);
        $str = '<link rel="stylesheet" type="text/css" href="'.$src.'" />';
        $load && !isset(static::$registerCssFiles[$src]) && static::$loaded[md5($str)] = 1;
        return (!isset(static::$registerCssFiles[$src]) && static::$registerCssFiles[$src] = $str) ? $str : '';
    }

}