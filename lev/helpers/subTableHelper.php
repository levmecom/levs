<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-11-13 11:46
 *
 * 项目：rm  -  $  - subTableHelper.php
 *
 * 作者：liwei 
 */

namespace lev\helpers;

use Lev;
use lev\base\Modelv;
use modules\levfm\helpers\levfmSetHelper;

!defined('INLEV') && exit('Access Denied LEV');


class subTableHelper extends cacheFileHelpers
{

    /**
     * 分表缓存文件目录
     */
    public static $cacheDir = '/.subTable';

    /**
     * 分表前缀
     */
    public static $subTablePre = '_subtable_';

    public static function getSubNameById($tableName, $id) {
        if ($subs = static::getSubTablesCache($tableName)) {
            unset($subs[0]);
            if ($subs) {
                foreach ($subs as $v) {
                    if ($v['startId'] <= $id && $v['endId'] >= $id) {
                        return $v['subName'];
                    }
                }
            }
        }
        return $tableName;
    }

    /**
     * 获取最新创建的一张分表名称
     * @param $tableName
     * @param bool $cache
     * @return mixed
     */
    public static function getSubTableNameIng($tableName, $cache = true) {
        $subTabs = static::getSubTableIng($tableName, $cache);
        return !empty($subTabs['subName']) ? $subTabs['subName'] : $tableName;
    }

    /**
     * 获取最新创建的一张分表详细信息
     * @param $tableName
     * @param bool $cache
     * @return array|mixed
     */
    public static function getSubTableIng($tableName, $cache = true) {
        $subTabs = $cache ? static::getSubTablesCache($tableName) : static::getSubTablesDB($tableName);
        return $subTabs ? end($subTabs) : [];
    }

    /**
     * 创建分表前设置一个缓存锁
     * @param $tableName
     * @param bool $check
     * @return bool|mixed|string
     */
    public static function createLock($tableName, $check = false)
    {
        $key = 'createLock'.dbHelper::tableName($tableName);
        if ($check) {
            if ($check === 'clear') {
                return static::clearc($key);
            }
            return static::getc($key);
        }
        return static::setc($key, 1, 60);
    }

    /**
     * 创建一个分表
     * @param $tableName
     * @return array
     */
    public static function createSubTableSchema($tableName) {
        $tableName = dbHelper::tableName($tableName);
        $subName = count(static::getSubTablesDB($tableName)) ?: 1;

        if (!dbHelper::existsTable($tableName)) {
            return Lev::responseMsg(-1, '分表创建失败，源数据表不存在：'.$tableName);
        }
        $subNameFull = $subNameFull = $tableName . static::$subTablePre . $subName;
        if (!dbHelper::existsTable($subNameFull)) {
            static::renameTable($tableName, $subNameFull);
            static::copyTableSchema($subNameFull, $tableName);
            $startId = static::selectTableAutoIncrement($subNameFull) + levfmSetHelper::startId();
            static::alterTableAutoIncrement($tableName, $startId);
            static::addSubTable($tableName, null, $startId);
            static::addSubTable($subNameFull);
            $msg = Lev::responseMsg(1, '恭喜，分表创建成功！表名称：'.$subNameFull);
        }else {
            $msg = Lev::responseMsg(2, '分表已存在！表名称：'.$subNameFull);
        }
        static::createLock($tableName, 'clear');
        return $msg;
    }

    /**
     * 设置缓存中存储分表信息
     * @param $tableName
     * @param string $pk
     * @param null $dataRows
     * @param int $startId
     * @param int $endId
     * @return array
     */
    public static function setSubTableInfo($tableName, $pk = 'id', $dataRows = null, $startId = 0, $endId = 0) {
        $tableName = dbHelper::tableName($tableName);
        if ($dataRows === null) {
            $endId = $startId ?: static::selectTableAutoIncrement($tableName);
            $start = dbHelper::findOne("SELECT $pk FROM $tableName ORDER BY $pk ASC");
            if (empty($start[$pk])) {
                $startId = $endId;
                $endId = 0;
            } else {
                $startId = $start[$pk];
                $endId -= 1;
            }
            $dataRows = dbHelper::findOne("SELECT COUNT(*) FROM $tableName ");
            $dataRows = empty($dataRows['COUNT(*)']) ? 0 : $dataRows['COUNT(*)'];
        }

        return ['dataRows'=>$dataRows, 'startId'=>$startId, 'endId'=>$endId, 'subName'=>$tableName];
    }

