<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-06 11:09
 *
 * 项目：upload  -  $  - Modelv.php
 *
 * 作者：liwei 
 */

namespace lev\base;

use Lev;
use lev\helpers\dbHelper;
use lev\helpers\SettingsHelper;
use lev\widgets\inputs\inputsWidget;

!defined('INLEV') && exit('Access Denied LEV');


class Modelv extends Migrationv
{
    public static $opInfo = null; //缓存一条opid数据
    public static $tableName = '';

    public static function tableName($tableName = '', $prefix = true) {
        $tableName = $tableName ?: static::$tableName;
        !$prefix && $tableName = static::preTableName($tableName);
        return static::quoteTableName($tableName, $prefix);
    }

    public static function insert($data, $returnInsertId = false) {
        return dbHelper::insert(static::tableName('', 0), $data, $returnInsertId);
    }

    public static function safeColumns($columns) {
        return dbHelper::safeColumns(static::tableName(), $columns);
    }
    public static function safeColumnsInpus($columns) {
        $inputs = static::inputsKeyField();
        foreach ($columns as $k => $v) {
            //if ($k != 'settings' && (!isset($inputs[$k]) || static::isSettingsField($k))) unset($columns[$k]);
            if ($k != 'settings' && !isset($inputs[$k])) unset($columns[$k]);
        }
        return $columns;
    }
    public static function safeColumnsGen($columns) {
        return $columns;
    }

    /**
     * 缓存一条opid数据
     * @param null $opid
     * @param null $opInfo
     * @return |null
     */
    public static function setopInfo($opid = null, $opInfo = null) {
        if ($opInfo !== null) {
            static::$opInfo = $opInfo;
        }else if (static::$opInfo === null) {
            $opid === null &&
            $opid = Lev::stripTags(Lev::GPv('opid'));
            if ($opid || is_numeric($opid)) {
                static::$opInfo = static::findOne(['id'=>$opid]);
            }
            !static::$opInfo && static::$opInfo = [];
        }
        return static::$opInfo;
    }

    /**
     * 注册模块用户
     * @param $uid
     * @param $where
     * @param array $insert
     * @return array
     */
    public static function registerMudUser($uid, $where = [], array $insert = []) {
        if ($uid < 1) {
            return [];
        }

        static $userinfos;
        if (!isset($userinfos[$uid])) {
            $userinfo = static::findOne($where ?: ['uid'=>$uid]);
            if (empty($userinfo)) {
                $insert['addtime'] = Lev::$app['timestamp'];
                isset($insert['id']) || $insert['uid'] = $uid;
                $userinfo = $insert;
                $userinfo['id'] = static::insert($insert, true);
            }

            $userinfos[$uid] = $userinfo;
        }
        return $userinfos[$uid];
    }

    public static function isSettingsField($field) {
        return strpos($field, 'settings__') === 0;
    }
    public static function getFormSettings($datas, $settings = []) {
        $settings = Lev::getSettings($settings);
        foreach ($datas as $k => $v) {
            if (static::isSettingsField($k)) {
                $settings[explode('settings__', $k)[1]] = Lev::stripTags($v);
                unset($datas[$k]);
            }
        }
        $datas['settings'] = Lev::setSettings($settings);
        return $datas;
    }
    public static function setFormSettings($settings, $result = []) {
        if ($settings) {
            $settings = Lev::getSettings($settings);
            foreach ($settings as $k => $v) {
                $result['settings__' . $k] = $v;
            }
        }
        return $result;
    }

    public static function setupDesc() {
//        return [
//            1 => '第一步',
//            2 => '第二步',
//            3 => '第三步',
//            4 => '第四步',
//            5 => '第五步',
//        ];
        return [];
    }
    public static function getNextSetup() {
        $setup = Lev::GETv('setup') ?: 1;
        $setupDesc = static::setupDesc();
        $next = 0;
        foreach ($setupDesc as $k => $v) {
            if ($next) {
                $next = $k;
                break;
            }
            $k == $setup && $next = true;
        }
        return $next === true ? $k : $next;
    }

    public static function total($where) {
        is_array($where) && $where = static::createWhereFromArray($where);
        $total = dbHelper::findOne('SELECT COUNT(*) FROM '.static::tableName().($where ? ' WHERE '.$where : ''));
        return floatval($total['COUNT(*)']);
    }

    /**
     * @param $data
     * @param string|array $condition
     * @return mixed
     */
    public static function update($data, $condition = '') {
        return dbHelper::update(static::tableName('', 0), $data, $condition);
    }

