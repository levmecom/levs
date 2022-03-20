<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 *
 * 创建时间：2021-10-04 19:48:33
 *
 * 项目：/levs  -  $  - AdminModulesController.php
 *
 * 作者：{{AUTO GENERATE}}
 */

//此文件使用程序自动生成，下次生成时【不会】覆盖。
//在这里可以补充、完善你的程序逻辑

namespace modules\levs\controllers;

use Lev;
use lev\base\Assetsv;
use lev\base\Viewv;
use lev\helpers\cacheFileHelpers;
use lev\helpers\ModulesHelper;
use lev\helpers\UrlHelper;
use modules\levs\controllers\_gen\BaseAdminModules;
use modules\levs\helpers\downZipmudHelper;
use modules\levs\helpers\siteHelper;
use modules\levs\modules\ftp\helpers\ftpZipHelper;
use modules\levstore\controllers\ApiController;

!defined('INLEV') && exit('Access Denied LEV');

class BaseAdminModulesMiddle extends BaseAdminModules {

    public static function actionSetSiteIndex() {
        $iden = Lev::stripTags(Lev::GPv('iden'));
        cacheFileHelpers::setc('SiteIndex', $iden);
        Lev::showMessages(Lev::responseMsg(1, null, ['tourl'=>UrlHelper::adminModules()]));
    }

    public static function actionIndex()
    {
        if (Lev::GPv('refreshsite')) {
            Lev::setCnzzJs('checkNewMuds', siteHelper::getCheckNewMudJs(0, false, 1));
            Lev::setNotices('正在刷新站点授权，请稍候...');
        }
        Assetsv::animateCss();
        return parent::actionIndex(); // TODO: Change the autogenerated stub
    }

    public static function actionShopSet() {
        Viewv::render(static::$tmpPath . '/shop_set', [
            'checkNewUrl' => UrlHelper::checkNewMud(),
        ]);
    }

    public static function actionCheckNew() {

        $message = Lev::GPv('cache') ? siteHelper::checkNewCache() : siteHelper::checkNew(); //if (Lev::GPv('debug')) print_r($message);
        if (Lev::GPv('inajax')) {
            $js = empty($message['extjs']) ? '//' : $message['extjs'][0];
            $shopBtn = '<a class="link icon-only toLevStoreFormSubmit animated heartBeat wd40" title="模块商城"><svg class="icon" aria-hidden="true" style="color: red;font-size: 24px;"><use xlink:href="#fa-shop"></use></svg><i class="dhua_gif_bg bgx sz50"></i></a>';
            Lev::GPv('refreshsite') && $js.=';window.setInterval(function(){if (jQuery("script.checkNewMud").length >2) { actionLocalStorage("cookieNotices", \'刷新完成！点击商店前往 &raquo; '.$shopBtn.'\'); window.location = "'.UrlHelper::adminModules().'"}}, 1500);';
            exit($js);
        }
        empty($message['message']) && $message['message'] = '无更新模块';
        $message['message'] .= (empty($message['extjs'][1]) ? '' : $message['extjs'][1]);
        Lev::showMessages($message);
    }

    public static function actionAuthSite($returnPm = 0) {
        $siteuid = Lev::stripTags(Lev::GPv('siteuid'));
        $accesstoken = Lev::stripTags(Lev::GPv('accesstoken'));
        $encrys = Lev::stripTags(Lev::GPv('encrys'));

        if ($returnPm === true) {
            return $siteuid && $accesstoken && $encrys ? Lev::toReRoute([
                'admin-modules/auth-site',
                'siteuid' => $siteuid,
                'accesstoken' => $accesstoken,
                'encrys' => $encrys,
            ]) : '';
        }

        $msg = siteHelper::authSite($siteuid, $accesstoken, $encrys);
        $msg['message'] .= '<a href="'.UrlHelper::storeMy().'" class="inblk scale9 button" target="_top" _bk="1">继续绑定站点</a>';
        Lev::GPv('disableBack') && Lev::setNotices($msg['message'], true);
        Lev::showMessages($msg);
        return '';
    }

