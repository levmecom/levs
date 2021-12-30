<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-19 12:52
 *
 * 项目：upload  -  $  - run.php
 *
 * 作者：liwei 
 */

/* @var $initJs string */
/* @var $loadUrl string */
/* @var $box string */
/* @var $jsonp boolean */
?>


<div class="infiniteLoadShowBoxx" style="width: 100%;">

    <div class="virtual-list listLoadBox" page="2" url="<?php echo $loadUrl?>">

<?php if ($initJs):?>
    <div class="preloader-bv" style="width:100%;min-height:300px;background:rgba(0,0,0,0.07);display: flex;justify-content: center;align-items: center;"><div class="preloader preloader-white"></div></div>
<div class="LoadPageAjaxJS">
    <script>
        (function () {
            'use strict';

            jQuery(function () {
                infiniteLoadJs.init();
            });

            var _loadbox = '<?php echo $box?>.infinite-scroll';
            var infiniteLoadJs = {
                init:function () {

                    window.setTimeout(function () {
                        infiniteLoadJs.data('<?php echo $loadUrl?>');
                    }, 400);

                },
                data:function (loadUrl) {
                    jQuery.ajax({
                        url: loadUrl,
                        data:{init:1, _:Math.random()},
                        dataType:'<?php echo empty($jsonp) ? 'json' : 'jsonp'?>',
                        type:'get',
                        success:function (data) {
                            if (data) {//data.htms
                                jQuery(_loadbox +' .infiniteLoadShowBoxx .listLoadBox').html(data.htms);
                                data.not && Levme.scrollLoad.setNothing(_loadbox);
                                jQuery(_loadbox).find('.loadStart').removeClass('hiddenx');
                                myApp.initImagesLazyLoad('.page-content');
                            }
                        },
                        error:function (data) {

                        }
                    });
                }
            };

        })();
    </script>
</div>
<?php endif;?>
        <script>
            jQuery(function () {
                Levme.scrollLoad.init();
            });
        </script>
    </div>
    <div class="date infinite-scroll-preloader hiddenx"><div class="preloader preloader-red"></div></div>

    <div class="flex-box ju-sa">
        <a class="date loadStart <?php echo $initJs?'hiddenx':''?>" data-box="<?php echo $box?>">点击加载更多</a>
    </div>
</div>

