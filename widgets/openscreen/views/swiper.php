<?php
/**
 * Copyright (c) 2021-2222   All rights reserved.
 *
 * 创建时间：2021-12-04 22:57
 *
 * 项目：levs  -  $  - swiper.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');

?>
<style>
    .swiper-screen-mbox {}
    .swiper-screen-mbox .swiper-container {position: fixed;top: 0;left: 0;right: 0;bottom: 0;z-index: 999998}
    .swiper-screen-mbox .start-ct-btn {position: absolute;bottom: 40px;left: calc(50% - 39px);z-index: 999999;}
    .swiper-screen-mbox .del-ct-btn {position: absolute;top:30px;right:30px;z-index: 999999;background: rgba(0,0,0,0.5) !important;display: flex;}
    .swiper-screen-mbox .del-ct-btn sc {margin-right: 5px}

    .swiper-screen-mbox .swiper-slide,
    .swiper-screen-mbox .swiper-slide img {
        max-width: 100%;
        display: flex;
        align-items: center;
        max-height: 100%;
        justify-content: center;
    }
    .swiper-screen-mbox .swiper-slide img {max-width:360px;}
    .swiper-screen-mbox .swiper-slide a p {color: #fff}
</style>



<div class="swiper-screen-mbox swiperScreenMbox">

    <div class="swiper-container swiperScreenMc appbg">
        <div class="swiper-wrapper">
            <?php foreach ($welcomeImgs as $v): ?>
                <div class="swiper-slide">
                    <a class="<?=$v['_target'],$v['_link']?>">
                        <img class="lazy swiper-lazy" data-src="<?=$v['_src']?>">
                        <p class="scale9"><?=$v['name']?></p>
                    </a>
                </div>
            <?php endforeach;?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
    <a class="button button-fill color-black start-ct-btn startBtn scale8 hiddenx">立即开始</a>
    <a class="button button-fill color-black del-ct-btn startBtn delCtBtn scale8 hiddenx"><sc><?php echo $autoTime?>s</sc>跳过</a>
</div>

<script>
jQuery(function () {
    function showSwiper() {
        var swiper = new Swiper('.swiperScreenMc.swiper-container', {
            speed: 800,
            spaceBetween: 10,
            //preloadImages: true,
            lazyLoading: true,
            lazyLoadingClass: 'lazy',
            pagination: '.swiperScreenMc .swiper-pagination',
            paginationClickable: true,
            autoplay: 5000,
            //slidesPerView: 2,
            //onSlideChangeEnd: function () {},
            // onSlideChangeStart: function () {
            //     window.setTimeout(function () { myApp.initImagesLazyLoad('.pages'); }, 201);
            // }
        });
        jQuery('.swiperScreenMbox').show().removeClass('opacity-hide');
        jQuery('.delCtBtn').removeClass('hiddenx');
    }

    var isApp = <?php echo Lev::GETv('app') ? 1 : 0?>;
    var homeLink = '<?php echo $homeLink;?>';
    var coolTime = parseFloat('<?php echo $coolTime?>');
    var autoTime = parseFloat('<?php echo $autoTime?>');

    isApp && (coolTime = 0.02);//APP 打开仅延时72秒


    Levme.onClick('.startBtn', function () {
        finishToHomeLink();
    });

    //Levme.setMyCity();

    if (actionLocalStorage('swiperScreenMbox') < new Date().getTime() - 3600000 * coolTime) {
        actionLocalStorage('swiperScreenMbox', new Date().getTime());
        showSwiper();
    }else {
        coolTime = 2;
        jQuery('.swiperScreenMbox').fadeOut();
    }

    timerScreen(autoTime);
    function timerScreen(autoTime) {
        var secBox = '.startBtn sc';
        if (jQuery(secBox).length < 1) return;
        var sec = autoTime ? autoTime : parseFloat(jQuery(secBox).html());
        var secx = sec -1;
        jQuery(secBox).html(Math.ceil(secx <0 ? 0 : secx) +'s');
        if (!isNaN(sec) && sec > 0) {
            window.setTimeout(function () {
                timerScreen();
            }, 1000);
        }else {
            finishToHomeLink();
        }
    }

    function finishToHomeLink() {
        if (homeLink) {
            window.location = '<?=Lev::toReWrRoute(['default/to-home-link', 'id'=>'levs'])?>';
        }else {
            jQuery('.swiperScreenMbox').fadeOut(300, 'swing', function () {
                jQuery(this).remove();
            });
        }
    }
});
</script>
