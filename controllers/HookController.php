<?php
/* 
 * Copyright (c) 2018-2021  * 
 * 创建时间：2021-04-24 06:14
 *
 * 项目：upload  -  $  - levssqController.php
 *
 * 作者：liwei 
 */


namespace modules\levs\controllers;

!defined('INLEV') && exit('Access Denied LEV');

use Lev;
use lev\base\Controllerv;
use lev\base\Viewv;
use modules\levs\helpers\hookHelper;

class HookController extends Controllerv {

    /**
     * Renders the index view for the module
     */
    public function actionIndex()
    {
        header("Content-type: text/javascript; charset=".Lev::$app['charset']);

        echo hookHelper::createGlobarFooterJs();
    }

}