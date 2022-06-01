<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-08-06 19:34
 *
 * 项目：rm  -  $  - tablesForm.php
 *
 * 作者：liwei 
 */

namespace lev\widgets\inputs;

use Lev;
use lev\base\Widgetv;

!defined('INLEV') && exit('Access Denied LEV');


class tablesForm extends Widgetv
{
    public static $addRowBtn = '新增一行';

    public static function run($values = [], $show = false) {
        return parent::render($show, __DIR__ . '/views/tables_form.php', [
            'inputs' => static::inputs(),
            'values' => $values,
            'pre' => static::getFormPre(),
        ]);
    }

    public static function inputInfo($name, $value, $v)
    {
        return static::tablesData($v);
    }

    public static function inputInfoSubnav($name, $value, $v)
    {
        return static::tablesData($v, 1);
    }

    public static function inputInfoOneTr($name, $value, $v)
    {
        if ($inputvalues = Lev::getSettings($v['inputvalue'])) {
            $inputvalue[0] = reset($inputvalues);
        }
        $inputvalue[0]['id'] = 0;
        $v['inputvalue'] = $inputvalue;
        return '<onetr>'.static::tablesData($v).'<onetr>';
    }

    public static function checkSelects($inputtype) {
        return in_array($inputtype, ['selects', 'selectscode', 'usetypescore']);
    }

    public static function checkUploads($inputtype) {
        return in_array($inputtype, ['uploadimg', 'uploadattach']);
    }

    public static function checkInputtype($inputtype) {
        return in_array($inputtype, ['tablesForm', 'tabletrForm', 'tableSubnavForm']);
    }

    public static function getFormPre() {
        return 'tablesFormv';
    }

    public static function inputName($name, $v) {
        if (isset($v['idk___']) && strpos($name, '[]') !== false) {
            $name = str_replace('[]', '[idk___'.$v['idk___'].']', $name);
        }
        return $name;
    }

    /**
     *
     * @param $tables
     * @param array $addtr
     * @param bool $unserialize
     * @return array
     */
    public static function dataFormat($tables, $addtr = [], $unserialize = false)
    {
        $childPre = 'cld__';
        $res = $result = [];
        if ($tables) {
            foreach ($tables as $field => $values) {
                $data = $values['id'];
                if ($data) {
                    $arr = [];
                    foreach ($values as $key => $fdv) {
                        foreach ($data as $k => $id) {
                            $id = intval($id);
                            $arr[$id][$key] = isset($fdv['idk___' . $id]) ? $fdv['idk___' . $id] : $fdv[$k];
                            $arr[$id]['id'] = $id;
                        }
                    }
                    //$arr && array_key_exists('order', reset($arr)) && Lev::arraySorts($arr, ['order']);
                    if ($arr) {
                        $onearr = reset($arr);
                        $orderKey = isset($onearr['order']) ? ['order', 'id'] : ['id'];
                        Lev::arraySorts($arr, $orderKey);
                    }
                    $res[$field] = $arr;
                    unset($addtr[$field]);
                }
            }
            foreach ($res as $field => $trs) {
                if (strpos($field, $childPre) !== 0) {
                    $fieldstr = '_' . $field . '__' . $field;
                    $_trs = [];
                    foreach ($trs as $idk => $tr) {
                        $idk = $tr['id'];
                        isset($res[$childPre . $idk . $fieldstr]) && $tr[$childPre] = $res[$childPre . $idk . $fieldstr];
                        $_trs[$idk] = $tr;
                    }
                    $result[$field] = $unserialize ? $_trs : serialize($_trs);
                }
            }
        }
        if ($addtr) foreach ($addtr as $field => $v) $result[$field] = '';
        return $result;
    }

