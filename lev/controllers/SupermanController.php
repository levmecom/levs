<?php
/* 
 * Copyright (c) 2018-2021  * 
 * 创建时间：2021-04-24 06:14
 *
 * 项目：upload  -  $  - levssqController.php
 *
 * 作者：liwei 
 */


namespace lev\controllers;

!defined('INLEV') && exit('Access Denied LEV');

use Lev;
use lev\base\Adminv;
use lev\base\Assetsv;
use lev\base\Controllerv;
use lev\base\Modulesv;
use lev\base\Viewv;
use lev\helpers\cacheFileHelpers;
use lev\helpers\ModulesHelper;
use lev\helpers\SettingsHelper;
use lev\helpers\subTableHelper;
use lev\helpers\UrlHelper;
use lev\widgets\adminModulesNav\adminModulesNav;
use lev\widgets\inputs\inputsWidget;
use lev\widgets\install\installWidget;
use lev\widgets\login\loginWidget;
use modules\levs\helpers\siteHelper;
use modules\levs\modules\ftp\helpers\ftpZipHelper;

Adminv::checkAccess();
Assetsv::registerSuperman();

class SupermanController extends Controllerv {

    public static function actionUpdateQuickNav() {
        $navstr = Lev::base64_decode_url(Lev::GPv('navstr'));
        $icon = Lev::base64_decode_url(Lev::GPv('icon'));
        $title = Lev::stripTags(Lev::GPv('title'));
        if (Lev::GPv('del')) {
            adminModulesNav::updateQuickNav($navstr, null, null);
            Lev::showMessages('删除完成');
        }else if ($navstr && ($icon || $title)){
            adminModulesNav::updateQuickNav($navstr, $icon, $title);
            Lev::showMessages('更新完成');
        }
        Lev::showMessages('收藏链接、图标或标题不能为空');
    }

    /**
     * 组件、插件详情跳转到应用中心
     * Renders the index view for the module
     */
    public function actionIndex()
    {
        $iden = Lev::GETv('iden');
        $iden = $iden ? explode('/', urldecode(Lev::stripTags($iden)))[0] : '';
        if ($iden) {
//            $url = Lev::GETv('cl') ? 'https://addon.dismall.com/?@' . $iden . '.plugin#component'
//                : 'https://addon.dismall.com/?@' . $iden . '.plugin';
            $url = Lev::toRoute([UrlHelper::storeHome(), 'iden'=>$iden, 'r'=>'view']);
        }else {
            //$url = 'https://addon.dismall.com/developer-10158.html';
            $url = UrlHelper::storeHome();
        }
        parent::redirect($url);
    }

    public function actionMyDismallPlugin() {
        //$pm['url'] = 'https://api.dismall.com/';
    }

    /**
     * 更新模块信息、模块设置缓存
     */
    public function actionSetCaches() {
        inputsWidget::setCaches();
        ModulesHelper::setCaches();
        Lev::showMessages('恭喜，更新完成！');
    }

    /**
     * 更新分表缓存
     *
     * @see UrlHelper::updateSubTableCache()
     */
    public static function actionUpdateSubTableCache() {
        Lev::showMessages(subTableHelper::updateSubTablesCache());
    }

    /**
     * 创建分表
     *
     * @see UrlHelper::createSubTable()
     */
    public static function actionCreateSubTable() {
        $tableName = Lev::stripTags(Lev::GPv('table'));
        if (!Lev::GPv('doit')) {
            subTableHelper::createLock($tableName);
            parent::redirect(Lev::toCurrent(['doit'=>1]));
        }
        Lev::showMessages(subTableHelper::createSubTableSchema($tableName));
    }

    /**
     * 删除分表
     *
     * @see
     */
    public static function actionDropSubTable() {
        $tableName = Lev::stripTags(Lev::GPv('table'));
        if (!Lev::GPv('doit')) {
            parent::redirect(Lev::toCurrent(['doit'=>1]));
        }
        Lev::showMessages(subTableHelper::dropSubTable($tableName));
    }

    public static function actionInstallOrUpdateModule() {
        $iden = Lev::stripTags(Lev::GPv('iden'));

        $_GET['checkFile'] = 1;
        $_GET['doit'] = 1;

        ModulesHelper::isInstallModule($iden) ? static::actionUpdateModule() : static::actionInstallModule();
    }

