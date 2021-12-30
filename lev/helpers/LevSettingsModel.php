<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-07 22:45
 *
 * 项目：upload  -  $  - LevModulesModel.php
 *
 * 作者：liwei 
 */

namespace lev\helpers;

use Lev;
use lev\base\Migrationv;
use lev\base\Modelv;

!defined('INLEV') && exit('Access Denied LEV');


class LevSettingsModel extends Modelv
{

    public static function tableName($val = '', $prefix = true) {
        return parent::tableName($val ?: '{{%lev_settings}}', $prefix);
    }

    public static function getColumns() {
        return dbHelper::getTableColumns(static::tableName());
    }

    public static function getModuleSettingsInfo($iden, $key = '', $classify = '') {
        $where = $classify ? " AND classify='$classify' " : '';
        return static::findAll("moduleidentifier='$iden' $where ORDER BY displayorder ASC", $key);
    }

    public static function getModuleTabSettings($iden, $classify = '', $keyfield = '') {
        $where = $classify ? " AND classify='$classify' " : '';
        return static::findAll("moduleidentifier='$iden' $where AND status>0 ORDER BY status ASC, displayorder ASC", $keyfield);
    }

    public static function getModuleSetSettings($iden, $classify = '') {
        $where = $classify ? " AND classify='$classify' " : '';
        return static::findAll("moduleidentifier='$iden' $where AND status=0 ORDER BY displayorder ASC");
    }

    public static function field_scoretype() {
        return ScoreHelper::scoretypes();
    }
    public static function setscoretype() {
        return ScoreHelper::scoretypes();
    }
    public static function setscoretypesyy() {
        return ScoreHelper::scoretypesyy();
    }

    public static function tabClassify($iden) {
        $mudInfo = ModulesHelper::getModuleInfo($iden);
        $tabs = Lev::getSettings($mudInfo['settings'], 'dropTables');
        $tabClassify = [];
        if ($tabs) {
            foreach ($tabs as $v) {
                $tab = Modelv::quoteTableName(Modelv::preTableName($v), false);
                $tabClassify[$tab] = $tab;
            }
        }
        if ($iden === 'levmodules') {
            $tabClassify += static::baseTabClassify();
        }
        return $tabClassify;
    }
    public static function baseTabClassify() {
        $tabClassify = [];

        $tabs = Migrationv::getLevBaseTables();
        foreach ($tabs as $v) {
            $tab = Modelv::quoteTableName(Modelv::preTableName($v), false);
            $tabClassify[$tab] = $tab;
        }
        return $tabClassify;
    }

    /**
     * @param array $fieldInfo
     * @return array
     */
    public static function setclassify($fieldInfo = []) {
        $iden = Lev::stripTags(Lev::GPv('iden')) ?: Lev::$app['iden'];
        $classify = ModulesHelper::getClassify($iden) + static::tabClassify($iden);
        return ModulesHelper::getSetClassify($iden, $classify);
    }
}