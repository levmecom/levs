<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 *
 * 创建时间：2021-11-25 20:03:14
 *
 * 项目：/ipban  -  $  - BaseAdminCensus.php
 *
 * 作者：{{AUTO GENERATE}}
 */

//此文件使用程序自动生成，下次生成时会覆盖，不建议修改。

namespace modules\levs\modules\ipban\controllers\_gen;

use Lev;
use lev\base\Adminv;
use lev\base\Assetsv;
use lev\base\Controllerv;
use lev\base\Modelv;
use lev\base\Viewv;
use lev\helpers\UrlHelper;
use lev\helpers\UserHelper;
use lev\widgets\adminModulesNav\adminModulesNav;
use modules\levs\modules\ipban\table\ipban_record_census\ipbanRecordCensusModelHelper;

!defined('INLEV') && exit('Access Denied LEV');

Adminv::checkAccess();
Assetsv::registerSuperman();

class BaseAdminCensus extends Controllerv
{

    public static $controllerName = 'modules\levs\modules\ipban\controllers\AdminCensusController';

    public static $Model = '\modules\levs\modules\ipban\table\ipban_record_census\ipbanRecordCensusModelHelper';
    public static $ModelSelf = '\modules\levs\modules\ipban\table\form\Form_ipban_record_census\Form_ipban_record_census';

    public static $tmpPath = 'admin-census';
    public static $trash   = true;//是否允许删除数据
    public static $addurl  = false;//是否显示创建数据按钮
    public static $copyOneBtn  = false;//复制一条数据
    public static $addRoute= 'Form_ipban_record_census';//自定义创建数据表单地址 - 默认使用源表单

    public static $srhtitle    = '';//模糊搜索字段名称
    public static $srhkeyWhere = '';//模糊搜索sql语句 例：name LIKE '%{srhkey}%'

    public static $limit = 20;//每页显示多少条
    public static $order = ['id DESC'];//排序

    public static $pages = ['pages'=>0, 'lists'=>[], 'total'=>0];//分页数据

    public static $tempDatas = [];//临时存储数据

    public static $SelectColumns = [];//存储下拉框字段

    /**
     * 列表显示字段设置
     * 例：array(
        'id'           => array('order' => null, 'name' => 'Id', 'thattr' => 'wd30 nowrap', 'merge' => []),

        'name'         => array('order' => null, 'name' => '模块名称', 'thattr' => 'wd30 nowrap', 'merge' => ['addtime']),

        'displayorder' => array('order' => null, 'name' => '排序', 'thattr' => 'wd30 nowrap', 'merge' => []),

        'uptime'       => array('order' => null, 'name' => '更新时间', 'thattr' => 'wd30 nowrap', 'merge' => []),

        );
     *
     * order: 点击字段排序
     * name: 字段名称
     * thattr: 表格th属性，放class属性内，内容举例：wd30 nowrap" style="font-size:12px
     * merge: 合并显示，多个字段合并显示到一个上
     */
    public static function showColumns() {
        return array(
                'id'         => array('order' => null, 'name' => 'Id', 'thattr' => 'wd30 nowrap', 'merge' => []),

                'pagename'   => array('order' => null, 'name' => '页面标识', 'thattr' => 'wd30 nowrap', 'merge' => []),

                'ip'         => array('order' => null, 'name' => 'IP', 'thattr' => 'wd30 nowrap', 'merge' => []),

                'iptotal'    => array('order' => null, 'name' => 'IP计数', 'thattr' => 'wd30 nowrap', 'merge' => []),

                'pagetotal'  => array('order' => null, 'name' => 'page计数', 'thattr' => 'wd30 nowrap', 'merge' => []),

                'requesturi' => array('order' => null, 'name' => '请求地址', 'thattr' => 'wd30 nowrap', 'merge' => []),

                'referer'    => array('order' => null, 'name' => '来源地址', 'thattr' => 'wd30 nowrap', 'merge' => []),

                'useragent'  => array('order' => null, 'name' => '用户标识', 'thattr' => 'wd30 nowrap', 'merge' => []),

                //'settings'   => array('order' => null, 'name' => '通用设置', 'thattr' => 'wd30 nowrap', 'merge' => []),

                'status'     => array('order' => null, 'name' => '状态', 'thattr' => 'wd30 nowrap', 'merge' => []),

                'uptime'     => array('order' => null, 'name' => '更新时间', 'thattr' => 'wd30 nowrap', 'merge' => []),

                //'addtime'    => array('order' => null, 'name' => '添加时间', 'thattr' => 'wd30 nowrap', 'merge' => []),

            );
    }