    public static function InstallOrUpdateModule($iden, $classdir, $newMud = []) {
        return ModulesHelper::isInstallModule($iden)
            ? static::UpdateModule($iden, $classdir, $newMud)
            : static::InstallModule($iden, $classdir, $newMud);
    }

    /**
     * 模块安装
     */
    public static function actionInstallModule() {
        $iden = Lev::stripTags(Lev::GPv('iden'));
        $classdir = Lev::stripTags(Lev::GPv('classdir'));

        $mudInfo = ModulesHelper::getModuleInfo($iden);

        $newMud = ModulesHelper::getUpdateMuds($iden);

        Lev::$app['title'] = !empty($mudInfo['name']) ? $mudInfo['name'] : (empty($newMud['name']) ? '新模块安装' : $newMud['name']);

        if (empty($newMud) && !ModulesHelper::checkUpdateFile($iden, $classdir)) {
            Lev::showMessages('抱歉，没有可用安装');
        }

        if (!Lev::GPv('doit')) {
            loginWidget::setLoginReferer(Lev::$app['referer']);
            if (is_array($newMud)) {
                unset($newMud['##mudFiles']);
            }
            $tips = empty($newMud) ? '未检测到新版本！' : '检测到新版本信息：<div style="max-height: 320px;overflow: auto;"><pre style="display: block;font-size: 12px;white-space:pre-wrap;word-break: break-all;transform: scale(0.8);transform-origin: left top;">'.print_r($newMud, true).'</pre></div>';
            Lev::showMessage($tips.'您确定要安装吗？', ['安装'=>Lev::toCurrent(['doit'=>1])], 'submit');
        }
        $msg = static::InstallModule($iden, $classdir, $newMud);
        Lev::showMessages($msg);
    }
    public static function InstallModule($iden, $classdir, $newMud = []) {
        Lev::$app['referer'] = loginWidget::getLoginReferer();

        !empty($newMud['##zipFileSrc']) &&
        Lev::actionObjectMethod('modules\levs\modules\ftp\helpers\ftpZipHelper', [$newMud['##zipFileSrc']], 'unZipModules');

        if ($classdir) {
            if (!ModulesHelper::isInstallModule($classdir)) {
                return Lev::responseMsg(-2020, '抱歉，你需要先安装父模块【'.$classdir.'】才可以安装子模块【'.$iden.'】');
            }
            $file = ModulesHelper::getRouteFile($iden, $classdir);
            if (!is_file($file)) {
                return Lev::responseMsg(-2000, '抱歉，路由文件不存在：' . $file);
            }
            $filename = Lev::getAlias('@modules/' . $classdir . '/' . $iden . '.inc.php');
            $size = file_put_contents($filename, file_get_contents($file));
        }else {
            $size = true;
        }
        if ($size) {
            $ns = '\modules\\'.Modulesv::getIdenNs($iden, $classdir).'\migrations\_install';
            Lev::setModule($iden, $classdir);
            Lev::actionObjectMethod($ns, [], 'actionInstall');
            siteHelper::clearNewStoreMuds($iden);

            $moveMsg = Assetsv::moveMudAssets($iden, false, $classdir);

            $iden == 'ftp' && ftpZipHelper::opCacheClear();
            ModulesHelper::isInstallModule('ftp') && ftpZipHelper::opCache($iden, '');
            cacheFileHelpers::clearc('zipsdir');
            return Lev::responseMsg(1, '恭喜，安装成功！'.$moveMsg['message'], ['extjs'=>installWidget::run($iden)]);
        }
        return Lev::responseMsg(-2001, '抱歉，文件写入内容空');
    }

