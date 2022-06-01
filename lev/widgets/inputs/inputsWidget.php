<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-13 12:49
 *
 * 项目：upload  -  $  - inputsWidget.php
 *
 * 作者：liwei 
 */


namespace lev\widgets\inputs;

!defined('INLEV') && exit('Access Denied LEV');

use Lev;
use lev\base\Assetsv;
use lev\base\Widgetv;
use lev\helpers\cacheFileHelpers;
use lev\helpers\ScoreHelper;
use lev\helpers\SettingsHelper;
use lev\widgets\editors\editorWidget;
use lev\widgets\slides\slidesWidget;
use lev\widgets\slides\tablesnavWidget;
use lev\widgets\uploads\uploadsWidget;
use modules\levfm\widgets\inputs\inputsFormWidget;

class inputsWidget extends Widgetv
{

    public static function inputtype() {

        $arr = [
            'text'           => '字符串(text)',
            'number'         => '数字(number)',
            'textarea'       => '多行文本(textarea)',
            'radio'          => '开关(radio)',
            'select'         => '单项选择(select)',
            'selects'        => '多项选择(selects)',//json
            'selectcode'     => '单项选择[代码控制](selectcode)',
            'selectscode'    => '多项选择[代码控制](selectscode)',//json
            'selectSearch'   => '可搜索单项选择(selectSearch)', // 双重 可设置方法类名 可自行输入设置
            'date'           => '日期(date)',
            'time'           => '时间(time)',
            'tabletr'        => '单行数据(tabletr)', //serialize
            'tables'         => '数据表格(tables)', //serialize
            'navs'           => '一级导航(navs)', //serialize
            'tablesnav'      => '二级导航(tablesnav)', //serialize
            'tablesForm'     => '子表单(tablesForm)', //serialize
            'tabletrForm'    => '单行子表单(tabletrForm)', //serialize
            'tableSubnavForm'=> '二级子表单(tableSubnavForm)', //serialize
            'slides'         => '幻灯片(slides)', //serialize
            'buttons'        => '功能按钮(buttons)',//空值
            'uploadimg'      => '图片上传(uploadimg)',//string
            'uploadattach'   => '指定扩展上传(uploadattach)',//string
            'wangeditor'     => '富文本编辑器(wangeditor)',//html
            //'usescore'       => '消耗奖励积分(usescore)',//空值
            'usetypescore'   => '积分类型(usetypescore)',//json
            'usetypescoreyy' => '积分类型yy(usetypescoreyy)',//json
        ];
        Lev::classExists('modules\levfm\widgets\inputs\inputsFormWidget') && $arr += inputsFormWidget::myInputtype();
        return $arr;
    }
    public static function setinputtype() {
        return static::inputtype();
    }

    public static function run($type = '', $name = '', $value = '', $v = [], $show = false) {
        $method = $type.'Type';
        return method_exists(__CLASS__, $method) ? static::$method($name, $value, $v)
            : (Lev::classExists('modules\levfm\widgets\inputs\inputsFormWidget')
                ? inputsFormWidget::input($type, $name, $value, $v)
                : '<b class=red>未知input类型 &raquo; '.$type.'</b> 需要安装【levfm】表单模块'
            );
    }

    public static function form($inputs, $values = [], $pre = '', $show = false) {

        return parent::render($show, __DIR__ . '/views/form.php', [
            'inputs' => $inputs,
            'values' => $values,
            'pre' => $pre,
        ]);
    }

    public static function settingsForm($inputs, $show = false) {
        return parent::render($show, __DIR__ . '/views/settings_form.php', [
            'inputs' => $inputs,
        ]);
    }

    public static function getPreInputname($inputname, $pre = 'settings') {
        return $pre ? $pre.'['.$inputname.']' : $inputname;
    }