    /**
     *
     * @see ApiController::actionDownloadZip()
     */
    public static function actionDownloadZip() {
        if (!Lev::GPv('doit')) {
            static::actionIndex();
            return;
        }

        $iv = Lev::stripTags(Lev::GPv('iv'));
        $iden = Lev::stripTags(Lev::GPv('iden'));
        $classdir = Lev::stripTags(Lev::GPv('classdir'));
        $encrys = Lev::stripTags(Lev::GPv('encrys'));

        $msg = downZipmudHelper::actionDownloadZip($iv, $iden, $classdir, $encrys);
        Lev::showMessages($msg);
    }

    /**
     * @return array
     *
     */
    public static function DownloadZipMuds() {
        $iv     = Lev::stripTags(Lev::GPv('iv'));
        $encrys = Lev::stripTags(Lev::GPv('encrys'));
        $idens  = Lev::stripTags(Lev::GPv('idens'));
        $names  = Lev::stripTags(Lev::GPv('names'));
        $vsiden = Lev::stripTags(Lev::GPv('vsiden'));
        $insidens  = Lev::stripTags(Lev::GPv('insidens'));
        $classdirs  = Lev::stripTags(Lev::GPv('classdirs'));
        $downs  = [];
        if ($idens) {
            foreach ($idens as $k => $iden) {
                if (!empty($insidens[$iden])) {
                    $downs[] = [
                        /* @see AdminModulesController::actionDownloadZip() */
                        'href' => Lev::toReRoute(['admin-modules/download-zip', 'iden' => $iden, 'classdir' => $classdirs[$k], 'iv' => $iv, 'encrys' => $encrys, 'token'=>$insidens[$iden], 'doit' => 1]),

                        'name' => $names[$k] . (strpos($vsiden, $iden) !== false ? $vsiden : $iden),
                    ];
                }
            }
        }
        return $downs;
    }

    public static function actionClearTips() {
        if (Lev::GPv('storeUpdateMud')) {
            siteHelper::getNewStoreMuds(null, true);
        }else {
            ftpZipHelper::opCacheClear();
        }
        Lev::showMessages(Lev::responseMsg());
    }

    /**
     * 检查是否存在与基础包合并安装模块
     */
    public static function mergeInstallMuds() {
        $htm = '';
        if (is_file($file = dirname(__DIR__) . '/migrations/merge_muds.install')) {
            $idens = file_get_contents($file);
            if ($idens = Lev::explodev($idens, "\n")) {
                foreach ($idens as $v) {
                    $one = Lev::explodev($v, '=');
                    if (!ModulesHelper::isInstallModule($one[0]) && is_dir(dirname(APPVROOT) . '/' . $one[0])) {
                        $htm .= '<a class="item-content" href="' . UrlHelper::installModule($one[0], 0) . '">
                            <div class="item-inner font14">
                                <div class="item-title">' . $one[1] . '<br>' . $one[0] . '</div>
                                <div class="button button-fill scale9">安装</div>
                            </div>
                        </a>';
                    }
                }
            }
            $htm &&
            $htm = '<div class="card">
                       <div class="card-header">有可用模块可安装</div>
                       <div class="card-content-inner list-block no-hairlines">'.$htm.'</div>
                    </div>';
        }
        return $htm;
    }

    public static function footerHtm()
    {
        //return parent::footerHtm(); // TODO: Change the autogenerated stub
        //return '<tips class="gray inblk scale8">自定义footerHtm</tips>';
        return static::mergeInstallMuds();
    }

    //商城有新版本
    public static $storeUpdateMud = null;

    public static function headerHtm()
    {
        //return parent::headerHtm(); // TODO: Change the autogenerated stub
        $ftpUpdateMud = ModulesHelper::getUpdateMuds();

        static::$storeUpdateMud === null &&
        static::$storeUpdateMud = siteHelper::getNewStoreMuds();

        return Viewv::renderPartial(static::$tmpPath.'/myself/header_htm', [
            'ftpUpdateMud'   => $ftpUpdateMud,
            'storeUpdateMud' => static::$storeUpdateMud,
            'DownloadZipMuds'=> static::DownloadZipMuds(),
            'authSiteUrl'  => static::actionAuthSite(true),
        ]);
    }
}

class AdminModulesController extends BaseAdminModulesMiddle
{

    public static $tmpPath = 'admin-modules';
    public static $trash   = false;//是否允许删除数据

    public static $addurl = false;//是否显示创建数据按钮

