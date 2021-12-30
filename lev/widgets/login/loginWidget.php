<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-06-08 23:56
 *
 * 项目：rm  -  $  - loginWidget.php
 *
 * 作者：liwei 
 */

namespace lev\widgets\login;

use Lev;
use lev\base\Viewv;
use lev\base\Widgetv;
use lev\helpers\ModulesHelper;
use lev\helpers\ScoreHelper;
use lev\helpers\UrlHelper;
use modules\levmb\helpers\levmbSetHelper;

!defined('INLEV') && exit('Access Denied LEV');


class loginWidget extends Widgetv
{

    public static function my() {
        Lev::$app['title'] = '我的';
        $scores = ScoreHelper::myScores();
        Viewv::render(__DIR__ . '/views/my.php', [
            'scores' => $scores,
        ]);
    }

    public static function run($show = true)
    {
        if (Lev::$app['uid'] >0) return '';

        return static::loadScreen($show);
    }

    public static function loadScreen($show = true) {
        $otherLogin = static::getOtherLoginHtm(true, true);

        return //!static::openLogin() ? '' :
            parent::render($show, __DIR__ . '/views/login_screen.php', [
                'otherLoginHtm' => $otherLogin[0],
                'envBtn'        => $otherLogin[1],
                'myphoneOpen'   => $otherLogin[2],
            ]);
    }

    public static function getApk() {
        return ($res = Lev::stget('apk', 'levmb')) && !$res['status'] ? $res : [];
    }

    public static function getDefaultLoginUrl() {
        $loginType = static::loginType();
        if ($loginType == 'app') {
            static::openLogin() && $url = '';
        }elseif ($loginType != 'discuz') {
            $auth = static::otherLogin();
            isset($auth[$loginType]['authUrl']) && $url = $auth[$loginType]['authUrl'];
        }
        if (!isset($url)) {
            $urls = static::loginUrl();
            $url = empty($urls) ? '' : $urls[0];
        }
        return $url;
    }

    public static function setloginType() {
        $auth = static::otherLogin();
        $arr['app'] = '本插件登陆';
        empty(Lev::$app['isDiscuz']) || $arr['discuz'] = 'DZ论坛登陆';
        if ($auth) {
            foreach ($auth as $v) {
                $arr[$v['id']] = $v['name'];
            }
        }
        return $arr;
    }

    public static function loginType() {
        return Lev::stgetv('loginType');
    }

    public static function openRegister() {
        return !Lev::stgetv('openRegister');
    }

    public static function openLogin() {

        return isset(Lev::$app['LevAPP']) ? 2 : !Lev::stgetv('openLogin');
    }

    public static function registerUrl() {
        $url = Lev::stgetv('registerUrl');
        if ($url) {
            $url = explode('##', $url);
            $url[0] = Lev::toRoute([$url[0]]);
            empty($url[1]) && $url[1] = '论坛注册';
        }
        return $url;
    }

    public static function loginUrl() {
        $url = Lev::stgetv('loginUrl');
        if ($url) {
            $url = explode('##', $url);
            $url[0] = Lev::toRoute([$url[0]]);
            empty($url[1]) && $url[1] = '论坛登陆';
        }
        return $url;
    }

    public static function setLoginReferer($url) {
        return Lev::opCookies('loginReferer', $url, 300, false);
    }
    public static function getLoginReferer() {
        return Lev::opCookies('loginReferer', null, -1, false);
    }

    public static function checkLoginType($loginType) {
//        if (!Lev::ckmobile() && in_array($loginType, ['weixinmp'])) {
//            return true;
//        }
        if (ModulesHelper::isInstallModule('levmb')) {
            return levmbSetHelper::checkLoginType($loginType);
        }
        return false;
    }

