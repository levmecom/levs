<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-11-09 11:52
 *
 * 项目：rm  -  $  - index.php
 *
 * 作者：liwei 
 */

?>

<div class="page page-admin">
    <?php Lev::navbarAdmin();?>

    <div class="page-content">
        <div class="page-content-inner">

            <div class="list-block card">
                <div class="card-header">
                    <div>提交列表</div>
                    <div class="buttons-row">
                        <div class="flex-box scale8 transr">
                            <a class="button button-fill color-yellow" target="_blank" href="<?=Lev::toSetRoute(['classify'=>1])?>">接口设置</a>
                        </div>
                    </div>
                </div>
                <ul>
                    <?php foreach ($sitemaps as $k => $v):?>
                    <li>
                        <a class="item-content item-link item-content-32" href="<?=Lev::toReRoute(['sitemap/'.$k])?>">
                            <div class="item-inner">
                                <div class="item-title">
                                    <?=$v?>
                                </div>
                            </div>
                        </a>
                    </li>
                    <?php endforeach;?>
                </ul>
            </div>

            <div class="list-block card">
                <div class="card-header">
                    <div>已生成xml</div>
                </div>
                <ul>
                    <?php foreach ($xmls as $k => $v):$v = strstr($v, $xmlweb)?>
                        <li>
                            <a class="item-content item-link item-content-32" target="_blank" _bk="1" href="<?=$v?>">
                                <div class="item-inner">
                                    <div class="item-title">
                                        <?=$k,'. ',$v?>
                                    </div>
                                </div>
                            </a>
                        </li>
                    <?php endforeach;?>
                </ul>
            </div>

        </div>
    </div>
</div>