    public static function setDefaultSettings($inputtype, $settings) {
        if (!$settings) {
            if (tablesForm::checkInputtype($inputtype)) {
                $settings = Lev::setSettings(['tablesForm' => Lev::setSettings(array(
                    8 => array(
                        'id' => 8,
                        'order' => '0',
                        'title' => 'ID',
                        'inputname' => 'id',
                        'inputtype' => 'number',
                        'width' => '40',
                        'settings' => '',
                        'placeholder' => '',
                        'inputvalue' => '',
                    ),
                    1 => array(
                        'id' => 1,
                        'order' => '1',
                        'title' => '排序',
                        'inputname' => 'order',
                        'inputtype' => 'number',
                        'width' => '40',
                        'settings' => '',
                        'placeholder' => '',
                        'inputvalue' => '',
                    ),
                    5 => array(
                        'id' => 5,
                        'order' => '2',
                        'title' => '开关',
                        'inputname' => 'status',
                        'inputtype' => 'radio',
                        'width' => '40',
                        'settings' => '',
                        'placeholder' => '',
                        'inputvalue' => '',
                    ),
                    3 => array(
                        'id' => 3,
                        'order' => '3',
                        'title' => '显示类型',
                        'inputname' => 'ShowType',
                        'inputtype' => 'select',
                        'width' => '100',
                        'settings' => '0=图标
1=文字
2=图标和文字',
                        'placeholder' => '',
                        'inputvalue' => '',
                    ),
                    4 => array(
                        'id' => 4,
                        'order' => '4',
                        'title' => '打开方式',
                        'inputname' => 'target',
                        'inputtype' => 'selectcode',
                        'width' => '100',
                        'settings' => 'lev\\helpers\\SettingsHelper',
                        'placeholder' => '',
                        'inputvalue' => '',
                    ),
                    2 => array(
                        'id' => 2,
                        'order' => '5',
                        'title' => '导航名称',
                        'inputname' => 'name',
                        'inputtype' => 'text',
                        'width' => '100',
                        'settings' => '',
                        'placeholder' => '',
                        'inputvalue' => '',
                    ),
                    6 => array(
                        'id' => 6,
                        'order' => '6',
                        'title' => '链接地址',
                        'inputname' => 'link',
                        'inputtype' => 'text',
                        'width' => '',
                        'settings' => '',
                        'placeholder' => '',
                        'inputvalue' => '',
                    ),
                    7 => array(
                        'id' => 7,
                        'order' => '7',
                        'title' => '导航图标',
                        'inputname' => 'logoupload',
                        'inputtype' => 'uploadimg',
                        'width' => '',
                        'settings' => '',
                        'placeholder' => '#号开头调用icon图标、也可是@别名和url址址',
                        'inputvalue' => '',
                    ),
                    9 => array(
                        'id' => 9,
                        'order' => '9',
                        'title' => '按钮颜色',
                        'inputname' => 'bgcolor',
                        'inputtype' => 'select',
                        'width' => '77',
                        'settings' => 'yellow = 金黄色
red    = 红色
blue   = 蓝色
green  = 绿色
gray   = 灰色
black  = 黑色
white  = 白色',
                        'placeholder' => '',
                        'inputvalue' => '',
                    ),
                ))]);
            }else if (in_array($inputtype, ['tablesnav', 'tables', 'navs'])) {//二级导航类型，默认值
                $settings = 'order=排序===60
name=名称===100
target=打开方式==lev\helpers\SettingsHelper=100
status=开关
homeStatus=首页===50
link=链接地址
logo=应用图标=#号开头调用icon图标、也可是@别名和url址址；';
            } elseif ($inputtype == 'slides') {//二级导航类型，默认值
                $settings = 'order=排序===60
name=名称===100
target=打开方式==lev\helpers\SettingsHelper=100
status=开关
link=图片链接
upload=图片路径';
            } elseif ($inputtype == 'tabletr') {//单行数据，默认值
                $settings = 'name=名称===100
target=打开方式==lev\helpers\SettingsHelper=100
status=开关
link=图片链接
upload=图片路径';
            } elseif ($inputtype == 'buttons') {
                $settings = '首页=={homeUrl}==red==_blank';
            }
        }
        return $settings;
    }

    public static function getTablesIntID($fields) {
        $isIntFields = [];
        foreach ($fields as $v) {
            if ($v['settings']) {
                if (tablesForm::checkInputtype($v['inputtype'])) {
                    $arr = tablesForm::tablesInputs($v['settings']);
                }else {
                    $arr = static::tablesFields($v['settings']);
                    !empty($arr['id']['tip']) && stripos($arr['id']['tip'], 'int') !== false && $isIntFields[$v['inputname']] = 1;
                }
            }
        }
        return $isIntFields;
    }

    public static function getModSettings($iden, $classify = '', $key = '', $order = []) {
        $where = ['moduleidentifier'=>$iden];
        $classify && $where['classify'] = $classify;
        $where['status'] = 0;
        return SettingsHelper::findAll($where, $key, $order);
    }

    public static function saveSettings($identifier, $classify = '') {
        if (!$identifier) {
            return Lev::responseMsg(-1, '模块标识符不能为空');
        }
        $vars = static::getModSettings($identifier, $classify);
        if ($vars) {
            $counts = 0;
            $posts = (array)Lev::POSTv('settings');
//            $tables = Lev::POSTv('tables');
//            if ($tables) {
//                $posts += static::tablesDataFormat($tables, static::getTablesIntID($vars));
//            }//exit;
            $posts = static::formatSaveInputsData($posts, $vars);
            foreach ($vars as $v) {
                !isset($posts[$v['inputname']]) && $posts[$v['inputname']] = '';
                if (is_array($posts[$v['inputname']])) {
                    $posts[$v['inputname']] = json_encode($posts[$v['inputname']]);
                }
                $v['inputvalue'] != $posts[$v['inputname']] &&
                $counts += SettingsHelper::update(['inputvalue' => $posts[$v['inputname']]], ['id' => $v['id']]) ? 1 : 0;
            }
            static::setCaches($identifier);
            return Lev::responseMsg(1, '成功更新 '.$counts.' 项配置');
        }
        return Lev::responseMsg(-2, '没有查询到设置字段');
    }

