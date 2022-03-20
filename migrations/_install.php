<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-07 12:16
 *
 * 项目：upload  -  $  - install.php
 *
 * 作者：liwei 
 */

//安装完成自动删除此文件和data目录

namespace modules\levs\migrations;

!defined('INLEV') && exit('Access Denied LEV');


use Lev;
use lev\base\Adminv;
use lev\base\Migrationv;
use lev\controllers\SupermanController;
use lev\helpers\cacheFileHelpers;
use lev\helpers\dbHelper;
use lev\helpers\ModulesHelper;
use lev\helpers\SettingsHelper;
use lev\widgets\inputs\inputsWidget;

Adminv::checkAccess();

class _install extends Migrationv
{

    public static function actionInstall() {
        _update::createInstallConfig();

        static::createTables();

        _update::createLevsGate();

        _update::updateModuleInfo();
        _update::updateSettings();

        //static::batchInsertSettings();
        //static::batchInsertData();

        _update::insertNewData();

        inputsWidget::setCaches();
        ModulesHelper::setCaches();

        static::mergeInstall();

        _update::deleteInstallFile();
    }

    public static function mergeInstall() {
        if (is_file($file = __DIR__ . '/data/merge_install.php')) {
            $idens = include $file;
            if ($idens) {
                foreach ($idens as $iden) {
                    SupermanController::InstallOrUpdateModule($iden, '');
                }
            }
        }
    }

    public static function getModuleInfo() {
        static $moduleinfo;
        $moduleinfo = isset($moduleinfo) ? $moduleinfo : (is_file($file = __DIR__ . '/data/moduleinfo.php') ? include $file : []);
        $moduleinfo['settings'] = Lev::getSettings($moduleinfo['settings']);
        $moduleinfo['settings']['dropTables'] = static::getTables();
        $moduleinfo['settings'] = Lev::setSettings($moduleinfo['settings']);
        return $moduleinfo;
    }

    private static function batchInsertSettings() {
        if (is_file($file = __DIR__.'/data/settings.php')) {
            //ini_set('memory_limit', '-1');
            $rows = include_once $file;
            if ($rows) {
                $columns = [];
                foreach ($rows as $k => $v) unset($rows[$k]['id']);
                foreach ($rows[0] as $field => $v) {
                    $columns[] = $field;
                }
                $chunk = array_chunk($rows, 3000);
                foreach ($chunk as $rows) {
                    $sql = parent::batchInsert(SettingsHelper::tableName(), $columns, $rows);
                    $sql && dbHelper::executeSql($sql);
                }
            }

        }
    }

    /**
     *  自动识别data目录下的备份数据并导入
     */
    private static function batchInsertData()
    {
        $initDataTables = glob(__DIR__ . '/data/pre_*/');
        if (empty($initDataTables)) {
            return;
        }
        //ini_set('memory_limit', '-1');
        $tablePreFix = Lev::$app['db']['prefix'];
        foreach ($initDataTables as $v) {
            $tabFiles = glob(rtrim($v, '/') . '/*.php');
            if (!empty($tabFiles)) {
                foreach ($tabFiles as $r) {
                    $filesize = filesize($r);
                    if (is_file($r) && $filesize && $filesize < 30 * 1024 * 1024) {//文件大小小于30M
                        $rows = include $r;
                        if ($rows) {
                            $name = basename($r);
                            $tables = explode('_', $name);
                            $tablePreFix != 'pre_' && $tables[0] = rtrim($tablePreFix, '_');
                            $tableName = implode('_', array_slice($tables, 0, -1));
                            if (dbHelper::existsTable($tableName)) {
                                $columns = [];
                                foreach ($rows[0] as $field => $d) {
                                    $columns[] = $field;
                                }
                                $chunk = array_chunk($rows, 500);
                                foreach ($chunk as $rows) {
                                    $sql = parent::batchInsert($tableName, $columns, $rows);
                                    $sql && dbHelper::executeSql($sql);
                                }
                            }
                        }
                    }

                }
            }
        }
    }

    public static function createTables() {
        $sql = static::getCreateTableMysql();
        $sql && dbHelper::executeSql($sql);
    }

    /**
     * 获取Mysql数据库表安装sql
     * @param bool $tabName
     * @return mixed|string
     */
    public static function getCreateTableMysql() {
        $sqlArr = static::MysqlInfo();
        return isset($sqlArr[0]) ? $sqlArr[0] : '';
    }

    public static function getTables() {
        $sqlArr = static::MysqlInfo();
        return isset($sqlArr[1]) ? $sqlArr[1] : [];
    }

    public static function MysqlInfo() {
        static $info;
        return isset($info) ? $info : (is_file($file = __DIR__ . '/data/mysql.php') ? include $file : []);
    }
}