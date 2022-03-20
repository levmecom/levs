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
use lev\base\Assetsv;
use lev\base\Controllerv;
use lev\base\Requestv;
use lev\base\Viewv;
use lev\helpers\UrlHelper;
use modules\levs\helpers\levsSetHelper;

class DefaultController extends Controllerv {

    /**
     * Renders the index view for the module
     */
    public function actionIndex()
    {
        if (Lev::checkHideT()) {
            exit('404');
        }
        if ($toIden = Lev::stripTags(Lev::GETv('to')))
        {
            $toIden != 'levs' &&
            parent::redirect(UrlHelper::toModule($toIden));
        }

        if (trim(Lev::stget('homeLink', 'levs')) == '/' && Lev::SiteIden() != 'levs')
        {
            parent::redirect(UrlHelper::home());
        }

        Lev::$app['title'] = levsSetHelper::appTitle() ?: 'Lev导航';

        $appLink = levsSetHelper::appTopLink();

        Viewv::render('default/index', [
            'isApp' => Lev::GPv('app'),
            'topLinkList' => $appLink['top'],
            'linkList' => $appLink['link'],
        ]);

    }

    public function actionToHomeLink() {
        $homeUrl = levsSetHelper::homeLink();
        parent::redirect($homeUrl);
    }

    public static function actionQrcode() {

        Lev::$app['title'] = 'PC端已关闭，扫码访问';

        Assetsv::qrcodeJs();
        Viewv::render('default/qrcode', [
            'qrcodeLink' => Lev::stget('forceAPP', 'levs') ?: Lev::$app['referer'],
        ]);
    }
}