    /**
     * @param $tables
     * @return array
     */
    public static function tablesDataFormat($tables, $intID = [], $primary = 'id')
    {
        $childPre = 'cld__';
        $res = $result = [];
        $setFields = array_keys($tables);
        foreach ($setFields as $field) {
            $data = $tables[$field]['id'];
            $keys = array_keys($tables[$field]);
            $arr = [];
            foreach ($keys as $key) {
                foreach ($data as $k => $v) {
                    if (isset($intID[$field])) {
                        $v = intval($v);
                        $v <1 && $v = false;
                    }
                    if (!$v && !is_numeric($v)) {//continue 慢
                    }else {
                        $arr[$data[$k]][$key] = $tables[$field][$key][$k];//$tables[$field]['id']
                    }
                }
            }
            $arr && (array_key_exists('order', reset($arr)) ? Lev::arraySorts($arr, ['order', 'id']) : Lev::arraySorts($arr, ['id']));
            $res[$field] = $arr;
        }
        foreach ($res as $field => $trs) {
            if (strpos($field, $childPre) !== 0) {
                $fieldstr = '_' . $field . '__' . $field;
                $_trs = [];
                foreach ($trs as $k => $tr) {
                    isset($res[$childPre . $tr['id'].$fieldstr]) && $tr['cld__'] = $res[$childPre . $tr['id'].$fieldstr];
                    $_trs[$tr['id']] = $tr;
                }
                $result[$field] = serialize($_trs);
            }
        }//print_r($result);exit;
        return $result;
    }

    public static function formatInputvalue($v, $fd, $iden) {
        if (is_string($v[$fd]) && strpos($v[$fd], '@appassets/') === 0) {
            $v[$fd] = str_replace('@appassets/', Assetsv::getAppassets($iden) . '/', $v[$fd]);
        }else {
            static::checkUploadField($fd) && $v[$fd] = Lev::uploadRealSrc($v[$fd]);//upload 后缀
        }
        if (static::checkLogoField($fd)) {//logo前缀
            $v['=' . $fd] = SettingsHelper::navIcon($v['id'], $v[$fd]);
        } elseif (static::checkLinkField($fd)) {//link前缀
//            $pm = Lev::getUrlParam($v[$fd]);
//            !isset($pm['id']) && strpos($v[$fd], Lev::$app['homeFile']) !== false && $pm['id'] = $iden;
//            $v['=' . $fd] = Lev::toRoute($pm);
            $v['=' . $fd] = static::linkFormat($v[$fd], $iden);
        }
        return $v;
    }

    public static function linkFormat($link, $iden) {
        if (strpos($link, 'http://') === 0 || strpos($link, 'https://') === 0 || strpos($link, '//') === 0) {
            return $link;
        }
        $pm = Lev::getUrlParam($link);
        !isset($pm['id']) && strpos($link, Lev::$app['homeFile']) !== false && $pm['id'] = $iden;
        return Lev::toRoute($pm);
    }

    public static function setCaches($iden = null) {
        $where = ['status'=>0];
        $iden && $where['moduleidentifier'] = $iden;
        $data = SettingsHelper::findAll($where);
        $items = [];
        if ($data) {
            foreach ($data as $v) {
                //$items[$v['moduleidentifier']][$v['inputname']] = $v['inputvalue'];
                if ($v['inputtype'] == 'wangeditor') {
                    $v['inputvalue'] = Lev::decodeHtml($v['inputvalue']);
                }
                if (tablesForm::checkInputtype($v['inputtype'])) {
                    $v['inputvalue'] = Lev::getSettings($v['inputvalue']);
                }
                if (static::checkTablesField($v['inputtype'])) {
                    $v['inputvalue'] = static::tablesDataShowFormat($v['inputvalue'], $v['moduleidentifier'], true);
                    if ($v['inputtype'] == 'tabletr') {
                        $v['inputvalue'] = $v['inputvalue'] ? reset($v['inputvalue']) : [];
                    }
                }elseif (static::checkJsonField($v['inputtype'])) {
                    $v['inputvalue'] = json_decode($v['inputvalue']);
                }else {
                    $format = static::formatInputvalue([$v['inputname']=>$v['inputvalue']], $v['inputname'], $v['moduleidentifier']);
                    isset($format['='.$v['inputname']]) &&
                    $items[$v['moduleidentifier']]['='.$v['inputname']] = $format['='.$v['inputname']];
                    $v['inputvalue'] = $format[$v['inputname']];
                }
                $items[$v['moduleidentifier']][$v['inputname']] = $v['inputvalue'];
            }
        }
        self::createSettingsFile($items);
    }