    public static function adminop() {
        if ($adminop = Lev::GPv('adminop')) {
            if (!static::$trash && in_array($adminop, ['deleteIds', 'deleteDay'])) {
                echo json_encode(Lev::responseMsg(-44, '抱歉，禁止删除数据'));
                return true;
            }elseif (Lev::GPv('confirmdoit') && !Lev::GPv('doit')) {
                Lev::showMessages(Lev::responseMsg(-45, '您确定要执行此操作吗？', ['confirmurl'=>Lev::toCurrent(['doit'=>1])]));
                return true;
            }
            if (($tips = ipbanRecordCensusModelHelper::adminop($adminop)) !== null) {
                echo json_encode($tips);
                return true;
            }
        }
        return false;
    }

    /**
     * Renders the index view for the module
     * @see Modelv::adminop()
     * @see Modelv::pageButtons()
     */
    public static function actionIndex()
    {

        if (static::adminop()) {
            return;
        }

        if (Lev::GPv('viewid') !== null) {
            static::actionView();
            return;
        }

        $allColumns = ipbanRecordCensusModelHelper::allColumns();

        Lev::$app['title'] = 'IP统计 管理';
        $srhtitle = static::$srhtitle;

        $where  = [];
        $cols = static::setColumnShowtype();
        foreach ($allColumns as $k => $v) {
            if (isset($cols['showtype']['srhkey_'.$k])) {
                //if ($srhkeyf = Lev::stripTags(Lev::GETv($k))) {
                if (($srhkeyf = Lev::stripTags(Lev::GETv($k))) || is_numeric($srhkeyf)) {
                    $sql = "`$k`='$srhkeyf'";
                    if (!empty($cols['showtype']['srhkey_'.$k]['or'])) {
                        if (is_array($cols['showtype']['srhkey_'.$k]['or'])) {
                            foreach ($cols['showtype']['srhkey_' . $k]['or'] as $_key) {
                                $sql .= " OR `$_key`='$srhkeyf'";
                            }
                        }else {
                            $_key = $cols['showtype']['srhkey_'.$k]['or'];
                            $sql .= " OR `$_key`='$srhkeyf'";
                        }
                        $sql = "($sql)";
                    }
                    $where[] = $sql;
                }
            }
        }

        $srhkey = '';
        if ($srhtitle && Lev::GPv('isSrhkeyup')) {
            $srhkey = Lev::stripTags(Lev::GETv('srhkey'));
            $srhkey = addcslashes($srhkey, '%_');
        }
        if (!empty($srhkey) && static::$srhkeyWhere) {
            $where[] = '('.str_replace('{srhkey}', $srhkey, static::$srhkeyWhere).')';
        }

        $where = implode(' AND ', $where);

        $asc = null;
        if ($orderFd = Lev::stripTags(Lev::GPv('orderFd'))) {
            $asc = Lev::GPv('asc') ? ' ASC' : ' DESC';
            $order[] = $orderFd . $asc;
        }else {
            $order = static::$order;
        }

        $limit = static::$limit;
        $pages = ipbanRecordCensusModelHelper::pageButtons($where?:1, $limit, $order);

        static::$pages = $pages;

        Viewv::render(static::$tmpPath.'/index', [
            'pages'       => $pages,
            'srhkey'      => $srhkey,
            'srhtitle'    => $srhtitle,
            'trash'       => static::$trash,
            'asc'         => $asc ? stripos($asc, 'ASC') === false : null,
            'addurl'      => static::$addurl ? static::addurl() : '',
            'allColumns'  => $allColumns,
            'showColumns' => static::showColumns(),
            'footerBtns'  => static::footerBtns(),
        ]);

    }

