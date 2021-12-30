<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-12-04 11:36:26
 *
 * 项目：/levs  -  $  - index.php
 *
 * 作者：AUTO GENERATE
 */

//此文件使用程序自动生成，下次生成时会覆盖，不建议修改。

use modules\levs\controllers\AdminUsersController;

/* @var $trash boolean */
/* @var $footerBtns array */
/* @var $addurl string */
/* @var $srhtitle string */
/* @var $srhkey string */
/* @var $showColumns array */
/* @var $allColumns array */
/* @var $asc boolean */
/* @var $pages array['lists', 'pages'] */
?>

<div class="page page-admin gen-page-admin">
    <?php Lev::toolbarAdmin(0, $trash, $footerBtns); Lev::navbarAdmin($addurl, $srhtitle, $srhkey, 1, '', 0, $trash); ?>

    <div class="page-content gen-page-content">
        <?php echo AdminUsersController::headerHtm()?>

        <div class="card data-listb gen-list">
            <div class="data-xtable gen-tab">
                <table><thead><tr>
                        <th class="checkbox-cell tab-center wd30">
                            <input type="checkbox" name="opids" onclick="checkedToggle(this,'input[name=\'ids[]\']')">
                        </th>
                        <?php foreach ($showColumns as $k => $v): $isTime[$k] = stripos($k, 'time') !== false ?'numeric-cell':'';?>
                        <th class="column-<?=$k?> <?=$isTime[$k]?> <?=$v['thattr']?>">
                        <?php if ($v['order']):?>
                            <a href="<?=Lev::toCurrent(['orderFd'=>$k, 'asc'=>$asc])?>">
                                <?=$v['name']?>
                                <svg class="icon date inblk scale7"><use xlink:href="#fa-<?=$asc ? 'up' : 'down'?>"></use></svg>
                            </a>
                        <?php else:?>
                            <?=$v['name']?>
                        <?php endif;?>
                        </th>
                        <?php endforeach;?>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($pages['lists'])):foreach ($pages['lists'] as $_sv): $_pk = $_sv['id']?>
                    <?php $v = AdminUsersController::formatListsv($_sv, $pages['lists']);?>
                        <tr>
                            <td class="checkbox-cell tab-center ids-ckbox">
                                <input name="ids[]" type="checkbox" value="<?php echo $_pk?>" title="<?=$_pk?>" autocomplete="off">
                            </td>

                        <?php foreach ($showColumns as $k => $colv):$colname = $colv['name'];?>
                            <td class="column-<?=$k?> <?=$isTime[$k]?> ">
                                <?php include __DIR__ . '/column_one.php' ?>

                                <?php if (!empty($colv['merge'])): $_mrg = $colv['merge'];?>
                                <div class="flex-box" style="flex-wrap: wrap">
                                    <?php foreach ($_mrg as $k):$colname = $allColumns[$k];?>
                                    <?php $isTime[$k] = stripos($k, 'time') !== false ?'numeric-cell':'';?>
                                    <p class="date transl" title="<?=$colname?>">
                                        <?php include __DIR__ . '/column_one.php' ?>
                                    </p>
                                    <?php endforeach;?>
                                </div>
                                <?php endif;?>

                            </td>
                        <?php endforeach;?>
                            <td class="opt-btn-box1">
                                <div class="buttons-row scale6 transl avd-box1">
                                    <div class="wd60 nowrap flex-box" style="height: 20px;">
                                    <?php if ($addurl):?>
                                        <a class="button button-fill color-blue admin-edit-btnv" href="<?php echo Lev::toRoute([$addurl, 'opid'=>$_pk])?>">
                                            编辑
                                        </a>
                                    <?php endif;?>
                                    <?php if (AdminUsersController::$copyOneBtn):?>
                                        <a class="button button-fill color-yellow is_ajax_a admin-copy-btnv" href="<?=AdminUsersController::copyOneUrl($_pk)?>" title="复制这条数据">
                                            复制
                                        </a>
                                    <?php endif;?>
                                        <a class="button button-fill color-green  admin-view-btnv" target="_blank" href="<?=Lev::toCurrent(['viewid'=>$_pk])?>">
                                            查看
                                        </a>
                                    <?php if ($trash):?>
                                        <a class="button button-fill color-gray  admin-delete-btnv deleteOneBtn" opid="<?=$_pk?>">
                                            删除
                                        </a>
                                    <?php endif;?>
                                    </div>&nbsp;
                                </div>
                                <div class="buttons-row scale6 transl opt-box1">
                                    <?=AdminUsersController::optButtons($_sv)?>
                                </div>
                            </td>

                        </tr>
                    <?php endforeach; else:?>
                        <tr><td colspan="100" class="tab-center srhkey-tips-box">
                                <tips><?php echo $srhkey?'没有搜索到【'.$srhkey.'】相关数据':'没有数据'?></tips>
                            </td>
                        </tr>
                    <?php endif;?>
                    </tbody>
                </table>
                <div class="card-footer cf-c-box">
                    <div class="ft-btn-box"><?php echo AdminUsersController::cardFooterButtons()?></div>
                    <div class="paging-box"><?php echo $pages['pages']?></div>
                </div>
            </div>
        </div>

        <?php echo AdminUsersController::footerHtm()?>
    </div>

</div>
