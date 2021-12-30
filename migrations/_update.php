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

//安装或更新完成自动删除此文件和data目录

namespace modules\levs\migrations;

!defined('INLEV') && exit('Access Denied LEV');


use Lev;
use lev\base\Adminv;
use lev\helpers\cacheFileHelpers;
use lev\helpers\dbHelper;
use lev\helpers\ModulesHelper;
use lev\widgets\inputs\inputsWidget;

Adminv::checkAccess();

class _update extends _migrationHelper
{

    public static function actionUpdate() {
        static::createInstallConfig();

        static::updateTableSchema();

        static::createLevsGate();

        static::updateModuleInfo();
        static::updateSettings();

        static::insertNewData();

        inputsWidget::setCaches();
        ModulesHelper::setCaches();

        static::deleteInstallFile();
    }

    public static function updateTableSchema() {
        //_install::createTables();

        $mudInfo = ModulesHelper::getModuleFileInfo('levs');
        if ($mudInfo['versiontime'] < strtotime('2021-12-05')) {
            $sql = \lev\base\Migrationv::getLevBaseTableCreateSql();
            $sql && dbHelper::executeSql($sql);

            $sql = "ALTER TABLE `{{%lev_users}}` ADD `groupid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户组ID' AFTER `id`";
            dbHelper::existsField('{{%lev_users}}', 'groupid') || dbHelper::executeSql($sql);

            $sql = "ALTER TABLE `{{%lev_users}}` ADD `adminid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理组ID' AFTER `id`";
            dbHelper::existsField('{{%lev_users}}', 'adminid') || dbHelper::executeSql($sql);

            $sql = "ALTER TABLE `{{%lev_users}}` ADD `username` varchar(32) NOT NULL COMMENT '用户名' AFTER `id`";
            dbHelper::existsField('{{%lev_users}}', 'username') || dbHelper::executeSql($sql);

            $sql = "ALTER TABLE `{{%lev_users}}` ADD `city` varchar(220) NOT NULL DEFAULT '' COMMENT '城市' AFTER `rname`";
            dbHelper::existsField('{{%lev_users}}', 'city') || dbHelper::executeSql($sql);

            $sql = "ALTER TABLE `{{%lev_users}}` ADD `prov` varchar(220) NOT NULL DEFAULT '' COMMENT '省份' AFTER `rname`";
            dbHelper::existsField('{{%lev_users}}', 'prov') || dbHelper::executeSql($sql);

            $sql = "ALTER TABLE `{{%lev_users}}` ADD `country` varchar(220) NOT NULL DEFAULT '' COMMENT '国家' AFTER `rname`";
            dbHelper::existsField('{{%lev_users}}', 'country') || dbHelper::executeSql($sql);

            $sql = "ALTER TABLE `{{%lev_users_login}}` ADD `status` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态' AFTER `safecode`";
            dbHelper::existsTable('{{%lev_users_login}}') &&
            !dbHelper::existsField('{{%lev_users_login}}', 'status') && dbHelper::executeSql($sql);
        }

        $sql = "ALTER TABLE `{{%lev_users}}` ADD `ip` varchar(220) NOT NULL DEFAULT '' COMMENT 'IP' AFTER `safepwd`";
        dbHelper::existsField('{{%lev_users}}', 'ip') || dbHelper::executeSql($sql);

    }

    public static function createInstallConfig() {
        if (!is_file($file = dirname(__DIR__) . '/runtime/.install.lock')) {
            cacheFileHelpers::mkdirv(dirname($file));
            file_put_contents($file, date('Y-m-d H:i:s'));
        }
    }

    public static function createLevsGate() {

        if (Lev::$app['isDiscuz']) {
            is_file($__file = __DIR__ . '/data/levs_discuz.php') &&
            file_put_contents(Lev::$aliases['@webroot'] . '/levs.php', str_ireplace('!defined(', '//!defined(', file_get_contents($__file)));
        }

        if (!Lev::isDeveloper('levs')) {
            if (is_file($__file = dirname(__DIR__) . '/lev/Lev.php') && $data = file_get_contents($__file)) {
                if (strpos($data, '//INI_SET(\'display_errors\', 1);ERROR_REPORTING(') === false) {
                    file_put_contents($__file, str_replace('INI_SET(\'display_errors\', 1);ERROR_REPORTING(', '//INI_SET(\'display_errors\', 1);ERROR_REPORTING(', $data), LOCK_EX);
                }
            }
        }
    }


}