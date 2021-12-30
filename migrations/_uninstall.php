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

namespace modules\levs\migrations;

!defined('INLEV') && exit('Access Denied LEV');

use Lev;
use lev\base\Adminv;
use lev\base\Migrationv;
use lev\helpers\dbHelper;
use lev\helpers\ModulesHelper;
use lev\helpers\SettingsHelper;

Adminv::checkAccess();

class _uninstall extends Migrationv
{

    public static function actionUninstall() {
        static::deleteTables();

        static::deleteModuleInfo();
        static::deleteModuleSettings();

        ModulesHelper::deleteModuleFile(Lev::$app['iden']);

        static::dropLevTabs();
    }

    private static function deleteModuleSettings() {
        if (dbHelper::existsTable(SettingsHelper::tableName())) {
            SettingsHelper::delete(['moduleidentifier'=>Lev::$app['iden']]);
        }
    }

    private static function deleteModuleInfo() {
        if (dbHelper::existsTable(ModulesHelper::tableName())) {
            ModulesHelper::delete(['identifier'=>Lev::$app['iden']]);
        }
    }

    private static function deleteTables()
    {
        if (!dbHelper::existsTable(ModulesHelper::tableName())) {
            return;
        }
        $moduleInfo = ModulesHelper::getModuleInfo(Lev::$app['iden']);
        $settings = Lev::getSettings($moduleInfo['settings']);
        if ($settings['dropTables']) {
            foreach ($settings['dropTables'] as $tab) {
                $tab = trim($tab);
                if ($tab && $tab != '{{%}}') {
                    dbHelper::executeSql("DROP TABLE IF EXISTS ".dbHelper::tableName($tab));
                }
            }
        }
    }

    private static function dropLevTabs() {
        if (!ModulesHelper::findOne(1)) {
            dbHelper::executeSql("DROP TABLE IF EXISTS ".ModulesHelper::tableName());
            dbHelper::executeSql("DROP TABLE IF EXISTS ".SettingsHelper::tableName());
        }
    }
}