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

$panelHtm = '';
?>
<style>#cpcontainer {padding-bottom: 0 !important;padding-top: 0 !important;}</style>
<style>.ziframescreen .closescreen svg.icon {margin-top: 7px !important;}</style>
</div>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta charset="<?php echo Lev::$app['charset']?>">
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo Lev::$app['charset']?>" />
    <title><?php echo Lev::$app['title'],' - ',Lev::$app['SiteName'],' - LEV'?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">

    <meta name="apple-mobile-web-app-capable" content="yes">
    <!-- Color theme for statusbar -->
    <meta name="theme-color" content="#2196f3">
    <meta name="format-detection" content="telephone=no, email=no"/>

    <meta name="HandheldFriendly" content="true" />

    <meta name="keywords" content="<?php echo Lev::$app['metakeyword']?>" />
    <meta name="description" content="<?php echo Lev::$app['metadesc']?>" />
    <?php echo Assetsv::loadCssFkv1(),Assetsv::Jquery(1);?>

    <script type="text/javascript">var isGuest = <?php echo Lev::$app['uid'] <1 ? 'true' : 'false',
        ', UID = ',(Lev::$app['uid']),
        ', homeUrl = "',Lev::getAlias('@siteurl'),
        '", homeFile = "',Lev::$app['homeFile'],
        '", siteUri = "',Lev::getAlias('@hostinfo'),
        '", APPVIDEN = "',APPVIDEN,
        '", MODULEIDEN = "',Lev::$app['iden'],
        '", _csrf = "',Lev::$app['_csrf']?>";
    </script>

<div class="statusbar-overlay"></div>
<div class="panel-overlay"></div>

<div class="views" style="max-height: calc(100% - 35px)">
    <!-- Your main view, should have "view-main" class -->
    <div class="view view-main">

        <!-- Pages container, because we use fixed navbar and toolbar, it has additional appropriate classes-->
        <div class="pages navbar-through toolbar-through">

            <?php (is_file($_View_File) && include $_View_File) || Lev::tips('tmp not exist：'.$_View_File); ?>

        </div>

    </div>
</div>

<?php echo $panelHtm;?>

<?php include __DIR__ .'/common/m_z_iframe_screen.php';?>

<?php echo Assetsv::loadJs()?>

<script>actionLocalStorage('UID', parseInt('<?php echo Lev::$app['uid']?>'));</script>