    public static function createSettingsFile($params) {
        $size = null;
        if (!empty($params) && is_array($params)) {
            $inLev = '!defined(\'INLEV\') && exit(\'Access Denied Levset\');'."\n";
            $paramsFile = Lev::getAlias('@settings');
            cacheFileHelpers::mkdirv($paramsFile);
            $size = file_put_contents($paramsFile.'/settings.php', '<?php '.$inLev.' return ' . var_export($params, true) . ';');
            if ($size) {
                foreach ($params as $iden => $settings) {
                    Lev::$app['version'] < 'Lev3.3.1.2105' || Lev::setAppSettings($iden, $settings);
                    file_put_contents($paramsFile . '/' . $iden . '.php', '<?php '.$inLev.' return ' . var_export($settings, true) . ';');
                }
            }else {
                throw new \Exception('文件写入失败，请确定目录可写：'.$paramsFile);
            }
        }
        return $size;
    }

    public static function textType($name, $value) {
        return '<input type="text" class="form-control" name="'.$name.'" value="'.$value.'"/>';
    }

    public static function numberType($name, $value) {
        return '<input type="text" class="form-control" name="'.$name.'" value="'.$value.'"/>';
    }

    public static function dateType($name, $value) {
        return '<input type="date" class="form-control" name="'.$name.'" value="'.$value.'"/>';
    }

    public static function timeType($name, $value) {
        return '<input type="time" class="form-control" name="'.$name.'" value="'.$value.'"/>';
    }

    public static function textareaType($name, $value) {
        return '<textarea class="form-control resizable" name="'.$name.'">'.$value.'</textarea>';
    }

    public static function radioType($name, $value, $v = []) {
        return '<div class="switch-radiob setToggleValue scale9 transl '.(empty($v['_disabled'])?'':'disabled').'">
                <label class="label-switch color-red">
                    <input type="checkbox" '.(!$value ? 'checked' : '').'>
                    <div class="checkbox"></div>
                </label>
                <input type="hidden" class="form-control" name="'.$name.'" value="'.$value.'">
                </div>';
    }

    public static function selectType($name, $value, $v) {
        $input = '<select class="form-control" name="'.$name.'">';
        $_item = explode("\n", $v['settings']) ?: [];
        foreach ($_item as $k => $r) {
            if (trim($r)) {
                $one = explode('=', $r);
                $val = trim($one[0]);
                $_name = trim(Lev::arrv(1, $one));
                $input .= '<option value="' . $val . '"'.($val == $value ? ' selected' : '').'>' . $_name . '</option>';
            }
        }
        return $input . '</select>';
    }

    public static function selectsType($name, $value, $v) {
        $name = tablesForm::inputName($name, $v);

        $input = static::bigBtn($name);
        $input.= '<div class="checkbox-list bigObjx">';

        !is_array($value) && $value = json_decode($value, true);
        $values = $value ? array_flip($value) : [];
        $_item  = explode("\n", $v['settings']);
        foreach ($_item as $k => $r) {
            if (trim($r)) {
                $one    = explode('=', $r);
                $val    = trim($one[0]);
                $_name  = trim(Lev::arrv(1, $one));
                $ckd    = isset($values[$val]) ? ' checked' : '';
                $input .= '<label><input type="checkbox" name="'.$name.'[]" value="'.$val.'"'.$ckd.'>'.$_name.'</label>';
            }
        }
        return $input .'</div>';
    }

    public static function selectSearchType($name, $value, $v) {
        return selectSearch::input($name, $value, $v);
    }

    public static function tablesFormType($name, $value, $v) {
        return tablesForm::inputInfo($name, $value, $v);
    }

    public static function tabletrFormType($name, $value, $v) {
        return tablesForm::inputInfoOneTr($name, $value, $v);
    }

    public static function tableSubnavFormType($name, $value, $v) {
        return tablesForm::inputInfoSubnav($name, $value, $v);
    }

    /**
     * 用代码控制选项。eg $item = ['key'=>'value'] 一维数组
     * @param $name
     * @param $value
     * @param $v
     * @return string
     */
    public static function selectcodeType($name, $value, $v) {
        $input = '<select class="form-control" name="'.$name.'">';
        $item = Lev::actionObjectMethod(trim($v['settings']), [$v], 'set'.($v['inputname']?:$name)) ?: [];
        foreach ($item as $val => $_name) {
            $input .= '<option value="' . $val . '"'.($val == $value ? ' selected' : '').'>' . $_name . '</option>';
        }
        return $input . '</select>';
    }

    public static function bigBtn($name) {
        return '<div class="flex-box big-objx-m">
                    <a class="setBigBox date flex"><absxk>放大</absxk></a>
                    <label class="checkbox date flex">
                        <input type="checkbox" onclick="checkedToggle(this,  jQuery(this).parents(\'inpt\').find(\'input[name=\\\''.$name.'[]\\\']\'))" /><span>全选</span>
                    </label>
               </div>';
    }

