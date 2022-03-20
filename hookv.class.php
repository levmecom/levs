<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-06-06 21:07
 *
 * 项目：rm  -  $  - hook_levs.php
 *
 * 作者：liwei 
 */

!defined('IN_DISCUZ') && exit('Access Denied');

class plugin_levs extends BasePluginLevs
{

    public function __construct()
    {
        static::forceAPP();
        static::checkCloseLogin();
    }

    public static function common() {}//至少使用一个勾子方法，否则不调用勾子
    public static function global_footer() {
        if (static::checkHideT()) return '';

        $htm = '<div id="levs-hook-main" style="display:none !important;"></div>';
        if (static::qrcodeSrc()) {
            $src = is_file($file = DISCUZ_ROOT . 'data/levruntime/levs_global_footer.js')
                ? 'data/levruntime/levs_global_footer.js?'.filemtime($file)
                : 'plugin.php?id=levs&r=hook';
            $htm .= '<script src="'.$src.'"></script>';
        }
        if (is_file(__DIR__ . '/modules/olympic/runtime/hook.js')) {
            $htm.= '<script src="source/plugin/levs/modules/olympic/runtime/hook.js"></script>';
        }
        if (static::globalAdBtn() && is_file(__DIR__ . '/markets.inc.php')) {
            $lang = lang('plugin/levs');
            $htm .= '<a target="_blank" _bk="1" class="wp" style="font-size: 12px;display: flex;justify-content: center;background: rgba(0,0,0,0.1);margin: auto;" href="plugin.php?id=levs:markets">'.$lang['marketShowBtn'].'</a>';
        }
        return $htm;
    }

    public static function forceAPP() {
        if ($url = static::stget('forceAPP', 'levs')) {
            if ((empty($_GET['id']) || strpos($_GET['id'], 'lev') !== 0) && !checkmobile()) {
                header('location:plugin.php?id=levs&r=default/qrcode', true, 302);
                exit();
            }
        }
    }

    public static function checkCloseLogin() {
        self::isLogin() &&
        static::stget('closeDiscuzLogin', 'levs') &&
        self::redirect();
    }

    public static function isLogin() {
        if (CURSCRIPT == 'member' && trim($_GET['mod']) == 'logging' && trim($_GET['action']) == 'login') {
            return true;
        }
        return false;
    }

    public static function redirect() {
        global $_G;
        $url = 'plugin.php?id=levs';
        $doRedirect = !$_G['setting']['bbclosed'] && $_GET['ajaxtarget'] != 'messagelogin';
        if (empty($_GET['inajax'])) {
            $doRedirect && header('location:'.$url);
            echo 'Login closed';
        }else {
            $script = $doRedirect ? '<script>window.location="'.$url.'"</script>' : '';

            ob_end_clean();
            header("Expires: -1");
            header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
            header("Pragma: no-cache");
            header("Content-type: text/xml; charset=".CHARSET);
            echo '<?xml version="1.0" encoding="'.CHARSET.'"?><root><![CDATA[Login closed'.$script.']]></root>';
        }
        exit();
    }

    public static function qrcodeSrc() {
        return ($res = static::stget('qrcodeSrc', 'levs')) && !$res['status'];
    }

}
class plugin_levs_forum extends plugin_levs {}
class plugin_levs_member extends plugin_levs {}
class BasePluginLevs {

    public static $app = [
        'iden' => 'levs',
        'settings' => [],
    ];

    public static function globalAdBtn() {
        return !static::stget('globalAdBtn', 'levs');
    }

    public static function checkHideT()
    {
        return static::stget('SiteName', 'levs') == '&nbsp;';
    }

    /**
     * @param $key
     * @param bool $iden
     * @return string|array
     */
    public static function stget($key, $iden = false) {
        $iden === false && $iden = static::$app['iden'];
        !isset(static::$app['settings'][$iden]) && static::$app['settings'][$iden] = static::loadFileSettings($iden);
        return isset(static::$app['settings'][$iden][$key]) ? static::$app['settings'][$iden][$key] : '';
    }
    public static function loadFileSettings($iden = 'settings') {
        defined('INLEV') or define('INLEV', 1);
        return is_file($file = DISCUZ_ROOT . 'data/levruntime/settings/'.$iden.'.php') ? include $file : [];
    }

}