    public static function getOtherLoginHtm($hideAppLogin = false, $envLogin = false) {
        $other = static::otherLoginFormat();
        $htm = '';
        $myphone = 0;
        $envBtn = [];
        foreach ($other as $v) {
            if (!$v['status'] && !$v['showstatus']) {
                $myphone = $v['loginType'] == 'myphone';
                if ($hideAppLogin && $v['loginType'] == 'myphone') {
                }else {
                    $htm .= '<a class="link openPP" href="' . $v['authUrl'] . '">' . $v['_icon'] . '<span class="tabbar-label date">' . $v['name'] . '</span></a>';
                    if (empty($envBtn) && $envLogin && static::checkLoginType($v['loginType'])) {
                        $envBtn = $v;
                    }
                }
            }
        }
        return $envLogin ? [$htm, $envBtn, $myphone] : $htm;
    }
    public static function otherLogin() {
        return Lev::actionObjectMethodIden('levmb', 'modules\levmb\helpers\levmbSetHelper', [], 'authLogin') ?: [];
    }
    public static function otherLoginFormat() {
        return Lev::actionObjectMethodIden('levmb', 'modules\levmb\helpers\levmbSetHelper', [], 'authLoginFormat') ?: [];
    }

    public static function AppName() {
//        return Lev::stget('AppName', Lev::$app['iden'])
//            ?: (ModulesHelper::getModuleFileInfo(Lev::$app['iden'])['name']
//            ?: (Lev::stget('AppName', APPVIDEN)
//            ?: Lev::$app['SiteName']));
        return Lev::stgetv('AppName') ?: Lev::$app['SiteName'];
    }

    public static function useragreement() {
        Lev::$app['title'] = '用户协议';
        Viewv::render(__DIR__ . '/views/about/useragreement.php', [
            'SiteName' => static::AppName(),
            'homeUrl'  => UrlHelper::home(),
            'hide'     => Lev::GETv('ziframescreen') ? ' hiddenx' : '',
        ]);
    }

    public static function privacypolicy() {
        Lev::$app['title'] = '隐私政策';
        Viewv::render(__DIR__ . '/views/about/privacypolicy.php', [
            'SiteName' => static::AppName(),
            'homeUrl'  => UrlHelper::home(),
            'hide'     => Lev::GETv('ziframescreen') ? ' hiddenx' : '',
            'termsofserviceurl' => UrlHelper::useragreement(),
            'email' => Lev::stgetv('ppemail'),
        ]);
    }

    public static function law() {
        Lev::$app['title'] = '法律声明';
        Viewv::render(__DIR__ . '/views/about/law.php', [
            'SiteName' => static::AppName(),
            'homeUrl'  => UrlHelper::home(),
            'hide'     => Lev::GETv('ziframescreen') ? ' hiddenx' : '',
        ]);
    }

    public static function disc() {
        Lev::$app['title'] = '免责声明';
        Viewv::render(__DIR__ . '/views/about/disc.php', [
            'SiteName' => static::AppName(),
            'homeUrl'  => UrlHelper::home(),
            'hide'     => Lev::GETv('ziframescreen') ? ' hiddenx' : '',
        ]);
    }

    public static function checkLoginMsg($msg = null, $win = 1)
    {
        if (Lev::$app['uid'] <1) {
            $url = UrlHelper::login();
            if ($win == 1) {
                Lev::showMessages(Lev::responseMsg(-5, '抱歉，请先登陆'), 0, $url, 'submit', $url, 2);
            }else {
                $msg === null &&
                $msg = '抱歉，请先登陆！<a class="button button-small button-fill scale9 vera wdmin wd30 inblk" href="'.$url.'" target="_blank">点我登陆</a>';
                Lev::setNotices($msg.'<script>jQuery(function() {openLoginScreen(0, undefined, 1)});</script>');
            }
        }
    }

    public static function editUsernameTips()
    {
        $tip = '首次修改免费';
        if (Lev::getSettings(Lev::$app['myInfo']['settings'], 'editUsername') >0 && $price = static::editUsernamePrice()) {
            if ($price[0]) {
                $tip = '本次修改费用：<b class=red>'.$price[0] . ScoreHelper::scorenamex($price[1]).'</b>';
            }
        }
        return $tip;
    }

    public static function editUsernamePrice() {
        return Lev::stget('editUsernamePrice', 'levs');
    }

}