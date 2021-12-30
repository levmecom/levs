<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-04-24 23:07
 *
 * 项目：upload  -  $  - BaseWidget.php
 *
 * 作者：liwei 
 */


namespace lev\base;

!defined('INLEV') && exit('Access Denied LEV');

class Widgetv implements WidgetvInterface
{

    /**
     * @param $show
     */
    public static function run($show = true) {

    }

    /**
     * @param bool $show
     * @param string $filename
     * @param array $param
     * @return string
     */
    public static function render($show = true, $filename = '', $param = []) {

        if ($show) {
            Viewv::renderFile($filename, $param);
        } else {
            return Viewv::renderPhpFile($filename, $param);
        }
        return '';
    }

}
