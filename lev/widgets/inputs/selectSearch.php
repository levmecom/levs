<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-08-10 13:02
 *
 * 项目：rm  -  $  - selectSearch.php
 *
 * 作者：liwei 
 */

namespace lev\widgets\inputs;

use Lev;
use lev\base\Widgetv;

!defined('INLEV') && exit('Access Denied LEV');


class selectSearch extends Widgetv
{

    /**
     * 用代码或用户设置控制选项。eg $item = ['key'=>'value'] 一维数组
     * 支持二维分组数组。eg $item = ['分组一'=> ['key'=>'value'], '分组二'=> ['key'=>'value']] 二维数组
     * @param $name
     * @param $value
     * @param $v
     * @return string
     */
    public static function input($name, $value, $v) {
        $id = !empty($v['_childId']) ? $v['_childId'] : Lev::arrv('idk___', $v, $v['id']);
        $input = '<a class="item-link smart-select" 
        data-open-in="popup" 
        data-searchbar="true" 
        data-virtual-list="true" 
        data-searchbar-cancel="取消" 
        data-searchbar-placeholder="搜索 '.$v['title'].' 或 回车添加 自定义选项" 
        data-page-title="'.$v['title'].' <tips>可选项</tips> <hiddenx><na>'.$name.'</na></hiddenx> <hiddenx><idk>'.$id.'</idk></hiddenx>">
        <select class="form-control hiddenx" name="'.$name.'" autocomplete="off">';
        $item = static::getSelectItem($v, true);
        //$value && !isset($item[$value]) && $item[$value] = $value;
        foreach ($item as $val => $_name) {
            if (is_array($_name)) {
                $input .= '<optgroup label="'.$val.'">';
                foreach ($_name as $valc => $_namec) {
                    $ckd = $valc === $value || (is_numeric($value) && $valc == $value);
                    $input .= '<option value="' . $valc . '" ' . ($ckd ? ($slt = ' selected') : '') . '>' . $_namec . '</option>';
                }
                $input .= '</optgroup>';
            }else {
                $ckd = $val === $value || (is_numeric($value) && $val == $value);
                $input .= '<option value="' . $val . '" ' . ($ckd ? ($slt = ' selected') : '') . '>' . $_name . '</option>';
            }
        }
        if ($value && !isset($slt)) {
            $input .= '<optgroup label="自定义的选项"><option value="' . $value . '" selected>' . $value . '</option></optgroup>';
        }
        return $input . '</select><div class="item-content"><div class="item-inner"><div class="item-title"></div></div></div></a>';
    }


    /**
     * 同时支持自定义选项和调用自定义类
     * @param $inputSetInfo
     * @param bool $code
     * @return array|bool|mixed
     */
    public static function getSelectItem($inputSetInfo, $code = false) {
        if ($code || substr($inputSetInfo['inputtype'], -4) == 'code') {
            $item = Lev::actionObjectMethod(trim($inputSetInfo['settings']), [], 'set' . $inputSetInfo['inputname']);
            if ($item !== false) {
                return $item;
            }
        }
        $item  = [];
        $_item = explode("\n", $inputSetInfo['settings']);
        foreach ($_item as $k => $r) {
            if (trim($r)) {
                $one         = explode('=', $r);
                $val         = trim($one[0]);
                $_name       = trim(Lev::arrv(1, $one));
                $item[$val]  = $_name;
            }
        }
        return $item;
    }
}