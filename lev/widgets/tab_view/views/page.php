<?php
/**
 * Copyright (c) 2022-2222   All rights reserved.
 *
 * 创建时间：2022-04-03 12:03
 *
 * 项目：levs  -  $  - page.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');

//滑动切换效果，可无限加载数据。适合数据量小的页面

empty($param) || extract($param);
?>


<div class="page <?=\lev\base\Controllerv::$pageName?>" data-page="<?=\lev\base\Controllerv::$pageName?>">
    <?php include \lev\widgets\tab_view\tabViewWidget::toolbarFile(); ?>

    <!--navbar-->
    <div class="navbar navbar-bgcolor-red app-navbar">
        <div class="navbar-inner">
            <?php include \lev\widgets\tab_view\tabViewWidget::navbarInnerFile(); ?>

            <!--导航-->
            <?php if (!empty($subnavs)):?>
                <div class="subnavbar nav-links">
                    <div class="data-table navBtnBox">
                        <?php foreach ($subnavs as $v):?>
                            <a class="tab-link navBtn_ tabBtn_<?=$v['tabid'],' ',$v['attr']?>" data-tabid="<?=$v['tabid']?>" data-tab=".tab-<?=$v['tabid']?>" data-url="<?=$v['url']?>">
                                <?=$v['name']?>
                            </a>
                        <?php endforeach;?>
                    </div>
                    <a class="moreIcon more-icon link icon-only button button-fill color-gray hiddenx">
                        <svg class="icon" aria-hidden="true"><use xlink:href="#fa-bars"></use></svg>
                    </a>
                </div>
            <?php endif;?>

        </div>

    </div>


    <div class="tabs-swipeable-wrap appbg">

        <div class="tabs mbTabsBox" data-tabId="<?php echo $deTabId?>">

            <?php foreach ($subnavs as $v):?>
                <?php \lev\widgets\tab_view\tabViewWidget::includeTabtmp($v)?>
            <?php endforeach;?>

        </div>
    </div>

    <div class="LoadPageAjaxJS hiddenx">
        <script>
            jQuery(function () {
                Levme.scrollLoad.init();
                Levme.tabs.tabShow.init('<?php echo $deTabId?>');
            });
        </script>
    </div>
</div>