    /**
     * 用代码控制选项。
     * @param $name
     * @param $value
     * @param $v
     * @return string
     */
    public static function selectscodeType($name, $value, $v) {
        $name  = tablesForm::inputName($name, $v);
        $input = static::bigBtn($name);
        $input.= '<div class="checkbox-list bigObjx">';
        !is_array($value) && $value = json_decode($value, true);
        $values = $value ? array_flip($value) : [];
        $item   = Lev::actionObjectMethod(trim($v['settings']), [$v], 'set'.($v['inputname']?:$name)) ?: [];
        foreach ($item as $val => $_name) {
            $ckd    = isset($values[$val]) ? ' checked' : '';
            $input .= '<label><input type="checkbox" name="'.$name.'[]" value="'.$val.'"'.$ckd.'>'.$_name.'</label>';
        }
        return $input . '</div>';
    }

    public static function buttonsType($name, $value, $v) {
        $_htm = '';
        $htm = static::replaceKeyword($v['settings']);
        if (strpos($htm, '==') !==false) {
            $ep = explode("\n", $htm);
            foreach ($ep as $xv) {
                $one = explode('==', $xv);
                if ($one[0] = trim($one[0])) {
                    $color = trim(Lev::arrv(2, $one, ''));
                    $color = $color ? ' color-'.$color.' ' : '';
                    $target = trim(Lev::arrv(3, $one, ''));
                    $target = !$target ? ' openziframescreen" force=1>' : ' external" _bk=1 target="'.$target.'">';
                    $href = trim($one[1]);
                    //isset($v['idk___']) &&
                    //$href = Lev::toRoute([$href, 'optid'=>$v['idk___'], '_trInfo'=>Lev::base64_encode_url(json_encode($v['_trInfo']))]);
                    $_htm .= '<a href="'.$href.'" class="button button-fill'.$color.$target.$one[0].'</a>';
                }
            }
        }
        return '<div class="settings-buttons-box">'.($_htm ?: $htm).'</div>';
    }

    public static function usescoreType($name, $value, $v) {
        $settings = Lev::explodev($v['settings'], '=', 'floatval', false);
        if ($settings[0] && $settings[1]) {
            return $settings[1] <0 ?
                '成功发布信息需支付：<b class=red>'.abs($settings[1]).ScoreHelper::scorename($settings[0]).'</b>' :
                '成功发布信息奖励：<b class=red>'.$settings[1].ScoreHelper::scorename($settings[0]).'</b>';
        }
        return '免费发布信息';
    }

    public static function usetypescoreyyType($name, $value, $v) {
        !is_array($value) && $value = json_decode($value, true);

        $scoretypes = ScoreHelper::scoretypesyy();
        $name       = tablesForm::inputName($name, $v);
        $box        = '<div class="flex-box usetypescoreb">';
        $input      = $box.'<input type="text" class="form-control wd120" name="'.$name.'[]" value="'.$value[0].'"/>';
        $options    = $input.'<select class="form-control wd60" name="'.$name.'[]">';
        foreach ($scoretypes as $scoreid => $title) {
            $options .= '<option value="' . $scoreid . '"'.($scoreid == $value[1] ? ' selected' : '').'>' . $title . '</option>';
        }
        $options .= '</select></div>';
        return $options;
    }

    public static function usetypescoreType($name, $value, $v) {
        $scoretypes = ScoreHelper::scoretypes();
        if (empty($scoretypes)) {
            return static::usetypescoreyyType($name, $value, $v);
        }
        !is_array($value) && $value = json_decode($value, true);

        $name       = tablesForm::inputName($name, $v);
        $box        = '<div class="flex-box usetypescoreb">';
        $input      = $box.'<input type="text" class="form-control wd120" name="'.$name.'[]" value="'.$value[0].'"/>';
        $options    = $input.'<select class="form-control wd60" name="'.$name.'[]">';
        foreach ($scoretypes as $scoreid => $title) {
            $options .= '<option value="' . $scoreid . '"'.($scoreid == $value[1] ? ' selected' : '').'>' . $title . '</option>';
        }
        $options .= '</select></div>';
        return $options;
    }

    public static function wangeditorType($name, $value, $v) {
        return editorWidget::wangEditor($name, $value, $v);
    }

    public static function uploadimgType($name, $value, $v) {
        //return uploadsWidget::image($name, $v['inputname'], $value, '上传限制:'.$v['settings']);
        return static::uploadattachType($name, $value, $v);
    }

    public static function uploadattachType($name, $value, $v) {
        $jsinit = true;
        $isForm = isset($v['formid']);
        if (isset($v['_parentInfo'])) {
            $jsinit = empty($v['_notUploadJs']);
            $v['id'] = $v['_parentInfo']['id'];
            $isForm = $isForm ?: isset($v['_parentInfo']['formid']);
        }
        return uploadsWidget::attach($name, $v['inputname'], $value, '限:'.$v['settings'], $isForm, $v['id'], false, $jsinit);
    }

    public static function htmlType($name, $value, $v)
    {
        return $v['settings'];
    }

    public static function tablesType($name, $value, $v) {
        return self::tablesData($v);
    }

    public static function navsType($name, $value, $v) {
        return self::tablesData($v);
    }

    public static function tablesnavType($name, $value, $v) {
        return self::tablesData($v, 1);
    }

    public static function slidesType($name, $value, $v) {
        return self::tablesData($v);
    }