    /**
     * @param $v
     * @param bool $child 是否为二级数据表格
     * @return string
     */
    public static function tablesData($v, $child = 0, $inputs = null)
    {
        $inputs === null &&
        $inputs = static::tablesInputs($v['settings']);
        $headers = static::tablesHeader($inputs, $v, $child);
        $htm = $headers[0];
        $tr  = '';
        $values = is_array($v['inputvalue']) ? $v['inputvalue'] : unserialize($v['inputvalue']);
        if ($values) {
            foreach ($values as $idk => $val) {
                $idk = $val['id'];
                $trcontent = static::tablesTr($inputs, $val, $v, $idk);
                if ($child) {
                    $count = isset($val['cld__']) ? count($val['cld__']) : 0;
                    $cls = $idk.'_'.$v['inputname'];
                    $tr.= '<tr class="navBox_'.$cls.'" data-idk="'.$idk.'"><td style="text-align:center"><a class="tablesnavOpenChildBtn" data-cls="'.$cls.'">['.$count.']</a></td>'.$trcontent.'</tr>';
                    $cldStr = '';
                    if (isset($val['cld__'])) {
                        $xv['inputname'] = 'cld__'.$idk.'_'.$v['inputname'].'__'.$v['inputname'];
                        foreach ($val['cld__'] as $cv) {
                            $idkc = $cv['id'];
                            $cldStr .= '<tr class="trid-c'.$idkc.'" data-idk="'.$idkc.'" data-pidk="'.$idk.'"><td style="text-align:center"><p class="date">&angrt;</p></td>' . static::tablesTr($inputs, $cv, $xv, $idkc, 'c'.$idkc) . '</tr>';
                        }
                    }
                    $tr.= '<tbody class="hiddenx childBox_'.$cls.' Navsx">'.$cldStr.'</tbody><tbody class="hiddenx childBox_'.$cls.'">
                        <tr><td class="tab-center"><div class="date">&angrt;</div></td><td colspan="100"><a class="tablesnavAddChildBtn" data-cls="'.$cls.'">新增子级</a></td></tr>
                    </tbody>';
                }else {
                    $tr .= '<tr class="trid- trid-'.$idk.'" data-idk="'.$idk.'">'.$trcontent.'</tr>';
                }
            }
        }
        $htm.= $headers[1].$tr.'</table></tabbox>';
        return $htm;
    }
    public static function tablesHeader($inputs, $v, $child = 0)
    {
        $formPre = static::getFormPre();
        $trForm = '<tbody class="my-add-tr-form hiddenx"><tr class="myAddTrx">';
        $header = '<tabbox class="data-xtable table-fields-box tables-formv inputname-'.$v['inputname'].'"><table><tr>';
        if ($child) {
            $trForm .= '<td class="tab-center">-</td>';
            $header .= '<th style="text-align:center;width:40px">子级</th>';
        }
        foreach ($inputs as $k => $rv) {
            $r = Lev::decodeHtml($rv);
            $r['_parentInfo'] = $v;
            $kcl = ' inputtype_'.$r['inputtype'];
            $r['inputtype'] == 'radio' && $kcl .= ' tab-center wd30 inputtype_'.$r['inputtype'];
            $header.='<th title="'.$r['placeholder'].'" class="kfield_'.$k.$kcl.'" '.($r['width'] ? ' style="width:'.$r['width'].'px"' : '').'>'.$r['title'].'<tips>'.$r['placeholder'].'</tips></th>';
            $trForm .= '<td class="fd-'.$r['inputname'].'"><inpt>'
                .inputsWidget::run($r['inputtype'], $formPre.'__addtr['.$v['inputname'].']['.$r['inputname'].'][]', $rv['inputvalue'], $r)
                .'</inpt></td>';
        }
        $addid = 'addTabTrId-'.$v['inputname'];
        $header.= '<th class="del-td" style="text-align:center;width:40px">删除</th>';
        $header.= '</tr>';
        $trForm.= '<td class="tab-center"><button type="button" class="addTabTr" title="'.static::$addRowBtn.'" id="'.$addid.'" data-input="'.$v['inputname'].'">[+]</button></td>';
        $trForm.= '</tr></tbody>';
        $trForm.= '<tbody class="my-add-tr-btn newTabTrBox-'.$v['inputname'].'"><tr><td class="wd80" colspan="100" style="padding:0">
<label clickfor="'.$addid.'" class="wd80 button-fill button scale7 inblk addRowBtn wdmin">'.static::$addRowBtn.'</label>
<label class="wd60 button-fill button color-black scale7 bigvBtn inblk">展开</label>
</td></tr></tbody>';
        return [$header, $trForm];
    }

    public static function tablesTr($inputs, $val, $v, $idk, $childId = 0) {
        $tr = '';
        $formPre = static::getFormPre();
        foreach ($inputs as $k => $r) {
            $valx = Lev::arrv($r['inputname'], $val, $r['inputvalue']);

            $r['idk___']         = $idk;
            $r['_parentInfo']    = $v;
            $r['_notUploadJs']   = true;
            $r['_childId']       = $childId;
            $r['_trInfo']        = $val;

            $tr .= '<td class="fd-'.$r['inputname'].'"><inpt>'
                .inputsWidget::run($r['inputtype'], $formPre.'['.$v['inputname'].']['.$r['inputname'].'][]', $valx, $r).'</inpt></td>';
        }
        $cls = 'idk_'.($childId ?: $idk).'_'.$v['inputname'];
        return $tr.'<td class="tab-center del-td"><a class="date delTr inblk" data-cls="'.$cls.'"><absxg>x</absxg></a></td>';
    }