    /**
     * 更新模块
     */
    public static function actionUpdateModule() {
        $iden = Lev::stripTags(Lev::GPv('iden'));
        $classdir = Lev::stripTags(Lev::GPv('classdir'));

        $mudInfo = ModulesHelper::getModuleInfo($iden) ?: ModulesHelper::getModuleFileInfo($iden);
        if (empty($mudInfo)) {
            Lev::showMessages('抱歉，没有找到更新模块信息');
        }

        $newMud = ModulesHelper::getUpdateMuds($iden);

        Lev::$app['title'] = !empty($mudInfo['name']) ? $mudInfo['name'] : (empty($newMud['name']) ? '新模块安装' : $newMud['name']);

        if (empty($newMud) && !ModulesHelper::checkUpdateFile($iden, $classdir)) {
            Lev::showMessages('抱歉，没有可用更新');
        }

        if (!Lev::GPv('doit')) {
            loginWidget::setLoginReferer(Lev::$app['referer']);
            if (is_array($newMud)) {
                unset($newMud['##mudFiles']);
            }
            $tips = empty($newMud) ? '未检测到新版本！' : '检测到新版本信息：<div style="max-height: 320px;overflow: auto;"><pre style="display: block;font-size: 12px;white-space:pre-wrap;word-break: break-all;transform: scale(0.8);transform-origin: left top;">'.print_r($newMud, true).'</pre></div>';
            Lev::showMessage($tips.'您确定要更新吗？', ['更新'=>Lev::toCurrent(['doit'=>1])], 'submit');
        }

        $msg = static::UpdateModule($iden, $classdir, $newMud);
        Lev::showMessages($msg);
    }
    public static function UpdateModule($iden, $classdir, $newMud = []) {
        Lev::$app['referer'] =  loginWidget::getLoginReferer() ?: UrlHelper::adminModules('levs');

        if (!empty($newMud['##changeFiles']['LevVersion'])) {
            $LevVersion = $newMud['##changeFiles']['LevVersion'];
            $levMudInfo = ModulesHelper::getModuleInfo('levs');
            if ($LevVersion > $levMudInfo['version']) {
                return Lev::responseMsg(-2200, '抱歉，【'.$newMud['name'].'】需要levs模块版本：'.$LevVersion.'及以上支持。<br>levs当前版本是：'.$levMudInfo['version']);
            }
        }

        //ftpZipHelper::unZipModules($newMud['##zipFileSrc']);
        !empty($newMud['##zipFileSrc']) &&
        Lev::actionObjectMethod('modules\levs\modules\ftp\helpers\ftpZipHelper', [$newMud['##zipFileSrc']], 'unZipModules');
        if (!Lev::GPv('checkFile')) {
            parent::redirect(Lev::toCurrent(['checkFile'=>1]));
        }

        if ($classdir) {
            $file = ModulesHelper::getRouteFile($iden, $classdir);
            if (!is_file($file)) {
                return Lev::responseMsg(-2210, '抱歉，路由文件不存在：' . $file);
            }
            $filename = Lev::getAlias('@modules/' . $classdir . '/' . $iden . '.inc.php');
            $size = file_put_contents($filename, file_get_contents($file));
        }else {
            $size = true;
        }
        if ($size) {
            $ns = '\modules\\'.Modulesv::getIdenNs($iden, $classdir).'\migrations\_update';
            Lev::setModule($iden, $classdir);
            Lev::actionObjectMethod($ns, [], 'actionUpdate');
            siteHelper::clearNewStoreMuds($iden);

            $moveMsg = Assetsv::moveMudAssets($iden);

            ModulesHelper::isInstallModule('ftp') && ftpZipHelper::opCache($iden, '');

            installWidget::delDzInstallFile($iden);

            cacheFileHelpers::clearc('zipsdir');
            return Lev::responseMsg(1, '恭喜，更新成功！'.$moveMsg['message']);
        }
        return Lev::responseMsg(-2220, '抱歉，文件写入内容空');
    }

