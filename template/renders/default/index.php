<?php

?>

<div class="page">

    <div class="page-content appbg" style="padding-top: 38px !important;">
        <div class="page-content-inner" style="max-width: 960px !important;">
            <!--幻灯片-->
            <div class="slides-mbox">
                <?php echo \lev\widgets\slides\slidesWidget::run()?>
            </div>

            <?php if ($topLinkList):?>
            <div class="applist-box card">
                <div class="card-header">
                    <a>
                        <p class="scale9 color-red">推荐</p>
                    </a>
                    <?php if (Lev::$app['isAdmin']) {?>
                        <a class="date" target="_blank" href="<?php echo Lev::toSetRoute(['classify'=>'app'])?>"><svg class="icon"><use xlink:href="#fa-set"></use></svg></a>
                    <?php }?>
                </div>
                <div class="app-iconlist">
                    <?php foreach ($topLinkList as $v) { if (\modules\levs\helpers\levsSetHelper::checkLinkAccess($v['LinkAccess'])) {?>
                        <a class="<?=$v['_target'],$v['_link']?>">
                            <iconb><?php echo $v['_icon']?></iconb>
                            <p class="scale9"><?=$v['name']?></p>
                        </a>
                    <?php }}?>
                </div>
            </div>
            <?php endif;?>

            <?php foreach ($linkList as $r) { if (\modules\levs\helpers\levsSetHelper::checkLinkAccess($r['LinkAccess'])) {?>
            <div class="applist-box card">
                <div class="card-header">
                    <a class="<?=$r['_target'],$r['_link']?>">
                        <?php echo $r['_icon']?>
                        <p class="scale9"><?=$r['name']?></p>
                    </a>
                    <?php if (Lev::$app['isAdmin']) {?>
                        <a class="date" target="_blank" href="<?php echo Lev::toSetRoute(['classify'=>'app'])?>"><svg class="icon"><use xlink:href="#fa-set"></use></svg></a>
                    <?php }?>
                </div>
                <div class="app-iconlist">
                    <?php if (!empty($r['cld__'])) foreach ($r['cld__'] as $v) { if (\modules\levs\helpers\levsSetHelper::checkLinkAccess($v['LinkAccess'])) {?>
                        <a class="color-black <?=$v['_target'],$v['_link']?>">
                            <iconb><?php echo $v['_icon']?></iconb>
                            <p class="scale9"><?=$v['name']?></p>
                        </a>
                    <?php }}?>
                </div>
            </div>
            <?php }}?>

        </div>

        <?php Lev::footer();?>
    </div>


    <?php Lev::navbar(); Lev::toolbar();?>
</div>