    public static function tabletrType($name, $value, $v) {
        $values = is_array($v['inputvalue']) ? $v['inputvalue'] : unserialize($v['inputvalue']);
        $value = $values ? reset($values) : [];

        $fields = static::tablesFields($v['settings']);
        $tr = '';
        $tr.= '<tr class="myAddTrx">';
        $htm = '<div class="data-xtable table-fields-box"><table><tr>';
        foreach ($fields as $k => $r) {
            $valx = $value[$k];
            $_style = $r['width'] ? ' style="width:'.$r['width'].'px"' : '';
            $kcl = '';
            $ckK = strtolower(substr($k, -6));
            if ($ckK == 'status') {
                $kcl = ' tab-center wd30';
                $tr.= '<td title="' . $r['tip'] . '"><div class="item-title setToggleValue">
                        <label class="label-switch scale7 color-blue"><input type="checkbox" '.(!$valx ? 'checked' : '').'><div class="checkbox"></div></label>
                        <input type="hidden" class="form-control" name="tables[' . $v['inputname'] . '][' . $k . '][]" value="'.$valx.'">
                    </div></td>';
            }elseif ($ckK == 'upload') {
                $tr .= '<td title="'.$r['tip'].'">'.uploadsWidget::run('tables['.$v['inputname'].']['.$k.'][]', $v['inputname'].$k, $valx).'</td>';
            }elseif ($options = Lev::arrv('options', $r, [])) {
                if (!is_array($options)) {
                    $tr .= '<td title="' . $r['tip'] . '">'.$options.'</td>';
                }else {
                    $ops = '';
                    foreach ($options as $key => $val) {
                        $slt = $valx == $key ? ' selected' : '';
                        $ops .= '<option value="' . $key . '" '.$slt.'>' . $val . '</option>';
                    }
                    $tr .= '<td title="' . $r['tip'] . '"><div class="input input-dropdown"><select name="tables[' . $v['inputname'] . '][' . $k . '][]" placeholder="' . $r['tip'] . '">' . $ops . '</select></div></td>';
                }
            }else {
                $tr .= '<td title="' . $r['tip'] . '"><input type="text" name="tables[' . $v['inputname'] . '][' . $k . '][]" placeholder="' . $r['tip'] . '" value="'.$valx.'"></td>';
            }
            $htm.='<th class="kfield_'.$k.$kcl.'" '.$_style.'>'.$r['name'].'</th>';
        }
        $htm.= $tr.'</table></div>';
        return $htm;
    }

