<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-11-25 09:57
 *
 * 项目：rm  -  $  - headerHtm.php
 *
 * 作者：liwei 
 */

?>

<div style="margin: 5px 10px">
    <div><tips>1.用户访问IP直接写入文件，以小时为单位记录到不同文件。点击更新将文件记录导入数据库并统计结果。导入完成后删除记录文件</tips></div>
    <div><tips>2.【更新】按钮不更新当前正在记录的小时。实时会更新。</tips></div>
</div>

<div class="card card-header">
    <div class="flex-box">
    <div class="buttons-row scale8 transl">
        <a class="button-fill button button-small ajaxBtn censusIpBtn" href="<?=Lev::toReRoute(['admin-record/census-ip'])?>">更新</a>
        <a class="button-fill button button-small ajaxBtn censusIpBtn color-blackg" href="<?=Lev::toReRoute(['admin-record/census-ip', 'h'=>24])?>">实时</a>
    </div>
    <div class="buttons-row scale8 transl data-xtable">
        <a class="button-fill button button-small" href="<?=Lev::toReRoute(['admin-census', 'addtime'=>date('Ymd', Lev::$app['timestamp'])])?>">今日统计：<?=$todIp?></a>
        <a class="button-fill button button-small color-gray" href="<?=Lev::toReRoute(['admin-census', 'addtime'=>date('Ymd', Lev::$app['timestamp']-3600*24)])?>">昨日：<?=$ysdIp?></a>
        <a class="button-fill button button-small color-gray" href="<?=Lev::toCurrent(['r'=>'admin-census/only-ip', 'srhkey'=>null], 0,0)?>">只看IP</a>
        <a class="button-fill button button-small color-gray" href="<?=Lev::toCurrent(['r'=>'admin-census/only-pagename', 'srhkey'=>null], 0,0)?>">只看页面</a>
    </div>
    </div>

    <div class="buttons-row scale8 data-xtable">
        <?php foreach ($Ymds as $v):?>
            <a class="button-fill button button-small ajaxBtn censusIpBtn date_<?=str_replace('/', '_', $v)?>" title="导入【<?=$v?>日】文件记录" href="<?=Lev::toReRoute(['admin-record/census-ip', 'date'=>$v, 'h'=>24])?>"><?=$v?></a>
        <?php endforeach;?>
    </div>
</div>

<script>
    jQuery(function () {
        Levme.onClick('.censusIpBtn', function () {
            showIconLoader(true);
            Levme.showNotices('【提示】统计可能需要较长时间，请耐心等候');
        })
    });
</script>