    public static function actionView() {
        $viewid = Lev::stripTags(Lev::GPv('viewid'));

        $info = ipbanRecordCensusModelHelper::findOne(['id'=>$viewid]);

        $allColumns = ipbanRecordCensusModelHelper::allColumns();

        Lev::$app['title'] = '查看数据：'.$viewid;

        Viewv::render(static::$tmpPath.'/view', [
            'info'       => $info,
            'srhkey'      => '',
            'srhtitle'    => '',
            'trash'       => static::$trash,
            'addurl'      => static::$addurl ? static::addurl() : '',
            'allColumns'  => $allColumns,
            'showColumns' => static::showColumns(),
            'footerBtns'  => static::footerBtns(),

            'headerViewHtm' => static::headerViewHtm(),
            'footerViewHtm' => static::footerViewHtm(),
        ]);
    }

    /**
     * Renders the index view for the module
     * @see Modelv::findOne()
     * @see Modelv::inputsSetup()
     * @see Modelv::setupDesc()
     */
    public static function actionForm() {
        $formPre = 'datax';

        $Model = static::$Model;

        $form = Lev::stripTags(Lev::GPv('form'));//通过传入表单名称调用表单
        if (!Lev::GPv('modelall')) {
            if ($form) {
                $form = Lev::ucfirstv($form);
                $Model = '\modules\levs\modules\ipban\table\form\Form' . $form . '\Form' . $form;
            } elseif (Lev::classExists(static::$ModelSelf)) {
                parent::redirect(Lev::toReRoute([static::$addRoute]));
            }
        }
        if (!Lev::classExists($Model)) {
            Lev::showMessages('表单不存在：'.$form);
        }

        if (Lev::POSTv('dosubmit')) {
            echo json_encode($Model::saveForm($formPre, static::$tmpPath.'/form', $form));
            return;
        }

        $opid = intval(Lev::GETv('opid'));
        $setup = Lev::stripTags(Lev::GETv('setup'));

        $opInfo = $opid >0 ? $Model::findOne(['id'=>$opid]) : [];
        //$inputs = ipbanRecordCensusModelHelper::inputs();
        $inputsSetup = $Model::inputsSetup();
        $setupDesc = $Model::setupDesc();
        $inputs = isset($inputsSetup[$setup]) ? $inputsSetup[$setup] : reset($inputsSetup);

        if ($opInfo) {
            $opInfo = $Model::setFormSettings($opInfo['settings'], $opInfo);
            Lev::$app['title'] = '编辑：'.$opInfo['id'];
        }else {
            Lev::$app['title'] = '创建';
        }

        Assetsv::animateCss();
        Viewv::render(static::$tmpPath.'/form_setup', [
            'opid'         => $opid,
            'inputs'       => $inputs,
            'extInputs'    => $Model::extInputs(),
            'setupDesc'    => $setupDesc,
            'inputsValues' => $opInfo,
            'formPre'      => $formPre,
            'setup'        => $setup,
            'addurl'       => static::addurl(),

            'headerHtm' => $Model::headerHtm(),
            'footerHtm' => $Model::footerHtm(),
            'footerFormInnerHtm' => $Model::footerFormInnerHtm(),
        ]);

    }
    public static function addurl() {
        return Lev::toReRoute([Lev::classExists(static::$ModelSelf) ? static::$addRoute : static::$tmpPath.'/form']);
    }

    /**
     * @return array
     */
    public static function setColumnShowtype() {
        return [
                'showtype' => [
                    //从上往下先到先得
                    'input_{columnKey}'  => ['width'=>120,'textarea'=>1],//显示为无刷新更改字段
                    'srhkey_{columnKey}' => ['or'=>['{columnKey}']],//显示为点击可搜索字段
                    'status_{columnKey}' => 1,//显示为开关状态字段 status 自动
                    'order_{columnKey}'  => 1,//显示为可排序输入框 含order关键字 自动
                    'time_{columnKey}'   => 1,//显示为时间格式字段 含time关键字 自动
                ],
            ];
    }

    /**
     * 格式化index.php列表页每个字段数据，更容易看懂
     * 优先显示级别最高
     * @param $sv
     * @return mixed
     */
    public static function formatListsv($sv, $lists) {
        $sv['#set'] = static::setColumnShowtype();
        $sv = static::formatUid($sv, $lists);
        return $sv;
    }