    public static function actionUninstallModule() {
        $iden = Lev::stripTags(Lev::GPv('iden'));
        $classdir = Lev::stripTags(Lev::GPv('classdir'));

        if ($iden == APPVIDEN) {
            $muds = ModulesHelper::findAll(1);
            count($muds) >1 &&
            Lev::showMessages('您正在卸载主模块，需要卸载完成其它模块才可以卸载');
        }
        if (!Lev::GPv('doit')) {
            loginWidget::setLoginReferer(Lev::$app['referer']);
            Lev::showMessage('卸载将删除所有数据及文件，您确定要卸载吗？', ['卸载'=>Lev::toCurrent(['doit'=>1, 'forceDel'=>0]), '卸载并强行删除模块目录文件'=>Lev::toCurrent(['doit'=>1, 'forceDel'=>1])], 'submit');
        }
        $referer = loginWidget::getLoginReferer();

        //$referer = Lev::toReRoute(['superman/modules', 'id'=>$classdir ?: null]);
        $ns = '\modules\\'.Modulesv::getIdenNs($iden, $classdir).'\migrations\_uninstall';
        Lev::setModule($iden, $classdir);
        Lev::actionObjectMethod($ns, [], 'actionUninstall');
        ModulesHelper::delete(['identifier'=>$iden]);
        SettingsHelper::delete(['moduleidentifier'=>$iden]);
        is_file($filename = Lev::getAlias('@modules/' . $classdir . '/' . $iden . '.inc.php')) && @unlink($filename);
        if (Lev::GPv('forceDel')) {
            Modulesv::deleteModuleFile($iden, true, $classdir);
            //Modulesv::deleteModuleDir(ModulesHelper::getIdenDir($iden, $classdir));
        }
        ModulesHelper::isInstallModule('ftp') && ftpZipHelper::opCache($iden, '');
        Lev::showMessage('恭喜，卸载成功！', installWidget::undz($iden), '', $referer);
    }

    public static function actionEnableDzPlugin() {
        $iden = Lev::stripTags(Lev::GPv('iden'));
        if (!$iden) {
            Lev::showMessages('错误，标识符不能为空');
        }

        parent::redirect(installWidget::getDzEnableurl($iden));
    }

    public static function actionModules() {

        if ($adminop = Lev::POSTv('adminop')) {
            if (($tips = ModulesHelper::adminop($adminop)) !== null) {
                echo Lev::jsonv($tips);
                return;
            }
        }

        Lev::$app['title'] = '组件管理';

        $iden = Lev::stripTags(urldecode(Lev::GETv('iden'))) ?: Lev::$app['iden'];
        $lists = ModulesHelper::findAll("identifier='{$iden}' OR classdir='{$iden}'");

        Viewv::render('@layouts/superman/modules', [
            'lists' => $lists,
        ]);
    }

    public static function actionSettings()
    {

        $classify = Lev::stripTags(urldecode(Lev::GETv('classify')));
        $iden = Lev::stripTags(urldecode(Lev::GETv('iden'))) ?: Lev::$app['iden'];

        if (Lev::GPv('dosubmit')) {
            $msg = inputsWidget::saveSettings($iden, $classify);
            if ($msg['status'] >0) {
                $rmsg = Lev::actionObjectMethodSettingsReturn($iden);
                $msg['_rmsg'] = $rmsg;
                $rmsg && isset($rmsg['message']) && $msg['message'] .= $rmsg['message'];
            }
            echo json_encode($msg);
            return;
        }

        $modInfo = ModulesHelper::getModuleInfo($iden);
        $inputs = inputsWidget::getModSettings($iden, $classify, '', ['displayorder ASC']);

        if (empty($inputs)) {
            if ($classify == 'dzset') {
                parent::redirect(installWidget::dzcofigurl($iden));
            }
            Lev::showMessage('该设置分类下无配置项目：'.$classify, [
                '#全部设置#' => Lev::toCurrent(['classify'=>null]),
                '组件管理'   => Lev::toReRoute(['superman/modules', 'id'=>$iden, 'iden'=>$iden]),
            ]);
        }

        $setClassify = ModulesHelper::getClassify($iden, 1);
        $sltOption = '<option value="">#全部设置#</option>';
        foreach ($setClassify as $key => $cname) {
            $ckd = $key == $classify ? 'selected' : '';
            $sltOption .= '<option value="'.$key.'" '.$ckd.'>'.$cname.'</option>';
        }

        $sltOption &&
        $sltOption = '<select class="button-fill setClassify button scale9 color-black" style="line-height: unset !important;" url="'.Lev::toCurrent(['classify'=>null]).'">'
            .$sltOption.'</select>';

        Lev::$app['title'] = $modInfo ? $modInfo['name'].'【'.Lev::arrv($classify, $setClassify, '#全部设置#').'】设置' : '全局设置';

        Viewv::render('@layouts/superman/settings', [
            'classify' => $classify,
            'iden' => $iden,
            'modInfo' => $modInfo,
            'inputs' => $inputs,
            'sltOption' => $sltOption,
        ]);
    }
}