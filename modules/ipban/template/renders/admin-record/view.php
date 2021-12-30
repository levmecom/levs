<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 *
 * 创建时间：2021-11-25 20:03:14
 *
 * 项目：/ipban  -  $  - view.php
 *
 * 作者：AUTO GENERATE
 */

//此文件使用程序自动生成，下次生成时会覆盖，不建议修改。

use modules\levs\modules\ipban\controllers\AdminRecordController;

/* @var $trash boolean */
/* @var $footerBtns array */
/* @var $addurl string */
/* @var $srhtitle string */
/* @var $srhkey string */
/* @var $showColumns array */
/* @var $allColumns array */
/* @var $info array */
?>

<?php

$_pk = $info['id'];
$_sv = $info;
$v = AdminRecordController::formatListsv($_sv, [$_sv]);

?>

<div class="page page-admin">
    <?php Lev::toolbarAdmin(0, $trash, $footerBtns); Lev::navbarAdmin($addurl, $srhtitle, $srhkey, 1, '', 0, $trash); ?>

    <div class="page-content">
        <?=$headerViewHtm?>

        <div class="card">
            <div class="card-header" style="justify-content: space-around">
                <div class="buttons-row scale9">
                <?php if (Lev::GPv('formatv')):?>
                    <a class="button-fill button color-red" href="<?=Lev::toCurrent(['formatv'=>0])?>">源数据</a>
                <?php else:?>
                    <a class="button-fill button color-lightblue" href="<?=Lev::toCurrent(['formatv'=>1])?>">格式数据</a>
                <?php endif;?>
                <?php if ($addurl):?>
                    <a class="button button-fill color-blue" href="<?php echo Lev::toRoute([$addurl, 'opid'=>$_pk])?>">
                        编辑
                    </a>
                    <a class="button button-fill color-red" href="<?=Lev::toReRoute([AdminRecordController::$tmpPath.'/form', 'opid'=>$_pk, 'modelall'=>1])?>">
                        源编辑
                    </a>
                <?php endif;?>
                <?php if ($trash):?>
                    <a class="button-fill button color-gray deleteOneBtn" opid="<?=$_pk?>">删除</a>
                <?php endif;?>
                </div>
            </div>
            <div class="data-xtable">
                <table>
                    <tr>
                        <th class="wd120 numeric-cell">字段名称</th>
                        <th>字段内容(值)</th>
                    </tr>
                    <?php foreach ($info as $k => $value):?>
                    <?php $isTime[$k] = '';?>
                        <tr>
                            <td class="numeric-cell"><b><?=Lev::arrv($k, $allColumns)?>：</b></td>
                            <td><div style="white-space: normal !important;word-break: break-all;">
                            <?php if (Lev::GPv('formatv')):?>
                                <?php $colname = $allColumns[$k]; include __DIR__ . '/column_one.php' ?>
                            <?php else:?>
                                <pre style="white-space: pre-wrap;word-break: break-all;"><?=$value?></pre>
                            <?php endif;?>
                                </div>

                            <?php
                                if (stripos($k, 'time') !== false) {
                                    echo '<div class="wd120 wdmin color-gray">'.date('Y-m-d H:i:s', $value).'</div>';
                                }
                            ?>

                            </td>
                        </tr>
                    <?php endforeach;?>
                </table>
            </div>
            <div class="card-footer"></div>
        </div>


        <?=$footerViewHtm?>
    </div>

</div>
