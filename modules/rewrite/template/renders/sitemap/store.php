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
    <?php Lev::navbarAdmin(0,0,0,0,'',1);Lev::toolbarAdmin(1)?>

    <div class="page-content">
        <div class="page-content-inner">

            <form id="saveForm" action="" method="post">
                <div class="card-header card">
                    <div class="apiurls-box item-input">
                        <select name="api" style="font-size: 12px !important;max-width: 320px;margin:0">
                            <?php foreach ($apiurls as $v):?>
                                <option value="<?=$v['id']?>"><?=$v['name'],$v['link']?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="buttons-row">
                        <div class="flex-box scale8 transr">
                            <a class="button button-fill color-yellow" target="_blank" href="<?=Lev::toSetRoute(['classify'=>1])?>">接口设置</a>
                        </div>
                    </div>
                </div>
                <div class="card-header card">
                    <label class="flex-box ju-sa font12" style="align-items: center">
                        <input type="checkbox" name="opids" onclick="checkedToggle(this,'input[name=\'urls[]\']')">全选
                    </label>
                    <div class="item-input">
                        <select name="genxml" style="margin: 0">
                            <option value="0">API接口提交</option>
                            <option value="1">生成XML</option>
                        </select>
                    </div>
                </div>
            <div class="list-block card">
                <ul>
                    <?php foreach ($urls as $v):?>
                        <?php $_href = !empty($v['href']) ? $v['href'] :\modules\levstore\helpers\UrlStoreHelper::view($v['identifier'])?>
                    <li>
                        <label class="item-content item-link item-content-32">
                            <div class="item-media">
                                <input type="checkbox" name="urls[]" value="<?=$_href?>">
                            </div>
                            <div class="item-inner">
                                <div class="item-title">
                                    <div class="font12 scale9 transl">
                                    <?=$v['name'],$_href?>
                                    </div>
                                </div>
                                <div class="item-after date transr">
                                    <a href="<?=$_href?>" target="_blank" _bk="1">访问</a>
                                </div>
                            </div>
                        </label>
                    </li>
                    <?php endforeach;?>
                </ul>
            </div>

                <div class="card-footer">
                    <div class="flex-box">
                        <button type="submit" id="dosubmit" class="button-fill button wd100 dosaveFormBtn">
                            <svg class="icon" aria-hidden="true"><use xlink:href="#fa-save"></use></svg>
                            保 存
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
