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
use lev\base\Controllerv;
use lev\base\Modelv;
use lev\base\Requestv;
use lev\helpers\cacheFileHelpers;
use lev\helpers\ModulesHelper;
use lev\helpers\ScoreHelper;
use lev\helpers\UrlHelper;
use lev\helpers\UserHelper;
use lev\helpers\UserLoginModelHelper;
use lev\widgets\login\loginWidget;
use modules\levmb\table\authuserHelper;

class LoginController extends Controllerv {

    public static function actionEditPassword() {
        if (Lev::$app['uid'] <1) {
            Lev::showMessages(Lev::responseMsg(-5, ''));
        }

        $pwd = Lev::stripTags(Lev::GPv('pwd'));
        $newpwd = Lev::stripTags(Lev::GPv('newpwd'));

        if (!$newpwd) {
            Lev::showMessages(Lev::responseMsg(-44, '抱歉，新密码不能为空'));
        }

        if (ModulesHelper::isInstallModule('levmb')) {
            if (!UserHelper::checkEditPassword(Lev::$app['uid']) && authuserHelper::findOne(['uid'=>Lev::$app['uid']])) {
                $igoneCkpwd = true;
            }
        }

        if (!isset($igoneCkpwd) && $pwdMsg = UserHelper::checkPasswordError(Lev::$app['uid'], $pwd)) {
            Lev::showMessages($pwdMsg);
        }

        $msg = UserHelper::setPassword(Lev::$app['uid'], $newpwd);
        if (isset($msg['succeed'])) {
            !UserHelper::checkEditPassword(Lev::$app['uid']) && UserHelper::updateSettingsEditPassword(Lev::$app['uid']);
        }
        UserHelper::doLogout();
        $msg['tourl'] = UrlHelper::my(0);
        Lev::showMessages($msg);
    }

    /**
     * 修改用户名
     */
    public static function actionEditUsername() {
        if (Lev::$app['uid'] <1) {
            Lev::showMessages(Lev::responseMsg(-5, ''));
        }

        if (Lev::stget('openEditUsername', 'levs')) {
            Lev::showMessages(Lev::responseMsg(-4, '禁止修改'));
        }

        $pwd = Lev::stripTags(Lev::GPv('pwd'));
        $username = urldecode(Lev::GPv('username'));

        if ($errMsg = UserLoginModelHelper::isNameExist($username)) {
            Lev::showMessages( $errMsg );
        }
        if (empty(Lev::$app['LevAPP']) && $errMsg = UserHelper::isNameExist($username)) {
            $uinfo = UserHelper::userInfo(Lev::$app['uid']);
            $uinfo['username'] !== $username &&
            Lev::showMessages( $errMsg );
        }

        if ($pwdMsg = UserHelper::checkPasswordError(Lev::$app['uid'], $pwd)) {
            Lev::showMessages($pwdMsg);
        }

        if (UserHelper::checkEditUsername(Lev::$app['uid']) && $price = loginWidget::editUsernamePrice()) {
            if ($price[0] && $acMsg = ScoreHelper::acscoreUses(-$price[0], '修改登陆用户名', $price[1])) {
                isset($acMsg['error']) && Lev::showMessages($acMsg);
            }
        }

        if ($res = UserLoginModelHelper::updateUsername(Lev::$app['uid'], $username)) {
            UserHelper::updateSettingsEditUsernamePrice(Lev::$app['uid']);
            UserLoginModelHelper::setLoginStatus(Lev::$app['uid']);
            Lev::showMessages(Lev::responseMsg(1, '操作成功', [$res]));
        }else {
            UserHelper::updateSettingsEditUsernamePrice(Lev::$app['uid'], 0);
            Lev::responseMsg(Lev::responseMsg(-1, '抱歉，修改失败了', [$res]));
        }
    }

    /**
     * 用户协议
     */
    public function actionUseragreement() {
        loginWidget::useragreement();
    }

    /**
     * 隐私政策
     */
    public function actionPrivacypolicy() {
        loginWidget::privacypolicy();
    }

    /**
     * 法律声明
     */
    public function actionLaw() {
        loginWidget::law();
    }

    /**
     * 免责声明
     */
    public function actionDisc() {
        loginWidget::disc();
    }

    public function actionMy() {
        loginWidget::my();
    }

    public static function actionExit() {
        UserHelper::doLogout();
        echo Lev::jsonv(Lev::responseMsg(1, '退出成功', ['loginReferer'=>loginWidget::getLoginReferer()]));
        //cacheFileHelpers::setc('loginlog/ddd', $ref.print_r($_SERVER, true), FILE_APPEND);
    }