    public static function delete($condition, $limit = 0) {
        return dbHelper::delete(static::tableName('', 0), $condition, $limit);
    }

    public static function findOne($where, $field = '*') {
        is_array($where) && $where = static::createWhereFromArray($where);
        $fullSql = "SELECT $field FROM ".static::tableName()." WHERE ".$where." LIMIT 1";
        return dbHelper::findOne($fullSql);
    }

    public static function findAll($where, $keyfield = '', $order = [], $field = '*') {
        return dbHelper::findAll(static::getFullSql($where, $order, $field), [], $keyfield);
    }
    public static function getFullSql($where, $order = [], $field = '*') {
        is_array($where) && $where = static::createWhereFromArray($where);
        $fullSql = "SELECT $field FROM " . static::tableName() . " WHERE " . $where;
        $order && $fullSql .= " ORDER BY " . (is_array($order) ? implode(', ', $order) : $order);
        return $fullSql;
    }

    public static function findAllField($field, $where, $keyfield = '', $order = []) {
        return static::findAll($where, $keyfield, $order, $field);
    }

    public static function save($datas, $where = [], $inId = false) {
        return $where ? static::update($datas, $where) : static::insert($datas, $inId);
    }

    public static function saveInputs($datas, $where = [], $inId = false, $tables = [], $inputs = [], $formSet = true, $tablesFormData = []) {
        !$inputs && $inputs = static::inputs();
        $datas = inputsWidget::formatSaveInputsData($datas, $inputs, $tables, $tablesFormData);
        $formSet && (!$where || isset($datas['settings'])) && $datas = static::getFormSettings($datas, $datas['settings']);
        isset($datas['settings']) && $datas['settings'] = Lev::setSettings($datas['settings']);
        $datas = static::safeColumnsGen($datas);
        return static::save($datas, $where, $inId);
    }

    public static function inputsDataShow($inputvalue, $inputname) {
        return inputsWidget::showData($inputvalue, static::inputs()[$inputname]);
    }

    public static function inputsKeyField($iden = '') {
//        !$iden && $iden = Lev::$app['iden'];
//        return SettingsHelper::getModuleTabSettings($iden, static::tableName('', false), 'inputname') ?: [];
        return static::inputs($iden, 'inputname');
    }

    public static function inputsSetup() {
        $inputs = static::inputs();
        foreach ($inputs as $v) {
            $classify[$v['status']][$v['inputname']] = $v;
        }
        return $classify;
    }

    public static function getInputInfo($inputname, $tablesInputId = null) {
        $inputInfo = Lev::arrv($inputname, static::inputs(), []);
        $tablesInputId !== null &&
        $inputInfo = Lev::arrv($tablesInputId, $inputInfo['settings']['tablesForm'], []);
        return $inputInfo;
    }

    /**
     * 表模型中定义
     * @param string $iden
     * @param string $keyfield
     * @param null $classify
     * @return array
     */
    public static function inputs($iden = '', $keyfield = '', $classify = null) {
        !$iden && $iden = Lev::$app['iden'];
        $classify === null && $classify = static::tableName('', false);
        return SettingsHelper::getModuleTabSettings($iden, $classify, $keyfield) ?: [];
    }


    public static function formRoute($formName) {
        return 'Form'.Lev::ucfirstv($formName);
    }
    /**
     * @param string $formPre
     * @param string $route
     * @param null $form
     * @param null $upData
     * @param null $upDataForce
     * @param null $opInfo
     * @return array
     * @see Modelv::findOne()
     * @see Modelv::safeColumnsInpus()
     * @see Modelv::saveInputs()
     * @see Modelv::getNextSetup()
     */
    public static function saveForm($formPre = 'datax', $route = '', $form = null, $upData = null, $upDataForce = null, $opInfo = null)
    {
        $upData === null &&
        $upData = Lev::stripTags(Lev::POSTv($formPre));

        $opid = intval(Lev::GPv('opid'));

        $opInfo === null &&
        $opInfo = $opid >0 ? static::findOne(['id'=>$opid]) : [];

        $upData['settings'] = $opInfo ? Lev::getSettings($opInfo['settings']) : [];
        $upData = static::safeColumnsInpus($upData);
        //$upData = static::safeColumns($upData);

        $upDataForce !== null && $upData += $upDataForce;

        if (isset($upData['id']) && !$upData['id']) {
            unset($upData['id']);
        }

        $upData['uptime'] = Lev::$app['timestamp'];
        if ($opInfo) {
            $rs = static::saveInputs($upData, ['id'=>$opid]);
        }else {
            $upData['addtime'] = Lev::$app['timestamp'];
            $rs = static::saveInputs($upData, [], true);
            $opid = $upData['id'] = $rs;
        }
        if ($rs) {
            $tourl = $route ? Lev::toReRoute([$route, 'opid'=>$opid, 'setup'=>static::getNextSetup(), 'form'=>$form ?: null]) : '';
            return Lev::responseMsg(1, '保存成功', ['opid'=>$opid, 'tourl'=>$tourl, 'upData'=>$upData]);
        }
        return Lev::responseMsg(-2203, '保存失败');
    }

