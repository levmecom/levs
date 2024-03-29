<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-21 13:36
 *
 * 项目：upload  -  $  - editorWidget.php
 *
 * 作者：liwei 
 */

namespace lev\widgets\editors;

use lev\base\Widgetv;

!defined('INLEV') && exit('Access Denied LEV');


class editorWidget extends Widgetv
{

    public static function run($show = true)
    {
        parent::run($show); // TODO: Change the autogenerated stub
    }

    public static function wangEditor($inputname, $inputvalue, $input = [], $editorHeight = 300, $show = true) {
        return parent::render($show, __DIR__ . '/views/wangeditor.php', [
            'inputname' => $inputname,
            'inputvalue' => $inputvalue,
            'input' => $input,
            'editorHeight' => $editorHeight,
        ]);
    }
}