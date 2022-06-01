<?php
/**
 * Copyright (c) 2022-2222   All rights reserved.
 *
 * 创建时间：2022-04-04 22:36
 *
 * 项目：levs  -  $  - navbar_inner.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');

//page.php include 文件

/* @var $navbarInner */

?>

<!--带搜索框头部-->
<?php if (!empty($navbarInner['srh'])) {?>

    <div class="left">
        <a class="link icon-only" href="<?php echo \lev\helpers\UrlHelper::homeMud()?>">
            <svg class="icon"><use xlink:href="#fa-home"></use></svg>
        </a>
    </div>
    <div class="title nowrap">
        <div class="searchbar tabViewNavbarSrhForm" style="background: none;">
        <div class="searchbar-input">
            <input type="search" placeholder="<?=$navbarInner['srh']['placeholder']?>">
        </div>
        </div>
    </div>
    <div class="right">
        <?php if (\lev\widgets\shares\sharesWidget::openShareBtn()):?>
            <a class="link icon-only scale9 showShareGridBtn"><svg class="icon"><use xlink:href="#fa-share"></use></svg></a>
        <?php endif;?>
        <?php echo Lev::actionObjectMethodIden('levsign', 'modules\levsign\levsignHelper', [], 'signNavbarBtn')?>
        <?=\lev\widgets\login\loginWidget::loginAvatarBtn()?>
    </div>

    <script>
        jQuery(function () {

            jQuery(document).on('keyup', '.tabViewNavbarSrhForm input', function (e) {
                if (e.which === 13) {
                    var srhResult = [];
                    var boxObj,btnType,dataid;
                    if (jQuery(this).parents('.navbar').find('.slideBtnBox').length > 0) {
                        boxObj = jQuery(this).parents('.navbar').find('.slideBtnBox');
                        btnType = '.slideBtn_';
                        dataid = 'slideid';
                    }else {
                        boxObj = jQuery(this).parents('.navbar').find('.navBtnBox');
                        btnType = '.navBtn_';
                        dataid = 'tabid';
                    }
                    var srhkey = jQuery(this).val();
                    if (jQuery(boxObj).find('.tabBtn_'+ srhkey).length >0) {
                        myApp.showTab(jQuery(boxObj).find('.tabBtn_'+ srhkey).data('tab'), false);
                        return;
                    }else if (jQuery(boxObj).find(btnType+ srhkey).length >0) {
                        Levme.jQuery(btnType+ jQuery(boxObj).find(btnType+ srhkey).data(dataid)).click();
                        return;
                    }
                    boxObj.find(btnType).each(function () {
                        var thisObj = this;
                        var txt = jQuery(thisObj).text();
                        if (txt && txt.indexOf(srhkey) >=0) {
                            var json = {
                                text:jQuery.trim(txt),
                                onClick:function () {
                                    if ( jQuery(thisObj).hasClass('tab-link') ) {
                                        myApp.showTab(jQuery(thisObj).data('tab'), false)
                                    }else {
                                        Levme.jQuery(btnType+ jQuery(thisObj).data(dataid)).click();
                                    }
                                }
                            };
                            srhResult.push(json);
                        }
                    });
                    if (srhResult.length > 0) {
                        myApp.actions(srhResult);
                    }else {
                        levtoast('未搜索到结果');
                    }
                }
            });
        });
    </script>

<?php } else {
    if ($navbarInner === null) {
        Lev::navbar([], true);
    }else if (is_file($navbarInner)) {
        include $navbarInner;
    }else {
        echo $navbarInner;
    }
}?>