<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 *
 * 创建时间：2021-11-25 20:03:14
 *
 * 项目：/ipban  -  $  - column_one.php
 *
 * 作者：AUTO GENERATE
 */

//此文件使用程序自动生成，下次生成时会覆盖，不建议修改。

use modules\levs\modules\ipban\controllers\AdminCensusController;

/* @var $k string inputname */
/* @var $v array 一条数据 */
/* @var $_sv array $v的源数据 */
/* @var $_pk string 主键 一般情况下都是id */
/* @var $colname string 字段名称 */
/* @var $isTime array 是否为时间类型字段 */


$showv = AdminCensusController::formatSelectColumn($k, $v[$k]); /* @var $showv string 字段值 */
?>



<?=AdminCensusController::redirct($k, $_sv)?>

<?php if ($_sv[$k] != $v[$k]): echo $v[$k];?>



<?php elseif (isset($v['#set']['showtype']['input_'.$k])):?>
    <?php $__style = empty($v['#set']['showtype']['input_'.$k]['width']) ? '' : 'style="width:'.$v['#set']['showtype']['input_'.$k]['width'].'px !important"'?>

    <?php if (!empty(AdminCensusController::$SelectColumns[$k])):?>
        <select name="<?=$k?>" class="wd80 set-field setField" <?=$__style?> opid="<?=$_pk?>" autocomplete="off">
            <option value="<?=$_sv[$k]?>" class="color-red" selected><?=Lev::arrv($_sv[$k], AdminCensusController::$SelectColumns[$k])?></option>
            <?php foreach (AdminCensusController::$SelectColumns[$k] as $ksv=>$ksn):?>
                <option value="<?=$ksv?>"><?=$ksn?></option>
            <?php endforeach;?>
        </select>
    <?php elseif (isset($v['#set']['showtype']['input_'.$k]['textarea'])):?>
        <textarea name="<?=$k?>" class="wd80 set-field setField" <?=$__style?> opid="<?=$_pk?>" autocomplete="off"><?=$v[$k]?></textarea>
    <?php else:?>
        <input type="text" name="<?=$k?>" class="wd80 set-field setField" <?=$__style?> value="<?=$v[$k]?>" opid="<?=$_pk?>" autocomplete="off">
    <?php endif;?>




<?php elseif (isset($v['#set']['showtype']['srhkey_'.$k])):?>
    <a href="<?php echo AdminCensusController::srhkeyUrl($k, $v[$k])?>">
        <?php echo $showv?>
    </a>


<?php elseif ($k === 'status' || isset($v['#set']['showtype']['status_'.$k])):?>
    <label class="label-switch scale8 color-green setStatus" opid="<?php echo $_pk?>">
        <input type="checkbox" <?=$v[$k]?'':'checked'?>>
        <div class="checkbox"></div>
    </label>


<?php elseif (stripos($k, 'order') !== false || isset($v['#set']['showtype']['order_'.$k])):?>
    <input type="number" name="<?=$k?>" class="dorder set-field setField" value="<?=$v[$k]?>" opid="<?=$_pk?>" autocomplete="off">


<?php elseif ($isTime[$k] || isset($v['#set']['showtype']['time_'.$k])):?>

    <p class="date transr" title="<?=$colname,'：',$v[$k]?>">
        <?=is_numeric($v[$k]) ? Lev::asRealTime($v[$k], '从未') : $v[$k]?>
    </p>
    <?php if ($k === 'uptime'):?>
        <p class="date transr" title="加入时间"><?php echo Lev::asRealTime($v['addtime'], '从未')?></p>
    <?php endif;?>



<?php else:?>
    <?php echo $showv?>


<?php endif;?>