    public static $srhtitle    = '模块名称';//模糊搜索字段名称
    public static $srhkeyWhere = "name LIKE '%{srhkey}%'";//模糊搜索sql语句 例：name LIKE '%{srhkey}%'

    public static $limit = 200;//每页显示多少条
    public static $order = ['status ASC, displayorder ASC, versiontime DESC'];//排序

    public static function col_typeid() {
        return [
            0 => '三方模块',
            8 => '官方模块',
            9 => '基础模块',
        ];
    }

    public static function showColumns() {
        return array(
            //'id'           => array('order' => null, 'name' => 'Id', 'thattr' => 'wd30 nowrap'),

            //'typeid'       => array('order' => null, 'name' => '分类', 'thattr' => 'wd30 nowrap'),

            'displayorder' => array('order' => null, 'name' => '排序', 'thattr' => 'wd30 nowrap'),

            'identifier'   => array('order' => null, 'name' => '唯一标识', 'thattr' => 'wd30 nowrap'),

            '_setIndex'   => array('order' => null, 'name' => '设为首页', 'thattr' => 'wd30 nowrap'),

            'name'         => array('order' => null, 'name' => '模块名称', 'thattr' => 'wd30 nowrap', 'merge' => ['copyright']),

            //'classdir'     => array('order' => null, 'name' => 'Classdir', 'thattr' => 'wd30 nowrap'),

            'descs'        => array('order' => null, 'name' => '简短描述', 'thattr' => ''),

            //'copyright'    => array('order' => null, 'name' => '版权', 'thattr' => 'wd30 nowrap'),

            'version'      => array('order' => null, 'name' => '版本号', 'thattr' => 'wd30 nowrap', 'merge' => ['versiontime']),

            //'versiontime'  => array('order' => null, 'name' => '版本时间号', 'thattr' => 'wd30 nowrap'),

            //'settings'     => array('order' => null, 'name' => '通用设置', 'thattr' => 'wd30 nowrap'),

            'status'       => array('order' => null, 'name' => '状态', 'thattr' => 'wd30 nowrap'),

            'uptime'       => array('order' => 1, 'name' => '更新时间', 'thattr' => 'wd30 nowrap'),

            //'addtime'      => array('order' => null, 'name' => '添加时间', 'thattr' => 'wd30 nowrap'),

        );
    }

    public static function setColumnShowtype()
    {
        //return parent::setColumnShowtype(); // TODO: Change the autogenerated stub
        return [
            'showtype' => [
                //从上往下先到先得
                'input_{columnKey}'  => ['width'=>120,'textarea'=>1],//显示为无刷新更改字段
                'srhkey_{columnKey}' => ['or'=>['{columnKey}']],//显示为点击可搜索字段
                'status_{columnKey}' => 1,//显示为开关状态字段 status 自动
                'status__setIndex' => 1,//显示为开关状态字段 status 自动
                'order_{columnKey}'  => 1,//显示为可排序输入框 含order关键字 自动
                'time_{columnKey}'   => 1,//显示为时间格式字段 含time关键字 自动
            ],
        ];
    }

    public static function formatListsv($sv, $lists)
    {
        $v = $sv;
        $sv = parent::formatListsv($sv, $lists); // TODO: Change the autogenerated stub
        $sv['descs'] = '<div class="mud-navb buttons-row scale7 transl">'.ModulesHelper::getAdminNavHtms($sv).'</div>
                        <p class="date transl">'.$v['descs'].'</p>';
        $idenHref = Lev::toReRoute(['superman/modules', 'id' => $v['classdir'] ?: $v['identifier']]);
        if ($v['classdir']) {
            $sv['identifier'] = '<a href="'.$idenHref.'">'.$v['classdir'].'/<p class="date inblk">'.$v['identifier'].'</p></a>';
        }else {
            $sv['identifier'] = ($v['identifier'] == APPVIDEN ? '<absx>主体</absx>' : '') . '
<a href="' . $idenHref . '">' . $v['identifier'] . '</a>';
        }
        $indexIden = cacheFileHelpers::getc('SiteIndex');//$indexIden == $v['identifier'] ? '你确定要取消此首页吗？' :
        $msg = '您确定要将【'.$v['identifier'].'】设置为首页吗？';
        $sv['_setIndex'] = '<label class="label-switch scale8 color-lightblue ajaxBtn" confirmmsg="'.$msg.'" href="'.Lev::toReRoute(['admin-modules/set-site-index', 'iden'=>$v['identifier'], 'id'=>'levs']).'">
        <input type="checkbox" '.($indexIden == $v['identifier'] ? 'checked' : '').'>
        <div class="checkbox"></div>
    </label>';
        //$sv['name'] .= '<p class="date transl" title="版权">'.$v['copyright'].'</p>';
        //$sv['version'].= '<p class="date transl" title="'.date('Ymd.His', $v['versiontime']).'">'.Lev::asRealTime($v['versiontime'], '新装').'更新</p>';
        return $sv;
    }