    /**
     * 在缓存中更新或添加一张分表
     * @param $tableName
     * @param null $dataRows
     * @param int $startId
     * @param int $endId
     */
    public static function addSubTable($tableName, $dataRows = null, $startId = 0, $endId = 0) {
        $tableName = dbHelper::tableName($tableName);

        $ckey = static::$subTablePre;
        !is_array($arr = static::getc($ckey, false)) && $arr = [];
        $tabs = explode($ckey, $tableName);
        $tab = $tabs[0];
        $tabKey = intval($tabs[1]);
        $arr[$tab][$tabKey] = static::setSubTableInfo($tableName, 'id', $dataRows, $startId, $endId);
        static::setc($ckey, $arr);
    }

    /**
     * 从缓存中获取分表
     * @param string $tableName
     * @return array|mixed|string
     */
    public static function getSubTablesCache($tableName = '') {
        !is_array($res = static::getc(static::$subTablePre, false)) && $res = [];
        if ($res && $tableName) {
            return $res[Modelv::tableName($tableName)];
        }
        return $res;
    }

    /**
     * 从数据库中获取分表
     * @param string $tableName
     * @return array|mixed
     */
    public static function getSubTablesDB($tableName = '') {
        $result = [];

        $res = static::findSubTables(($tableName ? dbHelper::tableName($tableName) : '%').static::$subTablePre.'%');
        foreach ($res as $sub) {
            $sub = reset($sub);
            $arr = explode(static::$subTablePre, $sub);
            $tab = $arr[0];
            $subNum = intval($arr[1]);
            empty($result[$tab][0]) && $result[$tab][0] = static::setSubTableInfo($tab);
            $result[$tab][$subNum] = static::setSubTableInfo($sub);
        }

        foreach ($result as $k => $v) {
            ksort($v, SORT_NUMERIC);
            $result[$k] = $v;
        }
        return empty($result[$tableName]) ? $result : $result[$tableName];
    }

    /**
     * 更新分表缓存
     */
    public static function updateSubTablesCache() {
        static::setc(static::$subTablePre, static::getSubTablesDB());
    }

    /**
     * 查找所有分表
     * @param $tableName
     * @return array
     */
    public static function findSubTables($tableName) {
        return dbHelper::findAll("SHOW TABLES LIKE '{$tableName}'");
    }

    /**
     * 修改一张表名称
     * @param $sourceTab
     * @param $newTabName
     */
    public static function renameTable($sourceTab, $newTabName) {
        $sql = 'alter table '.$sourceTab.' rename TO '.$newTabName;
        dbHelper::executeSql($sql);
    }

    /**
     * 复制一张表结构
     * @param $sourceTab
     * @param $newTabName
     */
    public static function copyTableSchema($sourceTab, $newTabName) {
        $sql = 'create table IF NOT EXISTS '.$newTabName.' like '.$sourceTab;
        dbHelper::executeSql($sql);
    }

    /**
     * 设置表自增值
     * @param $tableName
     * @param $number
     */
    public static function alterTableAutoIncrement($tableName, $number) {
        $sql = 'alter table '.$tableName.' auto_increment='.$number;
        dbHelper::executeSql($sql);
    }

    /**
     * 删除分表
     * @param $tableName
     */
    public static function dropSubTable($tableName) {
        dbHelper::executeSql("DROP TABLE IF EXISTS ".dbHelper::tableName($tableName));
        static::updateSubTablesCache();
        return Lev::responseMsg(1, '操作完成，刷新页面');
    }

    /**
     * 获取表自增值
     * @param $tableName
     * @return int
     */
    public static function selectTableAutoIncrement($tableName) {
        $rs = static::selectTablesInfo($tableName);
        return empty($rs['AUTO_INCREMENT']) ? 0 : $rs['AUTO_INCREMENT'];
    }

    /**
     * 获取表大小
     * @param $tableName
     * @return int|string
     */
    public static function selectTableSize($tableName) {
        $rs = static::selectTablesInfo($tableName);
        return empty($rs['DATA_LENGTH']) ? 0 : static::formatNumber($rs['DATA_LENGTH']);
    }

    /**
     * 获取表详细信息
     * @param $tableName
     * @return mixed
     */
    public static function selectTablesInfo($tableName) {
        static $tableNames;
        if (!isset($tableNames[$tableName])) {
            $tableName = dbHelper::tableName($tableName);
            $dbname = Lev::$app['db']['dbname'];
            $sql = "SELECT * FROM information_schema.TABLES WHERE table_schema='{$dbname}' AND table_name='{$tableName}'";
            $tableNames[$tableName] = dbHelper::findOne($sql);
        }
        return $tableNames[$tableName];
    }

    /**
     * 格式化数字
     * @param $num
     * @return string
     */
    public static function formatNumber($num) {
        if ($num <1000) $str = $num.'B';
        elseif ($num <1000000) $str = round($num/1024, 2).'KB';
        elseif ($num <1000000000) $str = round($num/1024/1024, 2).'MB';
        else $str = round($num/1024/1024/1024, 2).'GB';
        return $str;
    }

}