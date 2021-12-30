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
            <div class="card">
                <textarea name="urlstrings" class="resizable" placeholder="一行一条URL地址" style="width: calc(100% - 30px);margin: 10px;min-height: 200px;font-size: 12px;"></textarea>
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
