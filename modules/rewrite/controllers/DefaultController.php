<?php
/* 
 * Copyright (c) 2018-2021  * 
 * 创建时间：2021-04-24 06:14
 *
 * 项目：upload  -  $  - levssqController.php
 *
 * 作者：liwei 
 */


namespace modules\levs\modules\rewrite\controllers;

!defined('INLEV') && exit('Access Denied LEV');

use Lev;
use lev\base\Adminv;
use lev\base\Assetsv;
use lev\base\Controllerv;
use lev\base\Viewv;
use lev\helpers\cacheFileHelpers;
use modules\levs\modules\rewrite\helpers\rewriteSetHelper;
use modules\levs\modules\rewrite\rewriteHelper;

Adminv::checkAccess();

class DefaultController extends Controllerv {
    private static function SetRewrite($close)
    {
        if ($close) {
            cacheFileHelpers::clearc('levs_rewrite');
            is_file($file = Lev::$aliases['@webroot'] . '/levs_rewrite.php') && unlink($file);
        }else {
            if (!is_file($reFile = Lev::$aliases['@webroot'] . '/.htaccess')) {
                file_put_contents(Lev::$aliases['@webroot'] . '/.htaccess', file_get_contents(rewriteHelper::apacheRewriteFile()));
            }
            $file = rewriteHelper::levsRewriteFile();
            $data = str_ireplace('!defined(', '//!defined(', file_get_contents($file));
            $data = str_replace('{{%APPVROOT}}', APPVROOT, $data);
            file_put_contents(Lev::$aliases['@webroot'] . '/levs_rewrite.php', $data) && cacheFileHelpers::setc('levs_rewrite', 1);
        }
    }

    /**
     * Renders the index view for the module
     */
    public function actionIndex()
    {

        Lev::$app['title'] = 'URL美化';

        Assetsv::highlight();
        Viewv::render('default/index', [
            'assetsBaseUrl' => Assetsv::getAppassets('rewrite') . '/statics',
        ]);

    }

    public function actionSettings() {
        Lev::$app['title'] = 'URL美化（伪静态）';

        if (Lev::GPv('do')) {
            $close = Lev::GPv('close');
            static::SetRewrite($close);
            if ($close) {
                $tips = '（关闭伪静态）';
                $tourl = ['还原URL（启用伪静态）' => Lev::toReRoute(['default/settings', 'do'=>1, 'id'=>'rewrite'])];
            }else {
                $tips = '（启用伪静态）';
                $tourl = ['还原URL（关闭伪静态）' => Lev::toReRoute(['default/settings', 'do'=>1, 'close'=>1, 'id'=>'rewrite'])];
            }
            return Lev::showMessage($tips.'操作成功！', $tourl);
        }
        if (Lev::GPv('close')) {
            return Lev::showMessage('关闭后以前的美化URL将失效无法访问（站点将出现异常），您确定要关闭（伪静态）吗？', Lev::toCurrent(['do'=>1]), 'submit');
        }
        return Lev::showMessage('启用后【不影响】动态URL访问，您确定要启用URL美化（伪静态）吗？', Lev::toCurrent(['do'=>1]), 'submit');
    }

    public static function actionAjax() {
        echo json_encode(Lev::responseMsg(1, '', ['htms'=>'<tips class="flex-box ju-sa" style="height:100px">没有数据</tips>', 'not'=>1]));
    }
}