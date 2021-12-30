<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-18 19:18
 *
 * 项目：upload  -  $  - footer.php
 *
 * 作者：liwei 
 */

empty($footerNavs) && $footerNavs = \lev\helpers\SettingsHelper::getFooterNavs();
?>


<!--底部-->
<div class="lottr_footer pclottr_footer footerBoxv">

    <div class="markets-footerv"><?php echo empty($ad) ? '' : Lev::marketsFooter()?></div>

    <?php if ($footerNavs): ?>
    <div class="tcf_t2 card-footer">
        <div class="cft-l"></div>
        <div class="cft-c">
            <?php foreach ($footerNavs as $v): ?>
                <a class="icon-only <?php echo ($v['_link'] || $v['target']==99) ? $v['_target'].$v['_link'] : ''?>" title="<?php echo $v['name']?>">
                    <?php echo $v['_icon']?>
                </a>
            <?php endforeach;?>
        </div>
        <div class="cft-r"></div>
    </div>
    <?php endif;?>

    <p><small>© 2016-<?php echo date('Y'),' ',Lev::$app['version'],' ',Lev::$app['SiteName']?></small></p>
    <?php if (!Lev::ckmobile()):?>
    <p><a class="date inblk scale7" target="_blank" _bk="1" href="https://beian.miit.gov.cn"><?php echo Lev::$app['Icp']?></a></p>
    <?php else:?>
    <p><span class="date inblk scale7 mySelfBtn toIcpBtn" title="你确定要前往工信部网站吗？" href="https://beian.miit.gov.cn"><?php echo Lev::$app['Icp']?></span></p>
    <?php endif;?>
</div>

<div class="LoadPageAjaxJS">
<script>
    jQuery(function () {
        Levme.onClick('.footerBoxv .mySelfBtn', function () {
            if (jQuery(this).hasClass('notAlert')) return;
            var title = jQuery(this).attr('title');
            if (jQuery(this).hasClass('toIcpBtn')) {
                var href = jQuery(this).attr('href');
                myApp.confirm(title, function () {
                    window.location = href;
                });
            }else {
                title && myApp.alert(title);
            }
        });
    });
</script>
</div>