    /**
     * 无限加载数据
     * @param $where
     * @param int $limit
     * @param array $order eg: ['displayorder ASC', 'id ASC']
     * @param string $field
     * @param int $page
     * @return array
     */
    public static function pages($where, $limit = 20, $order = [], $field = '*', $page = 0, $keyfield = '') {
        $page = max(intval($page ?: Lev::GPv('page')), 1);
        $offset = ($page - 1) * $limit;

        is_array($where) && $where = Modelv::createWhereFromArray($where);
        $order = $order ? "ORDER BY ".(is_array($order) ? implode(', ', $order) : $order) : '';

        $where.= " $order LIMIT $limit OFFSET $offset";
        return static::findAll($where, $keyfield, '', $field);
    }

    public static function pageButtons($where, $limit = 20, $order = [], $buttonNum = 5, $url = '', $field = '*', $page = 0) {
        $page = max(intval($page ?: Lev::GPv('page')), 1);

        is_array($where) && $where = Modelv::createWhereFromArray($where);
        $total = static::total($where);
        $pageSize = ceil($total / $limit);
        $lists = $total <1 ? [] : static::pages($where, $limit, $order, $field, $page);
        $pages = '<a class="pg-btn button-fill button scale8 color-red" href="'. static::pageRoute($url, null).'">'.$total.'条</a>';
        if ($pageSize >1) {
            $buttonNum = $buttonNum > $pageSize ? $pageSize : ($buttonNum <1 ? 1 : $buttonNum);
            $max = $page + $buttonNum;
            $max = ($max > $pageSize ? $pageSize : $max) +1;
            $min = $page - intval($buttonNum/2);
            $xnum = $max - $min;
            $min = $xnum < $buttonNum ? $min - ($buttonNum - $xnum) : $min;
            $min <1 && $min = 1;
            for($i = $min; $i < $max; $i++) {
                if ($i - $min + 1 > $buttonNum) break;
                $active = $i == $page ? 'button-fill':'';
                $pages .= '<a class="pg-btn button scale8 '.$active.'" href="'. static::pageRoute($url, $i).'">'.$i.'</a>';
            }
            if ($pageSize > $buttonNum) {
                $buttonNum ==1 &&
                $pages .= '<a class="pg-btn button scale8" href="'. static::pageRoute($url, $page-1<1?1:$page-1).'">上</a>';
                $buttonNum <3 &&
                $pages.= '<a class="pg-btn button scale8" href="'. static::pageRoute($url, $page+1>$pageSize?$pageSize:$page+1).'">下</a>';
                $pages.= '<a class="pg-btn button scale8" href="'. static::pageRoute($url, $pageSize).'">尾</a>';
            }
        }
        $pages = '<div class="pg-boxb flex-box">'.$pages.'</div>';

        return array('pages'=>$pages, 'lists'=>$lists, 'total'=>$total);
    }
    public static function pageRoute($url, $page) {
        static $param;
        if (!isset($param)) {
            $param = $url ? (is_array($url) ? $url : Lev::getUrlParam($url)) : [];
        }
        if ($param) {
            $param['page'] = $page;
            return Lev::toReWrRoute($param);
        }
        return Lev::toCurrent(['page'=>$page]);
    }

    public static function adminop($adminop) {
        switch ($adminop) {
            case 'setStatus' : $tips = static::setStatus(); break;
            case 'setField'  : $tips = static::setField(); break;
            case 'deleteDay' : $tips = static::adminDayDelete(); break;
            case 'deleteIds' : $tips = static::adminDelete(); break;
            case 'copyOne'   : $tips = static::copyOne(); break;
            default: $tips = null; break;
        }
        return $tips;
    }

