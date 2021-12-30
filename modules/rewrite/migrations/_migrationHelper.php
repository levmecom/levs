<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-10-02 20:38
 *
 * 项目：rm  -  $  - _migrationHelper.php
 *
 * 作者：liwei 
 */

namespace modules\levs\modules\rewrite\migrations;

use Lev;
use lev\base\Migrationv;
use lev\helpers\cacheFileHelpers;
use lev\helpers\dbHelper;
use lev\helpers\ModulesHelper;
use lev\helpers\SettingsHelper;
use modules\levs\modules\ftp\helpers\ftpZipHelper;

!defined('INLEV') && exit('Access Denied LEV');

class _migrationHelper extends Migrationv
{

    //更新模块信息
    public static function updateModuleInfo($newModInfo = []) {
        empty($newModInfo) && $newModInfo = ModulesHelper::formatModuleInfo(_install::getModuleInfo());

        $newModInfo['identifier'] = Lev::$app['iden'];
        $newModInfo['versiontime'] = Lev::$app['timestamp'];

        $modInfo = ModulesHelper::getModuleInfo(Lev::$app['iden']);
        if ($modInfo) {
            ModulesHelper::update($newModInfo, ['id'=>$modInfo['id']]);
        }else {
            $newModInfo['addtime'] = Lev::$app['timestamp'];
            ModulesHelper::insert($newModInfo);
        }
    }

    public static function updateSettings() {
        if (is_file($file = __DIR__.'/data/settings.php')) {
            $rows = include_once $file;
            if ($rows) {
                $mysets = SettingsHelper::getModuleSettingsInfo(Lev::$app['iden'], 'inputname');
                foreach ($rows as $v) {
                    if (!empty($v['_delete']) && $v['_delete'] === true) {
                        SettingsHelper::delete(['inputname'=>$v['inputname'], 'moduleidentifier'=>Lev::$app['iden']]);
                    }else {
                        unset($v['id']);
                        $v['moduleidentifier'] = Lev::$app['iden'];
                        $data = SettingsHelper::safeColumns($v);
                        if ($data) {
                            if (isset($mysets[$v['inputname']])) {
                                unset($data['inputvalue'], $data['addtime']);
                                SettingsHelper::update($data, ['id' => $mysets[$v['inputname']]['id']]);
                            } else {
                                SettingsHelper::insert($data);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     *  建议不要在此使用大数据更新
     *  自动识别data目录下的备份数据并插入新数据
     */
    public static function insertNewData($pk = 'id')
    {
        $initDataTables = glob(__DIR__ . '/data/pre_*/');
        if (empty($initDataTables)) {
            return;
        }
        ini_set('memory_limit', '-1');
        $tablePreFix = Lev::$app['db']['prefix'];
        foreach ($initDataTables as $v) {
            $tabFiles = glob(rtrim($v, '/') . '/*.php');
            if (!empty($tabFiles)) {
                foreach ($tabFiles as $k => $r) {
                    $filesize = filesize($r);
                    if ($filesize && $filesize < 30 * 1024 * 1024) {//文件大小小于30M
                        $rows = include_once $r;
                        if ($rows) {
                            $name = basename($r);
                            $tables = explode('_', $name);
                            $tablePreFix != 'pre_' && $tables[0] = rtrim($tablePreFix, '_');
                            $tableName = implode('_', array_slice($tables, 0, -1));
                            if (dbHelper::existsTable($tableName)) {
                                static::doInsertNewData($tableName, $rows, $pk, $k);
                            }
                        }
                    }

                }
            }
        }
    }
    public static function doInsertNewData($tableName, $data, $pk, $k) {
        static $ckDatas;
        $key = $tableName . '_' . $k;
        if (!isset($ckDatas[$key])) {
            $tableName === ModulesHelper::tableName() && ($pk = 'code');
            $pks = Lev::getArrayColumn($data, [$pk]);
            if ($pks) {
                $instr = "'".implode("','", $pks)."'";
                $sql = "SELECT $pk FROM $tableName WHERE $pk IN ($instr)";
                $ckDatas[$key] = dbHelper::findAll($sql, [], $pk);
            }else {
                $ckDatas[$key] = [];
            }
        }
        $preNotTable = substr($tableName, strlen(Lev::$app['db']['prefix']));
        foreach ($data as $v) {
            $v = dbHelper::safeColumns($tableName, $v);
            !isset($ckDatas[$key][$v[$pk]]) && dbHelper::insert($preNotTable, $v);
        }
    }

    public static function isLevDev() {
        return is_file(__DIR__ . '/data/_lev_dev.bin');
    }
    public static function unlinkv($file) {
        return !static::isLevDev() && @unlink($file);
    }
    public static function deleteInstallFile() {
        if (!static::isLevDev() || is_file(__DIR__ . '/data/.force.del.bin')) {
            @unlink(__DIR__ . '/_migrationHelper.php') &&
            is_file($file = __DIR__ . '/_install.php') && @unlink($file);
            is_file($file = __DIR__ . '/_update.php') && @unlink($file);
            is_file($file = __DIR__ . '/data/mysql.php') && @unlink($file);
            is_file($file = __DIR__ . '/data/settings.php') && @unlink($file);
            is_dir($dir = __DIR__ . '/data') && cacheFileHelpers::rmdirv($dir);

            //is_file($file = dirname(__DIR__) . '/discuz_plugin_levstore.xml') && @unlink($file);
            //is_file($file = dirname(__DIR__) . '/install.php') && @unlink($file);
            //is_file($file = dirname(__DIR__) . '/upgrade.php') && @unlink($file);
        }

        ModulesHelper::isInstallModule('ftp') && ftpZipHelper::opCache(Lev::$app['iden'], '');
    }

}