    /**
     * @param $v
     * @param bool $child 是否为二级数据表格
     * @return string
     */
    public static function tablesData($v, $child = 0)
    {
        $fields = static::tablesFields($v['settings']);
        $tr = '<tbody class="newTabTrBox-'.$v['inputname'].'"></tbody>';
        $tr.= '<tr class="myAddTrx">';
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
                $tr.= '<td title="' . $r['tip'] . '"><div class="item-title setToggleValue">
                        <label class="label-switch scale7 color-blue"><input type="checkbox" checked><div class="checkbox"></div></label>
                        <input type="hidden" class="form-control" name="tables[' . $v['inputname'] . '][' . $k . '][]" value="0">
                    </div></td>';
            }elseif ($ckK == 'upload') {
                $tr .= '<td title="'.$r['tip'].'">'.uploadsWidget::run('tables['. $v['inputname'] .']['. $k . '][]', $v['inputname'].$k).'</td>';
            }elseif ($options = Lev::arrv('options', $r, [])) {
                if (!is_array($options)) {
                    $tr .= '<td title="' . $r['tip'] . '">'.$options.'</td>';
                }else {
                    $ops = '';
                    foreach ($options as $key => $val) {
                        $ops .= '<option value="' . $key . '">' . $val . '</option>';
                    }
                    $tr .= '<td title="' . $r['tip'] . '"><div class="input input-dropdown"><select name="tables[' . $v['inputname'] . '][' . $k . '][]" placeholder="' . $r['tip'] . '">' . $ops . '</select></div></td>';
                }
            }else {
                $tr .= '<td title="' . $r['tip'] . '"><input type="text" name="tables[' . $v['inputname'] . '][' . $k . '][]" placeholder="' . $r['tip'] . '"></td>';
            }
            $htm.='<th class="kfield_'.$k.$kcl.'" '.$_style.'>'.$r['name'].'</th>';
        }
        $htm.= '<th style="text-align:center;width:40px">删除</th></tr>';
        $tr.= '<td class="tab-center"><a class="addTabTr" title="新增一行" data-input="'.$v['inputname'].'">[+]</a></td></tr>';
        $values = is_array($v['inputvalue']) ? $v['inputvalue'] : unserialize($v['inputvalue']);
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
                    $tr.= '<tbody class="hiddenx childBox_'.$cls.' Navsx">'.$cldStr.'</tbody><tbody class="hiddenx childBox_'.$cls.'">
                        <tr><td class="tab-center"><div class="date">&angrt;</div></td><td colspan="100"><a class="tablesnavAddChildBtn" data-cls="'.$cls.'">新增子级</a></td></tr>
                    </tbody>';
                }else {
                    $tr .= '<tr>'.$trcontent.'</tr>';
                }
            }
        }
        $htm.= $tr.'</table></div>';
        return $htm;
    }
    public static function tablesTr($fields, $val, $v) {
        $tr = '';
        foreach ($fields as $k => $r) {
            $valx = Lev::arrv($k, $val, '');
            $ckK = strtolower(substr($k, -6));
            if ($ckK == 'status') {
                $tr.= '<td><div class="item-title setToggleValue">
                        <label class="label-switch scale7 color-blue">
                            <input type="checkbox" '.(!$valx ? 'checked' : '').'><div class="checkbox"></div>
                        </label>
                        <input type="hidden" class="form-control" name="tables['.$v['inputname'].']['.$k.'][]" value="'.$valx.'">
                    </div></td>';
            }elseif ($options = Lev::arrv('options', $r, [])) {
                if (!is_array($options)) {
                    $tr .= '<td>'. static::actionTablesObjectMethod($r['type'], [$val['id']], $k).'</td>';
                }else {
                    $ops = '';
                    foreach ($options as $key => $vl) {
                        $slt = $valx == $key ? 'selected' : '';
                        $ops .= '<option value="' . $key . '" ' . $slt . '>' . $vl . '</option>';
                    }
                    $tr .= '<td><div class="input input-dropdown"><select name="tables[' . $v['inputname'] . '][' . $k . '][]">' . $ops . '</select></div></td>';
                }
            }else {
                $tr .= '<td><input type="text" name="tables['.$v['inputname'].']['.$k.'][]" value="' . $valx . '"></td>';
            }
        }
        $cls = $val['id'].'_'.$v['inputname'];
        return $tr.'<td class="tab-center"><a class="date delTr" data-cls="'.$cls.'">x</a></td>';
    }
    public static function tablesFields($settings) {
        $field = explode("\n", $settings);
        $fields = ['id' => ['key' => 'id', 'name' => 'ID', 'tip'=>'必填,清空ID删除本条数据', 'type'=>'', 'width'=>'']];
        foreach ($field as $r) {
            $one = explode('=', $r);
            $key = trim(strip_tags($one[0]));
            $val = trim(Lev::arrv(1, $one, false));
            $tip = trim(Lev::arrv(2, $one, ''));
            $type = trim(Lev::arrv(3, $one, ''));
            $width = trim(Lev::arrv(4, $one, ''));
            if ($key && $val !== false) {
                $fields[$key] = ['key' => $key, 'name' => $val, 'tip'=>$tip, 'type'=>$type, 'width'=>$width];
                if ($type) {
                    $fields[$key]['options'] = static::actionTablesObjectMethod($type, [], $key);
                }
            }
        }
        return $fields;
    }

    public static function actionTablesObjectMethod($method, $config, $key) {
        return Lev::actionObjectMethod($method, $config, 'set'.$key, true) ?: Lev::actionObjectMethod($method, [], 'field_'.$key, true);
    }

    public static function checkJsonField($inputtype) {
        return in_array($inputtype, ['usetypescore', 'usetypescoreyy', 'selects', 'selectscode']);
    }

    public static function checkTablesField($inputtype) {
        return stripos($inputtype, 'table') === 0 || in_array($inputtype, ['tables', 'navs', 'tablesnav', 'slides', 'tabletr']);
    }

    public static function checkLogoField($field) {
        return in_array(strtolower(substr($field, 0, 4)), ['logo']);
    }

    public static function checkUploadField($field) {
        return strtolower(substr($field, -6)) == 'upload';
    }

    public static function checkLinkField($field) {
        return strtolower(substr($field, 0, 4)) == 'link';
    }

    public static function replaceKeyword($string) {
        if (is_array($string)) {
            foreach ($string as $k => $v) {
                $string[$k] = static::replaceKeyword($v);
            }
        }else {
            return str_ireplace('{homeUrl}', Lev::getAlias('@siteurl') . '/', $string);
        }
        return $string;
    }

    /**
     * @param array $fields
     * @return string
     */
    public static function getSlidesUploadField($fields) {
        if ($fields) foreach ($fields as $field) {
            if (self::checkUploadField($field)) return $field;
        }
        return 'upload';
    }

    /**
     * 格式化入库前的inputs数据
     * @param $datas
     * @param $inputs
     * @param array $tables
     * @param array $tablesFormData
     * @return mixed
     */
    public static function formatSaveInputsData($datas, $inputs, $tables = [], $tablesFormData = []) {
        if ($tables || $tables = Lev::POSTv('tables')) {
            //$datas += static::tablesDataFormat($tables, static::getTablesIntID($inputs));
            is_array($tabDatas = static::tablesDataFormat($tables, static::getTablesIntID($inputs))) &&
            $datas = $tabDatas + $datas;
        }
        if ($tablesFormData || $tablesForm = Lev::POSTv(tablesForm::getFormPre().'__addtr')) {
            //$datas += tablesForm::dataFormat(Lev::POSTv(tablesForm::getFormPre()), $tablesForm);
            is_array($tabDatas = tablesForm::dataFormat(Lev::POSTv(tablesForm::getFormPre()), $tablesForm)) &&
            $datas = $tabDatas + $datas;
        }

        foreach ($inputs as $v) {
            if ($v['inputtype'] == 'wangeditor') {
                $datas[$v['inputname']] = Lev::decodeHtml(Lev::removeScript(Lev::decodeHtml($datas[$v['inputname']])), false);
            }elseif ($v['inputname'] !== 'settings' && is_array($datas[$v['inputname']])) {
                $datas[$v['inputname']] = json_encode($datas[$v['inputname']]);
            }
        }
        return $datas;
    }

    public static function tablesDataShowFormat($datas, $iden, $lazy = false) {
        $datas && !is_array($datas) && $datas = unserialize($datas);
        $result = [];
        if ($datas) {
            foreach ($datas as $k => $v) {
                foreach ($v as $fd => $vl) {
                    if ($vl) {
                        $v = static::formatInputvalue($v, $fd, $iden);
                    }
                }
                isset($v['target']) && $v['_target'] = SettingsHelper::navTarget($v['target']);
                !empty($v['cld__']) && $v['cld__'] = static::tablesDataShowFormat($v['cld__'], $iden, $lazy);
                $result[$k] = $v;
            }
        }
        return $result;
    }

    /**
     * 格式化来自数据库的inputs数据
     * @param $inputvalue
     * @param $inputSetInfo
     * @return array|mixed|string|void
     */
    public static function showData($inputvalue, $inputSetInfo) {

        if (static::checkTablesField($inputSetInfo['inputtype'])) {
            $inputvalue = static::tablesDataShowFormat($inputvalue, $inputSetInfo['moduleidentifier'], true);
            if ($inputSetInfo['inputtype'] == 'slides') {
                //!is_array($inputvalue) && $inputvalue = unserialize($inputvalue);
                if ($inputvalue) {
                    $keys = reset($inputvalue);
                    $lists = SettingsHelper::slidesFormat($inputvalue, static::getSlidesUploadField(array_keys($keys)));
                    return slidesWidget::run($lists);
                }else {
                    return '';
                }
            } else {
                $lists = SettingsHelper::tablesnavFormat($inputvalue);
                return tablesnavWidget::run($lists, $inputSetInfo);
            }
        }elseif (in_array($inputSetInfo['inputtype'], ['selectcode'])) {
            $item = static::getSelectItem($inputSetInfo, 1);
            $inputvalue = $item[$inputvalue];
        }elseif (in_array($inputSetInfo['inputtype'], ['selectscode'])) {
            $item = static::getSelectItem($inputSetInfo, 1);
            return self::getSelectsShow($item, $inputvalue);
        }elseif (in_array($inputSetInfo['inputtype'], ['select'])) {
            $item = static::getSelectItem($inputSetInfo);
            $inputvalue = $item[$inputvalue];
        }elseif (in_array($inputSetInfo['inputtype'], ['selects'])) {
            $item = static::getSelectItem($inputSetInfo);
            return self::getSelectsShow($item, $inputvalue);
        }elseif (in_array($inputSetInfo['inputtype'], ['radio'])) {
            //return $inputvalue ? '关闭' : '开启';
            return static::radioType($inputSetInfo['inputname'], $inputvalue, ['_disabled'=>1]);
        }elseif (in_array($inputSetInfo['inputtype'], ['wangeditor'])) {
            return Assetsv::highlight(1).Lev::decodeHtml($inputvalue);
        }elseif (in_array($inputSetInfo['inputtype'], ['uploadattach'])) {
            return !$inputvalue ? '没有附件' : '<a href="'.Lev::uploadRealSrc($inputvalue).'" class="openPP">下载附件</a>';
        }elseif (in_array($inputSetInfo['inputtype'], ['uploadimg'])) {
            return '<img class=lazy data-src="'.Lev::uploadRealSrc($inputvalue).'">';
        }elseif (in_array($inputSetInfo['inputtype'], ['usetypescore'])) {
            $scoretypes = ScoreHelper::scoretypes();
            $value = !is_array($inputvalue) ? json_decode($inputvalue, true) : $inputvalue;
            return $value[0].Lev::arrv($value[1], $scoretypes);
        }
        return $inputvalue;
    }
    public static function getSelectItem($inputSetInfo, $code = false) {
        return selectSearch::getSelectItem($inputSetInfo, $code);
    }
    public static function getSelectsShow($item, $inputvalue) {
        if (!$inputvalue) return '';
        !is_array($inputvalue) && $inputvalue = json_decode($inputvalue, true);
        $_res = [];
        foreach ($inputvalue as $v) {
            $_res[] = $item[$v];
        }
        return implode(' | ', $_res);
    }

}