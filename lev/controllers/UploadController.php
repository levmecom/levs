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
use lev\base\Viewv;
use lev\helpers\ModulesHelper;
use lev\helpers\UploadHelper;
use lev\helpers\UserHelper;
use lev\widgets\uploads\uploadsWidget;

class UploadController extends Controllerv {

    public static function actionSetAvatar() {
        if (Lev::$app['uid'] <1) {
            Lev::showMessages(Lev::responseMsg(-5, ''));
        }

        $type = floatval(Lev::GPv('type'));

        $avatars = UserHelper::getAvatarsDir(0) . $type.'/';
        $files = glob($avatars . '*.jpg');
        if (empty($files)) {
            $notices = Lev::$app['isAdmin'] ? '<p class="red">【管理员提示】将*.jpg头像上传至目录即可：'.$avatars.'</p>' : '';
            Lev::showMessages(Lev::responseMsg(-1, '抱歉，暂无头像可更换', ['notices'=>$notices]));
        }

        if (Lev::GPv('doit')) {
            $avatar = Lev::stripTags(Lev::GPv('avatar'));
            if (!is_file($src = $avatars . $avatar)) {
                Lev::showMessages(Lev::responseMsg(-2, '设置失败，未知头像'));
            }

            UserHelper::setAvatarData($src, Lev::$app['uid'], 'middle', '', true);
            Lev::showMessages(Lev::responseMsg());
        }

        Viewv::render(dirname(__DIR__) . '/layouts/upload/set_avatar.php', [
            'files' => $files,
            'webrootLen' => strlen(Lev::$aliases['@webroot']),
        ]);
    }

    public function actionSettings() {
        static::csrfValidation();
        Adminv::checkAccess();

        $field = Lev::stripTags(Lev::GETv('input'));

        echo Lev::jsonv(UploadHelper::upload($_FILES[$field], static::getUploadIden().'settings', 0));

    }

    /**
     * 仅允许上传图片附件
     */
    public function actionImage() {
        static::csrfValidation();

        $field = Lev::stripTags(Lev::GETv('input'));

        echo Lev::jsonv(UploadHelper::upload($_FILES[$field], static::getUploadIden().'images', 1));

    }

    /**
     * 上传指定扩展名附件
     */
    public function actionAttach() {
        static::csrfValidation();

        $field = Lev::stripTags(Lev::GETv('input'));

        echo Lev::jsonv(uploadsWidget::uploadInputExt($field, static::getUploadIden().'attach'));

    }

    public static function getUploadIden() {
        $iden = Lev::stripTags(Lev::GPv('iden'));
        if ($iden && $iden != Lev::$app['iden'] && ModulesHelper::isInstallModule($iden)) {
            return $iden.'/';
        }
        return '';
    }
}




