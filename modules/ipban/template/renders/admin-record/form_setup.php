<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 *
 * 创建时间：2021-11-25 20:03:14
 *
 * 项目：/ipban  -  $  - form_setup.php
 *
 * 作者：AUTO GENERATE
 */

//此文件使用程序自动生成，下次生成时会覆盖，不建议修改。


use modules\levs\modules\ipban\controllers\AdminRecordController;

?>


<div class="page page-formb page-admin">
    <?php Lev::toolbarAdmin(1, 0, $footerBtns); Lev::navbarAdmin($addurl, $srhtitle, $srhkey, 1, '', 1); ?>

    <div class="page-content">
        <div class="page-content-inner" style="max-width:1440px">
            <?=$headerHtm?>

            <?php if (count($setupDesc) >1):?>
        <div class="card">
            <div class="card-content-inner buttons-row setups setupsBox" opid="<?=$opid?>">
                <?php foreach ($setupDesc as $k => $name):?>
                    <a class="button <?=$k==$setup ? 'active animated shake' : 'color-gray'?>" href="<?=Lev::toCurrent(['setup'=>$k])?>"><?=$name?></a>
                <?php endforeach;?>
            </div>
        </div>
            <?php endif;?>

        <div class="form-mainb" style="min-width: 660px">
            <form id="saveForm" class="card" action="" method="post">

                <?php echo \lev\widgets\inputs\inputsWidget::form($inputs, $inputsValues, $formPre);?>

                <?php echo \lev\widgets\inputs\inputsWidget::form($extInputs, [], '');?>

                <?=$footerFormInnerHtm?>

                <div class="card-footer">
                    <div class="flex-box">
                    <button type="submit" id="dosubmit" class="button-fill button wd100 dosaveFormBtn">
                        <svg class="icon" aria-hidden="true"><use xlink:href="#fa-save"></use></svg>
                        保 存
                    </button>
                <?php if (Lev::GPv('modelall')):?>
                    <a class="button button-fill color-lightblue scale8 inblk" href="<?php echo Lev::toRoute([$addurl, 'opid'=>$opid, 'modelall'=>null])?>">
                        自定义表单
                    </a>
                <?php else:?>
                    <a class="button button-fill color-red scale8 inblk" href="<?=Lev::toReRoute([AdminRecordController::$tmpPath.'/form', 'opid'=>$opid, 'modelall'=>1])?>">
                        源表单
                    </a>
                <?php endif;?>
                    </div>
                </div>
            </form>
        </div>

            <?=$footerHtm?>
        </div>
    </div>

</div>

