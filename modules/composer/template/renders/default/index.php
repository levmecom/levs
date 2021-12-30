<?php

?>

<div class="page">

    <div class="page-content appbg infinite-scroll composerListInfBox" style="position: relative !important;">
        <div class="page-content-inner" style="max-width:700px;background:rgba(0,0,0,0.2);max-height:min-content;overflow: hidden;">
            <!--幻灯片-->
            <div class="slides-mbox">
                <?php echo \lev\widgets\slides\slidesWidget::run()?>
            </div>

            <div class="composer-mbox">
                <?php echo \lev\widgets\infiniteLoad\infiniteLoadWidget::run('.composerListInfBox', Lev::toReRoute(['default/ajax', 'id'=>'composer']), 1)?>
            </div>


        </div>

        <?php Lev::footer();?>
    </div>

    <?php Lev::navbar(); Lev::toolbar();?>
</div>



