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

namespace modules\levs\modules\composer\migrations;

!defined('INLEV') && exit('Access Denied LEV');


use Lev;
use lev\base\Adminv;
use lev\helpers\ModulesHelper;
use lev\widgets\inputs\inputsWidget;

Adminv::checkAccess();

class _update extends _migrationHelper
{

    public static function actionUpdate() {
        static::updateTableSchema();

        static::updateModuleInfo();
        static::updateSettings();

        static::insertNewData();

        inputsWidget::setCaches();
        ModulesHelper::setCaches();

        static::checkfile();

        static::deleteInstallFile();
    }

    public static function updateTableSchema() {
        //_install::createTables();

        //$sql = "ALTER TABLE `pre_levsd_guess_log` ADD `award` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '奖金额度' AFTER `guess`; ";
        //dbHelper::existsField('pre_levsd_guess_log', 'award') || dbHelper::executeSql($sql);

    }

}