    public static function tablesInputs($settings = null, $tablesForm = null) {
        $tablesForm === null &&
        $tablesForm = Lev::getSettings(Lev::getSettings($settings, 'tablesForm'));
        if ($tablesForm) {
            foreach ($tablesForm as $v) {
                $v['inputname'] = $v['inputtype'] == 'order' ? 'order' : ($v['inputname'] ?: 'tb_'.$v['id']);
                $inputs[$v['inputname']] = $v;
            }
        }else {
            static::$addRowBtn = '新增一个字段';
        }
        empty($inputs) && $inputs = static::inputs();
        isset($inputs['id']) || $inputs['id'] = static::inputs()['id'];
        $inputs['id']['placeholder'] .= static::inputIdPlaceholder();
        //unset($inputs['settings'], $inputs['inputvalue']);
        return $inputs;
    }
    public static function inputIdPlaceholder() {
        return "\n1.必填且不重复，重复值将被覆盖。\n2.【注意】必须是整数，非整数视为0\n3.非必要不要轻易修改\n4.修改id将重置多项选择值";
    }

    public static function setinputtype() {
        $inputtypes = inputsWidget::inputtype();
        unset($inputtypes['tablesForm'], $inputtypes['tables'], $inputtypes['tabletr'], $inputtypes['tablesnav'], $inputtypes['slides']);
        unset($inputtypes['navs'], $inputtypes['wangeditor'], $inputtypes['usescore'], $inputtypes['tabletrForm'], $inputtypes['tableSubnavForm']);
        return $inputtypes;
    }
    public static function inputs()
    {
        return array (
            'id' =>
                array (
                    'classify' => '',
                    'title' => 'ID',
                    'placeholder' => '',
                    'inputname' => 'id',
                    'inputtype' => 'number',
                    'inputvalue' => '',
                    'settings' => '',
                    'displayorder' => '7',
                    'status' => '0',
                    'width' => '35',
                ),
            'order' =>
                array (
                    'classify' => '',
                    'title' => '排序',
                    'placeholder' => '',
                    'inputname' => 'order',
                    'inputtype' => 'number',
                    'inputvalue' => '',
                    'settings' => '',
                    'displayorder' => '7',
                    'status' => '0',
                    'width' => '35',
                ),
            'title' =>
                array (
                    'title' => '字段标题',
                    'placeholder' => '',
                    'inputname' => 'title',
                    'inputtype' => 'text',
                    'inputvalue' => '',
                    'settings' => '',
                    'displayorder' => '1',
                    'status' => '0',
                    'width' => '130',
                ),
            'inputname' =>
                array (
                    'classify' => '',
                    'title' => '字段名[name]',
                    'placeholder' => "字段名含 <b>html</b> ，将允许用户输入html元素
\n1.order排序字段，设置后自动排序；
\n2.id不重复字段且为整数，重复值会被覆盖，未设置自动自增",
                    'inputname' => 'inputname',
                    'inputtype' => 'text',
                    'inputvalue' => '',
                    'settings' => '',
                    'displayorder' => '2',
                    'status' => '0',
                    'width' => '130',
                ),
            'inputtype' =>
                array (
                    'classify' => '',
                    'title' => '字段类型[type]',
                    'placeholder' => '',
                    'inputname' => 'inputtype',
                    'inputtype' => 'selectcode',
                    'inputvalue' => 'text',
                    'settings' => 'lev\widgets\inputs\tablesForm',
                    'displayorder' => '3',
                    'status' => '0',
                    'width' => '100',
                ),
            'width' =>
                array (
                    'classify' => '',
                    'title' => '输入框宽度',
                    'placeholder' => '',
                    'inputname' => 'width',
                    'inputtype' => 'text',
                    'inputvalue' => '',
                    'settings' => '',
                    'displayorder' => '1',
                    'status' => '0',
                    'width' => '80',
                ),
            'settings' =>
                array (
                    'classify' => '',
                    'title' => '扩展设置',
                    'placeholder' => '',
                    'inputname' => 'settings',
                    'inputtype' => 'textarea',
                    'inputvalue' => '',
                    'settings' => '',
                    'displayorder' => '4',
                    'status' => '0',
                    'width' => '0',
                ),
            'placeholder' =>
                array (
                    'classify' => '',
                    'title' => '字段提示语[placeholder]',
                    'placeholder' => '简单明了的提示，有助于使用者更好的理解他的用途',
                    'inputname' => 'placeholder',
                    'inputtype' => 'textarea',
                    'inputvalue' => '',
                    'settings' => '',
                    'displayorder' => '5',
                    'status' => '0',
                    'width' => '0',
                ),
            'inputvalue' =>
                array (
                    'classify' => '',
                    'title' => '字段默认值[value]',
                    'placeholder' => '默认值',
                    'inputname' => 'inputvalue',
                    'inputtype' => 'textarea',
                    'inputvalue' => '',
                    'settings' => '',
                    'displayorder' => '6',
                    'status' => '0',
                    'width' => '0',
                ),
        );
    }

}