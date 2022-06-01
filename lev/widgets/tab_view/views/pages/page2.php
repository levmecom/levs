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

//无切换效果，ajax请求数据并记录，不重复请求同一URL 适合数据量大页面 如：走势图

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
                            <a class="navBtn_ navBtn_<?=$v['tabid'],' ',$v['attr']?>" data-tabid="<?=$v['tabid']?>" href="<?=$v['url']?>">
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


    <div class="page-content appbg">
        <div class="page-content-inner loadDataShowBox" style="padding-top:40px" data-tabid="<?php echo $deTabId?>">
        </div>

        <div class="fotter-box-gen">
            <?php include \lev\widgets\tab_view\tabViewWidget::footerFile(); ?>
        </div>

    </div>

    <div class="LoadPageAjaxJS">
        <script>
            jQuery(function () {
                var tempData = {
                    loadData: {},
                    loaded:{},
                    loadedTopTabid:{},
                    timeoutShowData:{},
                };

                tempData.subnavs = <?=json_encode($subnavs)?>;

                var deTabId = '<?=$deTabId?>';

                var loadDataShowBoxCls  = '.loadDataShowBox';
                var navBtnBoxCls        = '.navBtnBox';
                var navBtnPre           = '.navBtn_';
                var navBtnCls           = navBtnBoxCls+ ' ' +navBtnPre;
                var deTabIdCls          = navBtnPre + deTabId;

                showIconLoader(true);
                Levme.setTimeout(function () {
                    levtoMaoCenter(deTabIdCls, Levme.jQuery(navBtnBoxCls, 1), 300);
                    changeTab(deTabId);
                }, 200);

                Levme.onClick(Levme.jQuery(navBtnCls, 1), function () {
                    var tabid = jQuery(this).data('tabid');
                    var loadRoute = jQuery(this).attr('href');
                    changeTab(tabid, loadRoute);
                    return false;
                });

                Levme.onClick(Levme.jQuery(loadDataShowBoxCls + ' a, a.loadData', 1), function () {
                    var loadRoute = jQuery(this).attr('href');
                    if (!loadRoute || loadRoute === '#' || jQuery(this).hasClass('except')) return;

                    var tabid = jQuery(this).data('tabid');
                    if (!tabid) {
                        tabid = 'tab-x'+base64EncodeUrl(loadRoute);
                    }

                    var topTabid = Levme.jQuery(navBtnCls+'.active').data('tabid');
                    tempData.loadedTopTabid[topTabid] = tabid;

                    loadData(tabid, loadRoute);
                    return false;
                });

                function changeTab(tabid, loadRoute) {
                    var tabCls = navBtnPre + tabid;
                    var btnObj = Levme.jQuery(navBtnCls + tabCls);

                    Levme.jQuery(navBtnCls).removeClass('active');
                    btnObj.addClass('active');
                    levtoMaoCenter(tabCls, Levme.jQuery(navBtnBoxCls, 1), 300);

                    loadRoute = loadRoute || btnObj.attr('href');
                    var showTabid = tempData.loadedTopTabid[tabid] || tabid;
                    loadData(showTabid, loadRoute);
                }

                function loadData(tabid, loadRoute, force) {
                    if (!loadRoute) return;
                    !force && tempData.loadData[tabid] ? showData(tabid) :
                        Levme.ajaxv.getv(loadRoute, function (data, status) {
                            tempData.loadData[tabid] = data;
                            showData(tabid, data);
                        }, {inajax:1});
                }

                function showData(tabid, data) {
                    data = data || tempData.loadData[tabid];
                    if (data.error) {
                        //levtoast(data.message);
                        Levme.viptoast(data);
                        return;
                    }

                    if (tempData.loaded[tabid]) {
                        var htms = Levme.jQuery(loadDataShowBoxCls).html();
                        var thisTabid = Levme.jQuery(loadDataShowBoxCls).attr('data-tabid');
                        if (thisTabid === tabid && tempData.timeoutShowData[tabid] > new Date().getTime() - 10*1000) {
                            //相同tabid 10秒后可再次点击 重载数据
                            return;
                        }
                        tempData.loadData[thisTabid].htms = htms;
                    }
                    tempData.timeoutShowData[tabid] = new Date().getTime();

                    Levme.jQuery(loadDataShowBoxCls).attr('data-tabid', tabid);
                    Levme.jQuery(loadDataShowBoxCls).html(data.htms);

                    if (!tempData.loaded[tabid]) {
                        tempData.loaded[tabid] = tabid;
                        Levme.setTimeout(function () {
                            if (Levme.jQuery(loadDataShowBoxCls).attr('data-tabid') === tabid) {
                                tempData.loadData[tabid].htms = Levme.jQuery(loadDataShowBoxCls).html();//初始化
                            }
                        }, 408);
                    }
                }

            });
        </script>
    </div>
</div>