    /**
     * 格式化下拉型字段值
     * eg:
        public static function col_typeid() {
            return [
                0 => '三方模块',
                8 => '官方模块',
                9 => '基础模块',
            ];
        }
     * @param $columnKey
     * @param $val
     * @return mixed|null
     */
    public static function formatSelectColumn($columnKey, $val) {
        isset(static::$SelectColumns[$columnKey]) ||
        static::$SelectColumns[$columnKey] = method_exists('modules\levs\modules\ipban\controllers\AdminCensusController', $method = 'col_'.$columnKey) ? static::$method() : [];
        return Lev::arrv($val, static::$SelectColumns[$columnKey]);
    }

    /**
     * 将用户UID格式成点击可搜索并带有用户名
     * @param $sv
     * @param $lists
     * @param array $srhkey
     * @return mixed
     */
    public static function formatUid($sv, $lists, $srhkey = ['uid']) {
        static $users;
        foreach ($srhkey as $kv) {
            if ($cke = $sv[$kv]) break;
        }
        if ($cke) {
            if (!isset($users)) {
                $users = UserHelper::getUsers($lists, $srhkey);
                $users[0] = ['uid' => 0, 'username' => ''];
            }
            foreach ($srhkey as $kv) {
                if (isset($sv[$kv])) {
                    $sv[$kv] = '<a href="' . Lev::toCurrent([$kv => $sv[$kv]]) . '">'
                        . $sv[$kv] . '@<br>' . Lev::arrv([$sv[$kv], 'username'], $users, '#已丢失#') . '</a>';
                }
            }
        }
        return $sv;
    }

    /**
     * 可访问的链接
     * eg: <a _bk=1 target=_blank class="date inblk" href="'.Lev::toRoute(['/', $columnKey=>$sv[$columnKey]]).'"><svg class="icon color-gray"><use xlink:href="#fa-huoj"></use></svg></a>;
     * @param $columnKey
     * @param $sv
     * @return string
     */
    public static function redirct($columnKey, $sv) {
        return '';
    }

    /**
     * 操作链接
     * eg: <a class="button button-fill" href="'.Lev::toRoute(['/', 'opid'=>$sv['id']]).'">查看</a>;
     * @param $sv
     * @return string
     */
    public static function optButtons($sv) {
        return '';
    }

    //eg: <div class="card card-header"></div>
    /**
     * <tips class="gray inblk scale8">自定义headerViewHtm：文件位置：'.__DIR__ . '/BaseAdminCensus.php'.'</tips>
     * @return string
     */
    public static function headerViewHtm() {
        return '';
    }

    /**
     * <tips class="gray inblk scale8">自定义footerViewHtm</tips>
     * @return string
     */
    public static function footerViewHtm() {
        return '';
    }

    //eg: <div class="card card-header"></div>
    /**
     * @return string
     */
    public static function headerHtm() {
        return !Lev::isDeveloper(Lev::$app['iden']) ? '' :
            '<tips class="gray inblk scale8">自定义headerHtm：文件位置：'.__DIR__ . '/AdminCensusController.php'.'</tips>';
    }

    /**
     * <tips class="gray inblk scale8">自定义footerHtm</tips>
     * @return string
     */
    public static function footerHtm() {
        return '';
    }

    /**
     * @param array $btns
     * @return array
     */
    public static function footerBtns($btns = []) {
        $btns['pgset'] = [
            'name' => '<svg class="icon"><use xlink:href="#fa-set"></use></svg> 页面设置',
            'link' => UrlHelper::setAdminPage(Lev::base64_encode_url(static::$controllerName)),
            'attr' => 'color-black" target="_blank',
        ];
        return $btns;
    }

    /**
     * '<tips class="gray inblk scale8">自定义cardFooterButtons</tips>';
     * @param array $btns
     * @return string
     */
    public static function cardFooterButtons($btns = []) {
        $btns = $btns ?: static::footerBtns($btns);
        if (empty($btns) || !is_array($btns)) {
            return '';
        }
        return adminModulesNav::btnsrowHtm($btns);
    }

    public static function srhkeyUrl($srhkey, $value) {
        return Lev::toCurrent(['srhkey'=>null, 'isSrhkeyup'=>null, 'page'=>null, $srhkey=>$value]);
    }
    public static function copyOneUrl($opid) {
        return Lev::toCurrent(['opid'=>$opid, 'confirmdoit'=>1, 'doit'=>0, 'adminop'=>'copyOne']);
    }
}