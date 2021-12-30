<?php
/* 
 * Copyright (c) 2018-2021  * 
 * 创建时间：2021-04-24 06:14
 *
 * 项目：upload  -  $  - levssqController.php
 *
 * 作者：liwei 
 */


namespace modules\levs\modules\ipban\controllers;

!defined('INLEV') && exit('Access Denied LEV');

use Lev;
use lev\base\Adminv;
use lev\base\Assetsv;
use lev\base\Controllerv;
use lev\base\Viewv;


Adminv::checkAccess();
Assetsv::registerSuperman();


class DefaultController extends Controllerv {

    /**
     * Renders the index view for the module
     */
    public function actionIndex()
    {

        Lev::$app['title'] = '禁止IP访问';

        parent::redirect(Lev::toReRoute(['admin/ip-record']));

        Viewv::render('default/index', [
        ]);

    }

    public static function actionAjax() {
        echo json_encode(Lev::responseMsg(1, '', ['htms'=>'<tips class="flex-box ju-sa" style="height:100px">没有数据</tips>', 'not'=>1]));
    }
}