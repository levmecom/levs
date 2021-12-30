<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-04 17:46
 *
 * 项目：upload  -  $  - fk7_v1.php
 *
 * 作者：liwei 
 */

use lev\base\Assetsv;
use modules\levs\widgets\openscreen\welcomeImageWidget;

$panelHtm = '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo (Lev::$app['title'] ? Lev::stripTags(Lev::$app['title']).'-' : ''),Lev::$app['SiteName'],'-Lev'?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <!-- Color theme for statusbar -->
    <meta name="theme-color" content="#2196f3">
    <meta name="format-detection" content="telephone=no, email=no"/>

    <meta name="HandheldFriendly" content="true" />

    <meta name="keywords" content="<?php echo Lev::stripTags(Lev::$app['metakeyword'])?>" />
    <meta name="description" content="<?php echo Lev::stripTags(Lev::$app['metadesc'])?>" />

    <?php echo Assetsv::loadCssFkv1(),Assetsv::Jquery(1);?>

    <script type="text/javascript">var isGuest = <?php echo Lev::$app['uid'] <1 ? 'true' : 'false',
        ', UID = ',(Lev::$app['uid']),
        ', homeUrl = "',Lev::$aliases['@siteurl'],
        '", homeFile = "',Lev::$app['homeFile'],
        '", siteUri = "',Lev::$aliases['@hostinfo'],
        '", APPVIDEN = "',APPVIDEN,
        '", MODULEIDEN = "',Lev::$app['iden'],
        '", assets = "',Lev::$aliases['@assets'],
        '", RouteIden = "',\lev\base\Modulesv::getIdenRouteId(Lev::$app['iden']),
        '", _csrf = "',Lev::$app['_csrf']?>";
    </script>

</head>
<body class="iden-<?=Lev::$app['iden'],(defined('INADMINLEV') ? '-admin' : '')?>">

<div class="statusbar-overlay"></div>
<div class="panel-overlay"></div>

<div class="views">
    <!-- Your main view, should have "view-main" class -->
    <div class="view view-main">

        <?=modules\levs\widgets\openscreen\welcomeImageWidget::swiper();?>


        <!-- Pages container, because we use fixed navbar and toolbar, it has additional appropriate classes-->
        <div class="pages navbar-through toolbar-through">

            <?php (is_file($_View_File) && include $_View_File) || Lev::tips('模板文件不存在：'.$_View_File); ?>

        </div>

    </div>
</div>

<?php echo $panelHtm,Lev::$app['panelHtm'],\lev\widgets\adminModulesNav\adminModulesNav::setBtn();?>

<?php include __DIR__ .'/common/m_z_iframe_screen.php';?>

<?php echo (Lev::$app['aginLoadCss'] ? Assetsv::loadCssFkv1() :''),Assetsv::loadJs()?>
<script>actionLocalStorage('UID', parseInt('<?php echo Lev::$app['uid']?>'));actionLocalStorage('SiteUrl',homeUrl)</script>
<?=Lev::$app['openLoginScreen'] && Lev::$app['uid'] <1 ? '<script>openLoginScreen()</script>' : ''?>
<?php echo Lev::getNotices()?>


<?=\lev\widgets\shares\sharesWidget::run()?>

<?php \modules\levs\helpers\siteHelper::setCnzzJs(); ?>
<?php include __DIR__.'/common/cnzzJs.php'?>
</body>
</html>