    public static function redirct($columnKey, $sv)
    {
        //return parent::redirct($columnKey, $sv); // TODO: Change the autogenerated stub
        switch ($columnKey) {
            case 'name': $a = '<a _bk=1 target=_blank class="date inblk" href="'.UrlHelper::toModule($sv['identifier']).'"><svg class="icon color-black"><use xlink:href="#fa-huoj"></use></svg></a>'; break;
            default: $a = ''; break;
        }
        return $a;
    }

    public static function optButtons($sv)
    {
        //return parent::optButtons($sv); // TODO: Change the autogenerated stub
        $new = ModulesHelper::checkNewConfig($sv);
        if ($new || ModulesHelper::checkUpdateFile($sv['identifier'], $sv['classdir'])) {
            $btns = [
                [
                    'name' => '更新',
                    'link' => UrlHelper::updateModule($sv['identifier'], $sv['classdir']),
                    'attr' => $new ? 'color-red' : 'color-gray'
                ], [
                    'name' => '安装',
                    'link' => UrlHelper::installModule($sv['identifier'], $sv['classdir']),
                    'attr' => 'color-black'
                ],
            ];
        }
        $btns[] = [
            'name' => '卸载',
            'link' => UrlHelper::uninstallModule($sv['identifier'], $sv['classdir']),
            'attr' => 'color-gray'
        ];
        $btns[] = [
            'name' => '介绍',
            'link' => UrlHelper::storeView(0, $sv['identifier']),
            'attr' => 'color-green toLevStoreFormSubmit ckTimeout" target=_blank _bk="1'
        ];

        if (static::$storeUpdateMud && isset(static::$storeUpdateMud[$sv['identifier']])) {
            $btns[] = [
                'name' => '升级',
                'link' => UrlHelper::storeUpdateView(static::$storeUpdateMud[$sv['identifier']]['id']),
                'attr' => 'color-yellow toLevStoreFormSubmit ckTimeout isUpdateBtn Iden_'.$sv['identifier'].'" target=_blank _bk="1'
            ];
        }

        $htms = '<div class="wd60 nowrap flex-box">';
        foreach ($btns as $v) {
            $htms .= '<a class="button button-fill wd30 wdmin '.$v['attr'].'" href="' . $v['link'] . '">'.$v['name'].'</a>';
        }
        return $htms.'</div>&nbsp;';
    }

    public static function footerBtns($btns = [])
    {
        $btns = parent::footerBtns($btns); // TODO: Change the autogenerated stub
        unset($btns['pgset']);
        $btns[] = [
            'name' => '<svg class="icon"><use xlink:href="#fa-shop"></use></svg> 新版本检查',
            'link' => Lev::toReRoute(['admin-modules/shop-set']),//UrlHelper::checkNewMud(),
            'attr' => 'color-red openPP',
        ];
        ModulesHelper::isInstallModule('ftp') &&
        $btns[] = [
            'name' => '<svg class="icon"><use xlink:href="#fa-up"></use></svg> FTP上传设置',
            'link' => UrlHelper::ftpsettings(),
            'attr' => 'color-orange" target="_blank',
        ];
        if (!empty(Lev::$app['isDiscuz'])) {
            $btns[] = [
                'name' => 'Discuz！后台',
                'link' => Lev::$aliases['@siteurl'] . '/admin.php',
                'attr' => 'color-black" target="_blank',
            ];
        }
        return $btns;
    }

    public static function cardFooterButtons($btns = [])
    {
        //return parent::cardFooterButtons($btns); // TODO: Change the autogenerated stub
        return '';
    }
}