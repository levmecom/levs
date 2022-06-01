<?php
/* 
 * Copyright (c) 2018-2021  * 
 * 创建时间：2021-04-24 05:06
 *
 * 项目：upload  -  $  - BaseController.php
 *
 * 作者：liwei 
 */

namespace lev\base;

!defined('INLEV') && exit('Access Denied LEV');

use Lev;
use modules\levs\modules\ipban\ipRecordCacheHelper;

class Controllerv
{

    public static $route = '';
    public static $pageNamePre = 'pag-';
    public static $pageName = 'pag-';

    public static $defaultController = 'default';
    public static $defaultAction = 'index';

    public static function toAction() {
        defined('ROUTE_ERROR_SHOW_MESSAGE') && Lev::showMessages(ROUTE_ERROR_SHOW_MESSAGE ?: '错误路由常量被定义：ROUTE_ERROR_SHOW_MESSAGE', 40404);

        static::csrfValidation(0);

        ($r = urldecode(Lev::GPv('r'))) && !preg_match('/^[a-zA-Z\/][a-zA-Z0-9\/_-]+$/', $r)
        && Lev::showMessage('抱歉，路由不合法：'.$r.' 只允许使用【字母、数字、-、/、_】且不能以【数字、-、_】开头');

        Lev::$app['notices'] .= Lev::opCookies('_notices', null, -1, false);

        list($_c, $_a) = static::getPageRoute($r);

        $c = Lev::ucfirstv($_c);
        $a = Lev::ucfirstv($_a);

        static::$route = $_c . '/' . $_a;
        static::$pageName = static::getPageName(Lev::$app['iden'], $_c, $_a);

        Lev::classExists('modules\levs\modules\ipban\ipRecordCacheHelper') && ipRecordCacheHelper::init();

        $method = 'action'.$a;

        $mudInfo = Modulesv::getModuleFileInfo(Lev::$app['iden']);
        if (empty($mudInfo) || $mudInfo['status']) {
            $msg = '抱歉，【'.$mudInfo['name'].'】正在维护';
            !Lev::$app['isAdmin'] && Lev::showMessage($msg);
            Lev::setNotices($msg.'！仅限管理员访问');
        }

        $mudInfo['classdir'] && //环境检测不匹配 非管理员 直接Exit
        Lev::actionObjectMethodIden($mudInfo['classdir'], 'modules\\'.$mudInfo['classdir'].'\helpers\EnvHelper', [], 'check');

        $controller = $c.'Controller';
        $className = static::controllerMaps($controller) ?:
            'modules\\'.Modulesv::getIdenNs(Lev::$app['iden'], $mudInfo['classdir']).'\controllers\\'.$controller;

        Lev::$app['SiteName']    = Lev::stgetv('SiteName') ?: $mudInfo['name'];
        Lev::$app['metakeyword'] = $mudInfo['name'].','.Lev::stgetv('metakeyword').','.$mudInfo['identifier'];
        Lev::$app['metadesc']    = $mudInfo['name'].$mudInfo['descs'].Lev::stgetv('metadesc');

        Lev::actionObjectMethod($className, [], $method, 0) === false &&
        Lev::showMessage('抱歉，页面不存在：'.Lev::$app['iden'].'/'.$c.'/'.$a, '', '', '', 0, '404');

        static::forceAPP();
    }

    public static function forceAPP() {
        if (in_array('__allLev', static::exceptsLev())) return;

        $iden = Lev::GPv('id');
        $iden && strpos($iden, ':') !== false && $iden = explode(':', $iden)[1];
        if (!in_array($iden, static::exceptsLev()) && ($url = Lev::stget('forceAPP', 'levs'))) {
            if (!defined('INADMINLEV') && strpos($iden, 'levs') === false && !static::excepts() && !Lev::ckmobile()) {
                static::redirect(Lev::toReRoute(['default/qrcode', 'id'=>'levs']));
            }
        }
    }

    /**
     * rewrite 标识
     * @param null $iden
     * @param null $_c
     * @param null $_a
     * @return string
     */
    public static function getPageName($iden = null, $_c = null, $_a = null) {
        return static::$pageNamePre . $iden . '-' . $_c . '-' . $_a;
    }

    /**
     * @param $route
     * @return array
     */
    public static function getPageRoute($route) {
        $arr = is_array($route) ? $route : explode("/", $route);
        $_c = !empty($arr[0]) ? $arr[0] : static::$defaultController;
        $_a = !empty($arr[1]) ? $arr[1] : static::$defaultAction;
        return [$_c, $_a];
    }

    public static function exceptsLev() {
        return Lev::stget('exceptsLev', 'levs') ?: [];
    }
    public static function excepts() {
        return in_array(static::$route, [
            'wxlogin/msg',
        ]) || strpos(static::$route, 'login/') !== false;
    }

    /**
     * 全局通用的控制器，不能被替代
     * @param string $name
     * @return bool|mixed
     */
    public static function controllerMaps($name = '') {
        $maps = [
            'LoginController' => 'lev\controllers\LoginController',
            'SupermanController' => 'lev\controllers\SupermanController',
            'UploadController' => 'lev\controllers\UploadController',
        ];
        return $name && isset($maps[$name]) ? $maps[$name] : false;
    }

    public static function redirect($url) {
        header('Location:'.$url, 1, 302);
        exit;
    }

    //post数据强制验证
    public static function csrfValidation($force = true) {
        Adminv::apiCheck();
        !defined('ISAPI') && ($force || !empty($_POST)) && !Lev::csrfValidation() && exit('抱歉，您提交的数据无法被验证或是数据超出最大限制');
    }
}