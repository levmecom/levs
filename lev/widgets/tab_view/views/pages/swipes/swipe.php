<?php
/**
 * Copyright (c) 2022-2222   All rights reserved.
 *
 * 创建时间：2022-04-17 23:36
 *
 * 项目：levs  -  $  - swipe.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');

empty($param) || extract($param);
?>

<div class="page <?=$pageName?>" data-page="<?=$pageName?>">
    <?php include \lev\widgets\tab_view\tabViewWidget::toolbarFile(); ?>

    <!--navbar-->
    <div class="navbar navbar-bgcolor-red app-navbar">
        <div class="navbar-inner">
            <?php include \lev\widgets\tab_view\tabViewWidget::navbarInnerFile(); ?>

            <!--导航-->
            <?php if (!empty($subnavs)):?>
                <div class="subnavbar nav-links">
                    <div class="data-table slideBtnBox">
                        <?php foreach ($subnavs as $v):?>
                            <a class="slideBtn_ slideBtn_<?=$v['tabid'],' ',$v['attr']?>" data-slideid="<?=$v['tabid']?>" href="<?=$v['url']?>">
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

    <div class="swipeMainBox swiper-container appbg" style="height:100%">

        <div class="swipesBox swiper-wrapper" data-showid="<?php echo $deTabId?>">

            <?php foreach ($subnavs as $v) { include __DIR__ . '/slide.php'; }?>

        </div>
    </div>

    <div class="fotter-box-gen">
        <?php include \lev\widgets\tab_view\tabViewWidget::footerFile(); ?>
    </div>

    <div class="LoadPageAjaxJS hiddenx">
        <script>
            jQuery(function () {
                if (Levme.tempDatas['<?=$pageName?>']) {
                    if ( typeof slideConfig !== "undefined" && slideConfig.Swipev !== null ) {
                        slideConfig.Swipev.destroy(true, true);
                    }
                    if (Levme.jQuery('.swipeMainBox.swiper-container').hasClass('swiper-container-horizontal')) {
                        return;
                    }
                }
                Levme.tempDatas['<?=$pageName?>'] = '<?=$pageName?>';

                var tempData = {
                    dataLoading:0,
                    loadData: {},
                    loaded:{},
                    loadedNavSlideid:{},
                    timeoutShowData:{},
                };
                var slideConfig = {
                    pageName: '<?=$pageName?>',
                    deTabId: '<?=$deTabId?>',
                    subnavs:<?=json_encode($subnavs?:[])?>,
                    loadDataShowBoxCls: '.loadDataShowBox',
                    slideBtnBoxCls: '.slideBtnBox',
                    slideBtnPre: '.slideBtn_',
                    Swipev:null,
                    initSlide() {
                        //slideConfig.Swipev !== null && slideConfig.Swipev.destroy(true, true);
                        var SwiperContainer = slideConfig.getPageClass() + ' .swipeMainBox.swiper-container';
                        slideConfig.Swipev = new Swiper(SwiperContainer, {
                            initialSlide: slideConfig.getInitialSlide(),
                            onInit: function(swiper){//Swiper初始化了

                            },
                            onSlideChangeStart: function (swiper) {
                                var index = swiper.activeIndex;
                                var slideid = jQuery(slideConfig.getCommonSlideBtn()).eq(index).data('slideid');
                                var loadRoute = jQuery(slideConfig.getSlideBtn(slideid)).attr('href');
                                slideConfig.setEmptyLoadDataShowBox();
                                changeTab(slideid, loadRoute);
                            },
                            onSlideChangeEnd: function (swiper) {
                            }
                        });

                        Levme.onClick(slideConfig.getCommonSlideBtn(), function () {
                            var index = jQuery(this).index();
                            slideConfig.Swipev.slideTo(index, undefined, true);
                            return false;
                        });
                    },
                    getPageClass() {
                        return '.'+ slideConfig.pageName
                    },
                    setEmptyLoadDataShowBox (slideid) {
                        if (slideid === undefined) {
                            jQuery(slideConfig.getLoadDataShowBoxCls(slideid)).each(function () {
                                var thisHtms = jQuery(this).html();
                                var _slideid = jQuery(this).attr('data-boxid');
                                thisHtms && saveTempDataHtms(_slideid, thisHtms);
                            })
                        }else {
                            saveTempDataHtms(slideid, jQuery(slideConfig.getLoadDataShowBoxCls(slideid)).html());
                        }
                        jQuery(slideConfig.getLoadDataShowBoxCls(slideid)).html('');
                    },
                    getLoadDataShowBoxCls (slideid) {
                        var swipeSlideBox = slideid === undefined ? '' : ' .swipeSlideBox_'+ slideid;
                        return slideConfig.getPageClass() + swipeSlideBox +' '+ slideConfig.loadDataShowBoxCls;
                    },
                    getActiveLoadDataShowBoxCls () {
                        var slideid = jQuery(slideConfig.getCommonSlideBtn() +'.active').data('slideid');
                        var swipeSlideBox = ' .swipeSlideBox_'+ slideid;
                        return slideConfig.getPageClass() + swipeSlideBox +' '+ slideConfig.loadDataShowBoxCls;
                    },
                    getSlideBtn(slideid) {
                        return slideConfig.getPageClass() +' '+ slideConfig.slideBtnBoxCls +' '+ slideConfig.slideBtnPre + slideid;
                    },
                    getCommonSlideBtn() {
                        return slideConfig.getPageClass() +' '+ slideConfig.slideBtnBoxCls +' '+ slideConfig.slideBtnPre;
                    },
                    getSlideBtnBoxCls () {
                        return slideConfig.getPageClass() +' '+ slideConfig.slideBtnBoxCls;
                    },
                    getInitialSlide() {
                        return jQuery(slideConfig.slideBtnPre + slideConfig.deTabId).index(slideConfig.getCommonSlideBtn());
                    }
                };
                var infiniteScroll = {
                    tempData: {
                        loading:{},
                        btnTips:{},
                    },
                    tempPageDatas:{},
                    initInfinite:function(dataInfiniteBox, pageClass) {
                        infiniteScroll.isInfiniteFinish(dataInfiniteBox) &&
                        infiniteScroll.setInfiniteFinish(dataInfiniteBox);

                        Levme.onClick('.infinite-scroll .startInfiniteScrollBtn', function () {
                            var boxid = jQuery(this).parents('.infinite-scroll').find('.dataInfiniteBox').attr('data-boxid');
                            var thisInfiniteBox = infiniteScroll.getInfiniteBoxCls(boxid) + '.infinite-scroll';
                            infiniteScroll.ajaxLoadData(pageClass ? pageClass +' '+ thisInfiniteBox : thisInfiniteBox, true);
                            return false;
                        });
                    },
                    getInfiniteBoxCls(boxid) {
                        return '.infiniteBox_'+ boxid;
                    },
                    getStartInfiniteScrollBtn(dataInfiniteBox) {
                        return jQuery(dataInfiniteBox).parents('.infinite-scroll').find('.startInfiniteScrollBtn');
                    },
                    setDefault (dataInfiniteBox, message) {
                        infiniteScroll.setInfiniteFinish(dataInfiniteBox, message, true);
                    },
                    setInfiniteFinish (dataInfiniteBox, message, reset) {
                        var startInfiniteScrollBtnObj = infiniteScroll.getStartInfiniteScrollBtn(dataInfiniteBox);
                        if (reset) {
                            jQuery(dataInfiniteBox).find('.dataInfiniteBox').attr('page', "2");
                            startInfiniteScrollBtnObj.removeClass('InfiniteFinished disabled');
                            startInfiniteScrollBtnObj.html(message ? message : '点击加载更多').removeClass('hiddenx').attr('disabled', false);
                        }else {
                            jQuery(dataInfiniteBox).find('.dataInfiniteBox').attr('page', "-1");
                            startInfiniteScrollBtnObj.addClass('InfiniteFinished disabled');
                            startInfiniteScrollBtnObj.html(message ? message : '没有了').removeClass('hiddenx').attr('disabled', true);
                        }
                    },
                    isInfiniteFinish (dataInfiniteBox) {
                        return parseInt(jQuery(dataInfiniteBox).attr('not')) === 1;
                    },
                    ajaxLoadData (InfiniteBox, force) {//list-block virtual-list
                        var dataInfiniteBox = InfiniteBox + ' .dataInfiniteBox';

                        var page = parseInt(jQuery(dataInfiniteBox).attr('page'));
                        page === -2 && (page = 2) && jQuery(dataInfiniteBox).attr('page', page);
                        if (page === 2) {
                            myApp.attachInfiniteScroll(jQuery(InfiniteBox));//给指定的 HTML 容器添加无限滚动事件监听器
                            jQuery(document).off('infinite', InfiniteBox).on('infinite', InfiniteBox, function (e) {
                                if (slideConfig.Swipev.animating) {
                                    //levtoast('切换期间，禁止自动加载数据');
                                    return;
                                }
                                page >= 2 && doInfiniteData();
                            });
                        }
                        if (force) {
                            doInfiniteData();
                            return false;
                        }
                        function doInfiniteData() {
                            if (infiniteScroll.getLoadingStatus(dataInfiniteBox)) {
                                showIconLoader(true);
                                return false;
                            }

                            var jsonp = jQuery(dataInfiniteBox).attr('jsonp') ? 'jsonp' : 'json';
                            var url = jQuery(dataInfiniteBox).attr('url');
                            page = parseInt(jQuery(dataInfiniteBox).attr('page'));
                            page = isNaN(page) ? 0 : page;
                            if (page < 2) return true;
                            if (!url) {
                                return true;
                            }
                            infiniteScroll.setLoadingStatus(dataInfiniteBox, 1);
                            jQuery.ajax({
                                url: url,
                                data:{page:page, infinite:1, _csrf:_csrf, _:Math.random()},
                                dataType:jsonp,
                                type:'get',
                                success:function(data){
                                    infiniteScroll.setLoadingStatus(dataInfiniteBox, 0);
                                    if (!data || data.status <0 || parseInt(data.not) || !data.htms || !jQuery.trim(data.htms)) {
                                        var message = data && data.message ? data.message : undefined;
                                        infiniteScroll.setInfiniteFinish(dataInfiniteBox, message);
                                    }else {
                                        var status = parseInt(data.status);
                                        //data.message && levtoast(data.message, 5000);
                                        Levme.viptoast(data, 5000);

                                        if (status === -5) {
                                            openLoginScreen();
                                        } else if (status > 0) {
                                            var nextPage = data.page ? data.page : page + 1;
                                            var dataHtms = infiniteScroll.formatDataHtms(page, data.htms);
                                            infiniteScroll.setTempPageDatas(url, page, dataHtms);
                                            jQuery(dataInfiniteBox).attr('page', nextPage);
                                            jQuery(dataInfiniteBox).append(dataHtms);
                                            infiniteScroll.cutMaxDataHtms(dataInfiniteBox);
                                            Levme.setTimeout(function () {
                                                myApp.initImagesLazyLoad(dataInfiniteBox);
                                                saveTempDataHtms(jQuery(dataInfiniteBox).attr('data-boxid'), jQuery(dataInfiniteBox).html());
                                            }, 101);
                                        }
                                    }
                                },
                                error:function(data){
                                    infiniteScroll.setLoadingStatus(dataInfiniteBox, 0);
                                    errortips(data);
                                }
                            });
                        }
                    },
                    cutMaxDataHtms (dataInfiniteBox) {
                        var InfinitePageObj = jQuery(dataInfiniteBox).find('.InfinitePage_');
                        var datahtms = jQuery(dataInfiniteBox).html();
                        if (datahtms && datahtms.length > 10000) {
                            //console.log(datahtms.length);
                            if (InfinitePageObj.length >1) {
                                InfinitePageObj.eq(0).remove();
                            }
                        }
                    },
                    formatDataHtms (page, htms) {
                        return '<div class="InfinitePage_ InfinitePage_'+ page +'" page="'+ page +'">'+ htms +'</div>';
                    },
                    setTempPageDatas(url, page, dataHtms) {
                        infiniteScroll.tempPageDatas[base64EncodeUrl(url)+'_'+page] = dataHtms;
                    },
                    getTempPageDatas(url, page) {
                        return infiniteScroll.tempPageDatas[base64EncodeUrl(url)+'_'+page];
                    },
                    setLoadingStatus(dataInfiniteBox, value) {
                        infiniteScroll.tempData.loading[dataInfiniteBox] = value;
                        var btn = infiniteScroll.getStartInfiniteScrollBtn(dataInfiniteBox);
                        if (infiniteScroll.tempData.btnTips[dataInfiniteBox] === undefined) {
                            infiniteScroll.tempData.btnTips[dataInfiniteBox] = btn.html();
                        }
                        if (value) {
                            btn.html('<div class="preloader preloader-white"></div>');
                        }else {
                            hideIconLoader();
                            btn.html(infiniteScroll.tempData.btnTips[dataInfiniteBox] || '点击加载更多');
                        }
                    },
                    getLoadingStatus(dataInfiniteBox) {
                        return infiniteScroll.tempData.loading[dataInfiniteBox];
                    },
                };

                slideConfig.initSlide();

                var deSlideid = slideConfig.deTabId;

                var loadDataShowBoxCls  = slideConfig.getLoadDataShowBoxCls();
                var slideBtnBoxCls      = slideConfig.getSlideBtnBoxCls();
                var slideBtnPre         = slideConfig.slideBtnPre;
                var slideBtnCls         = slideConfig.getCommonSlideBtn();
                var deSlideidCls        = slideConfig.getSlideBtn(deSlideid);

                showIconLoader(true);
                Levme.setTimeout(function () {
                    levtoMaoCenter(deSlideidCls, slideBtnBoxCls, 300);
                    changeTab(deSlideid);
                    infiniteScroll.initInfinite(slideConfig.getActiveLoadDataShowBoxCls(), slideConfig.getPageClass());
                }, 200);

                Levme.onClick(loadDataShowBoxCls +' a, a.loadData', function () {
                    var loadRoute = jQuery(this).attr('href');
                    if (!loadRoute || loadRoute === '#' || jQuery(this).hasClass('except') || jQuery(this).attr('_bk')) return;

                    var slideid = jQuery(this).data('slideid');
                    if (!slideid) {
                        slideid = 'slide-x'+base64EncodeUrl(loadRoute);
                    }

                    var NavSlideid = jQuery(slideBtnCls+'.active').data('slideid');
                    tempData.loadedNavSlideid[NavSlideid] = slideid;

                    loadData(slideid, loadRoute);
                    return false;
                });

                function changeTab(slideid, loadRoute) {
                    var tabCls = slideBtnPre + slideid;
                    var btnObj = jQuery(slideBtnCls + tabCls);

                    jQuery(slideBtnCls).removeClass('active');
                    btnObj.addClass('active');
                    levtoMaoCenter(tabCls, slideBtnBoxCls, 300);

                    loadRoute = loadRoute || btnObj.attr('href');
                    var showSlideid = tempData.loadedNavSlideid[slideid] || slideid;
                    loadData(showSlideid, loadRoute);
                }

                function loadData(slideid, loadRoute, force) {
                    //if (tempData.dataLoading) return;
                    tempData.dataLoading = 1;

                    if (!loadRoute) return;
                    !force && tempData.loadData[slideid] ? showData(slideid) : ajaxData();
                    function ajaxData() {
                        showIconLoader(true);
                        Levme.ajaxv.getv(loadRoute, function (data, status) {
                            tempData.dataLoading = 0;
                            tempData.loadData[slideid] = data;
                            if (data.htms) {
                                data.htms = infiniteScroll.formatDataHtms(1, data.htms);
                            }
                            showData(slideid, data);
                        }, {inajax:1});
                    }
                }

                function showData(slideid, data) {
                    tempData.dataLoading = 0;

                    data = data || tempData.loadData[slideid];
                    if (data.error) {
                        //levtoast(data.message);
                        Levme.viptoast(data);
                        return;
                    }

                    var ActiveLoadDataShowBoxCls = slideConfig.getActiveLoadDataShowBoxCls();

                    var htms = jQuery(ActiveLoadDataShowBoxCls).html();
                    if (htms) {
                        if (tempData.loaded[slideid]) {
                            var thisSlideid = jQuery(ActiveLoadDataShowBoxCls).attr('temp-slideid');
                            if (thisSlideid === slideid && tempData.timeoutShowData[slideid] > new Date().getTime() - 10 * 1000) {
                                //相同Slideid 10秒后可再次点击 重载数据
                                return;
                            }
                            saveTempDataHtms(thisSlideid, htms);
                        }
                        tempData.timeoutShowData[slideid] = new Date().getTime();
                    }

                    jQuery(ActiveLoadDataShowBoxCls).attr('temp-slideid', slideid);
                    jQuery(ActiveLoadDataShowBoxCls).html(data.htms);

                    if (!tempData.loaded[slideid]) {
                        Levme.setTimeout(function () {
                            if (jQuery(ActiveLoadDataShowBoxCls).attr('temp-slideid') === slideid) {
                                tempData.loaded[slideid] = slideid;
                                //tempData.loadData[slideid].htms = jQuery(ActiveLoadDataShowBoxCls).html();//初始化
                                saveTempDataHtms(slideid, jQuery(ActiveLoadDataShowBoxCls).html());
                            }
                        }, 408);
                    }
                }

                function saveTempDataHtms(slideid, htms) {
                    if (!htms) {
                        return;
                    }
                    if (!tempData.loadData[slideid]) {
                        tempData.loadData[slideid] = {};
                    }
                    tempData.loadData[slideid].htms = htms;
                }
            });
        </script>
    </div>
</div>
