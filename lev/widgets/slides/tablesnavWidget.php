<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-18 23:51
 *
 * 项目：upload  -  $  - tablesnavWidget.php
 *
 * 作者：liwei 
 */

namespace lev\widgets\slides;

use Lev;
use lev\base\Widgetv;
use lev\widgets\inputs\inputsWidget;

!defined('INLEV') && exit('Access Denied LEV');


class tablesnavWidget extends Widgetv
{

    public static function run($navs = [], $input = [], $show = true)
    {
        if (empty($navs)) {
            return '';
        }

        if (in_array($input['inputtype'], ['tables', 'tabletr'])) {
            return self::showTablesData($input, $navs);
        }

        $tabFields = inputsWidget::getSelectItem($input);

        return parent::render($show, __DIR__ . '/views/tablesnav.php', [
            'navs' => $navs,
            'inputtype' => $input['inputtype'],
            'tabFields' => $tabFields,
        ]);
    }


    /**
     * 显示表格数据
     * @param $v
     * @param $values
     * @param int $child 是否为二级数据表格
     * @return string
     */
    public static function showTablesData($v, $values, $child = 0)
    {
        $fields = inputsWidget::tablesFields($v['settings']);
        if ($v['inputtype'] == 'tabletr') unset($fields['id']);
        $tr = '';
        $htm = '<div class="data-xtable table-fields-box"><table><tr>';
        if ($child) {
            $tr .= '<td class="tab-center">-</td>';
            $htm .= '<th style="text-align:center;width:40px">子级</th>';
        }
        foreach ($fields as $k => $r) {
            $_style = $r['width'] ? ' style="width:'.$r['width'].'px"' : '';
            $kcl = '';
            $ckK = strtolower(substr($k, -6));
            if ($ckK == 'status') {
                $kcl = ' tab-center wd30';
            }
            $htm.='<th class="kfield_'.$k.$kcl.'" '.$_style.'>'.$r['name'].'</th>';
        }
        !$values && $values = is_array($v['inputvalue']) ?: unserialize($v['inputvalue']);
        if ($values) {
            foreach ($values as $val) {
                $trcontent = static::tablesTr($fields, $val, $v);
                if ($child) {
                    $cls = $val['id'].'_'.$v['inputname'];
                    $tr.= '<tr class="navBox_'.$cls.'"><td style="text-align:center"><a class="tablesnavOpenChildBtn" data-cls="'.$cls.'">[+]</a></td>'.$trcontent.'</tr>';
                    $cldStr = '';
                    if (isset($val['cld__'])) {
                        $xv['inputname'] = 'cld__'.$val['id'].'_'.$v['inputname'].'__'.$v['inputname'];
                        foreach ($val['cld__'] as $cv) {
                            $cldStr .= '<tr><td style="text-align:center"><p class="date">&angrt;</p></td>' . static::tablesTr($fields, $cv, $xv) . '</tr>';
                        }
                    }
                }else {
                    $tr .= '<tr>'.$trcontent.'</tr>';
                }
            }
        }
        $htm.= $tr.'</table></div>';
        return $htm;
    }
    public static function tablesTr($fields, $val, $v = []) {
        $tr = '';
        foreach ($fields as $k => $r) {
            $valx = Lev::arrv($k, $val, '');
            $ckK = strtolower(substr($k, -6));
            if ($ckK == 'status') {
                $tr.= '<td><div class="item-title disabled">
                        <label class="label-switch scale7 color-blue">
                            <input type="checkbox" '.(!$valx ? 'checked' : '').'><div class="checkbox"></div>
                        </label>
                    </div></td>';
            }elseif ($options = Lev::arrv('options', $r, [])) {
                if (!is_array($options)) {
                    $tr .= '<td class="disabled">'. Lev::actionObjectMethod($r['type'], [$val['id']], 'field_'.$k, true).'</td>';
                }else {
                    $tr .= '<td>' . Lev::arrv($valx, $options) . '</td>';
                }
            }else {
                $tr .= '<td>' . $valx . '</td>';
            }
        }
        return $tr;
    }
}