    public static function copyOne($id = null) {
        $id === null && $id = Lev::stripTags(Lev::GPv('opid'));
        $info = static::findOne(['id'=>$id]);
        if (empty($info)) {
            return Lev::responseMsg(-1, '复制失败，查无数据：'.$id);
        }
        unset($info['id']);
        $inid = static::insert($info, true);
        return Lev::responseMsg(1, '复制成功：'.$inid, ['inid'=>$inid]);
    }

    /**
     * @param string $optab
     * @param array $upData
     * @return array
     */
    public static function setStatus($optab = '', $upData = []) {
        $optab && static::$tableName = $optab;
        $opid = intval(Lev::GPv('opid'));
        $status = intval(Lev::GPv('status')) ? 1 : 0;
        $field = Lev::stripTags(Lev::GPv('field'));
        if ($opid <1) {
            return Lev::responseMsg(-1023, 'opid不能为空');
        }

        $upData[$field?:'status'] = $status;//[$field?:'status'=>$status]

        $count = static::update($upData, ['id'=>$opid]);
        return Lev::responseMsg(1, '操作成功', ['count'=>$count]);
    }

    /**
     * @param string $optab
     * @param null $val
     * @param array $upData
     * @return array
     */
    public static function setField($optab = '', $val = null, $upData = []) {
        $optab && static::$tableName = $optab;
        $opid = intval(Lev::GPv('opid'));
        if ($opid <1) {
            return Lev::responseMsg(-1023, 'opid不能为空');
        }
        $idkey = Lev::stripTags(Lev::GPv('idkey'));
        $field = Lev::stripTags(Lev::GPv('field'));
        $val = $val ?: Lev::stripTags(Lev::GPv('val'));

        if (static::isSettingsField($field)) {
            $opinfo = static::findOne([$idkey?:'id'=>$opid]);
            if (empty($opinfo)) {
                return Lev::responseMsg(-1025, '查无数据：'.$opid);
            }
            $upData = static::getFormSettings([$field=>$val], $opinfo['settings']);
        }else {
            $upData[$field] = $val;
        }

        $count = static::update($upData, [$idkey?:'id'=>$opid]);
        return Lev::responseMsg(1, '操作成功', ['count'=>$count]);
    }

    public static function adminDelete($optab = '') {
        $optab && static::$tableName = $optab;
        if ($ids = Lev::POSTv('ids')) {
            $ids = array_map(function ($v){ return intval($v); }, $ids);
            $ids = array_unique($ids);
            $insql = implode(',', $ids);

            $field = Lev::stripTags(Lev::GPv('field'));
            $field = $field ? $field : 'id';

            if (($insql || is_numeric($insql))) {
                $ck = static::delete("$field IN($insql)");
                return Lev::responseMsg(1, '成功删除 '.count($ids).' 条数据', ['delrs'=>$ck, 'ids'=>$ids]);
            }
            return Lev::responseMsg(-4001, '没有可删除数据或未提交数据表');
        }
        return Lev::responseMsg(-4002, '请提交数据');
    }

    public static function adminDayDelete($extWhere = '') {
        if (is_numeric($day = Lev::POSTv('day'))) {
            $field = Lev::stripTags(Lev::GPv('field'));
            $field = $field ? $field : 'addtime';

            if ($day <=0) {
                $count = dbHelper::truncateTable(static::tableName());
                return Lev::responseMsg(1, '成功清空表', ['delCount'=>$count]);
            }
            $addtime = Lev::$app['timestamp'] - $day *3600 *24;
            $count = static::delete("$extWhere $field < $addtime");
            return Lev::responseMsg(1, '成功删除数据', ['delCount'=>$count]);
        }
        return Lev::responseMsg(-4002, '天数必须是数字');
    }

    public static function errorMsg2($inputname, $status, $message, $ext = []) {
        return static::errorMsg($inputname, $message, $status, $ext);
    }
    public static function errorMsg($inputname, $message, $status = 1, $ext = []) {
        $ext['errors'] = [$inputname=>$message];
        return Lev::responseMsg($status, $message, $ext);
    }

    /**
     * 简单拼接，交给底层处理
     * @param $arr
     * @return string
     */
    public static function createWhereFromArray($arr) {
        $where = [];
        foreach ($arr as $field => $value) {
            $where[] = is_numeric($field) ? $value : "`$field`='$value'";
        }
        return implode(' AND ', $where);
    }
}