    public function actionLoadScreen() {
        if (Lev::checkHideT()) {
            $param = [
                'loginUrl'    => Lev::$aliases['@siteurl'] . '/member.php?mod=logging&action=login',
                'registerUrl' => Lev::$aliases['@siteurl'] . '/member.php?mod=logging&action=login',
                'htms'        => '',
            ];
            echo Lev::jsonv(Lev::responseMsg(1, '', $param));
            exit;
        }

        if (Lev::$app['uid']) {
            $param = [
                'loginUrl' => UrlHelper::my(),
                'registerUrl' => UrlHelper::my(),
                'htms' => '',
            ];
        }else {
            $param = [
                'loginUrl'    => loginWidget::getDefaultLoginUrl(),
                'registerUrl' => loginWidget::openRegister() ? '' : (($url = loginWidget::registerUrl()) ? $url[0] : ''),
                'htms' => loginWidget::loadScreen(false),
            ];
        }

        echo Lev::jsonv(Lev::responseMsg(1, '', $param));
    }

    /**
     *
     */
    public static function actionCheckTempToken() {
        echo json_encode(Adminv::getTemporaryAccesstoken(Lev::stripTags(Lev::GPv('tempToken')))
            ? Lev::responseMsg()
            : Lev::responseMsg(-1, '失败'));
    }

    /**
     * Renders the index view for the module
     */
    public function actionIndex()
    {
        $loginReferer = 0;
        if (!loginWidget::openLogin()) {
            $res = Modelv::errorMsg('username', '登陆功能已关闭', -490);
        }else {
            $username = Lev::stripTags(Lev::POSTv('username'));
            $password = trim(Lev::POSTv('password'));
            $questionid = intval(Lev::POSTv('questionid'));
            $answer = Lev::stripTags(Lev::POSTv('answer'));
            $autologin = intval(Lev::POSTv('autologin'));
            if (!$username) {
                $res = Modelv::errorMsg('username', '用户名不能为空', -497);
            } elseif (!$password) {
                $res = Modelv::errorMsg('password', '密码不能为空', -498);
            } else {
                $loginReferer = loginWidget::getLoginReferer();
                if (empty(Lev::$app['LevAPP'])) {
                    $Levuser = UserLoginModelHelper::findOne(['username'=>$username]);
                    if (!empty($Levuser) && $username = UserHelper::userInfo($Levuser['id'])) {
                        $username = $username['username'];
                    }
                }
                $res = UserHelper::doLogin($username, $password, $autologin, $questionid, $answer);

            }
        }
        $res['loginReferer'] = $loginReferer;
        echo Lev::jsonv($res);
    }

    public function actionRegister() {

        $loginReferer = 0;
        if (!loginWidget::openRegister()) {
            $res = Modelv::errorMsg('username', '注册功能已关闭', -490);
        }else {
            $username = trim(Lev::POSTv('username'));
            $password = trim(Lev::POSTv('password'));
            $password2 = trim(Lev::POSTv('password2'));
            $questionid = intval(Lev::POSTv('questionid'));
            $answer = Lev::stripTags(Lev::POSTv('answer'));
            $email = Lev::stripTags(Lev::POSTv('email'));
            if (!$username) {
                $res = Modelv::errorMsg('username', '用户名不能为空', -497);
            } elseif (!$password) {
                $res = Modelv::errorMsg('password', '密码不能为空', -498);
            } elseif (!$password2) {
                $res = Modelv::errorMsg('password2', '密码不能为空', -498);
            } elseif ($password != $password2) {
                $res = Modelv::errorMsg('password2', '两次密码不一致', -498);
            } elseif ($password != addslashes($password)) {
                $res = Modelv::errorMsg('password', '密码不合法', -466);
            } elseif ($username != Lev::stripTags($username)) {
                $res = Modelv::errorMsg('username', '用户名不合法', -467);
            } else {
                $loginReferer = loginWidget::getLoginReferer();
                $res = UserHelper::doRegister($username, $password, $email, $questionid, $answer);
                if (empty(Lev::$app['LevAPP']) && isset($res['uid']) && $res['uid'] >0) {
                    $res['Lev'] = UserLoginModelHelper::registerUid($res['uid'], $username, $password);
                }
            }
        }
        $res['loginReferer'] = $loginReferer;
        echo Lev::jsonv($res);

    }

}