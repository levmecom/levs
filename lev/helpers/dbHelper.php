<?php

/*
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-07 20:08
 *
 * 项目：upload  -  $  - dbHelper.php
 *
 * 作者：liwei 
 */

namespace lev\helpers;

use Lev;
use lev\base\Modelv;

!defined('INLEV') && exit('Access Denied LEV');

class BaseDB {

    /**
     * @return DBVHelper|lev\dz\dzUserHelper
     */
    public static function getDB() {
        return Lev::getDB();
    }

    public static function getDBDriver() {
        return Lev::getDB()->object();
    }

    /**
     * @param $sql
     * @param array $arg
     * @param string $keyfield
     * @param bool $silent
     * @return array
     * @see DB::fetch_all()
     */
    public static function findAll($sql, $arg = array(), $keyfield = '', $silent = false) {
        return static::getDB()->fetch_all($sql, $arg, $keyfield, $silent);
    }

    public static function findOne($sql) {
        return static::getDB()->fetch_first($sql);
    }

    public static function insert($table, $data, $returnInsertId = false) {
        return static::getDB()->insert($table, $data, $returnInsertId);
    }

    public static function update($table, $data, $condition = '') {
        return static::getDB()->update($table, $data, $condition);
    }

    /**
     * 清空表数据
     * @param $table
     * @return mixed|null
     */
    public static function truncateTable($table) {
        return static::getDB()->query("TRUNCATE TABLE ".static::tableName($table));
    }

    /**
     * 无限加载数据
     * @param $where
     * @param array $order eg: ['displayorder ASC', 'id ASC']
     * @param int $page
     * @param int $limit
     * @return array
     */
    public static function pages($table, $where, $limit = 20, $order = [], $field = '*', $page = 0, $keyfield = '') {
        Modelv::$tableName = $table;
        return Modelv::pages($where, $limit, $order, $field, $page, $keyfield);
    }

    /**
     * 分页按钮
     * @param $where
     * @param int $limit
     * @param array $order
     * @param int $buttonNum
     * @param string $url
     * @param string $field
     * @param int $page
     * @return array
     */
    public static function pageButtons($table, $where, $limit = 20, $order = [], $buttonNum = 5, $url = '', $field = '*', $page = 0) {
        Modelv::$tableName = $table;
        return Modelv::pageButtons($where, $limit, $order, $buttonNum, $url, $field, $page);
    }

    /**
     * @param $table
     * @param $condition
     * @param int $limit
     * @return mixed
     */
    public static function delete($table, $condition, $limit = 0) {
        return static::getDB()->delete($table, $condition, $limit);
    }

    public static function tableName($tableName = '', $prefix = true) {
        return Modelv::tableName($tableName, $prefix);
    }

    public static function getModulesTableName($prefix = true) {
        return Modelv::quoteTableName('{{%lev_modules}}', $prefix);
    }

    public static function getSettingsTableName($prefix = true) {
        return Modelv::quoteTableName('{{%lev_settings}}', $prefix);
    }

    public static function getTableNames($likeTab) {
        static $tables;
        if (isset($tables[$likeTab])) return $tables[$likeTab];
        $tables[$likeTab] = [];
        $_tabs = static::getDB()->fetch_all("SHOW TABLES LIKE '{$likeTab}%'");
        if ($_tabs) {
            foreach ($_tabs as $tabname) {
                $tables[$likeTab][] = is_array($tabname) ? reset($tabname) : $tabname;
            }
        }
        return $tables[$likeTab];
    }

    /**
     * 检查表是否存在当前库中
     * @param $tablename
     * @return array
     */
    public static function existsTable($tableName)
    {
        static $tables;
        $tableName = static::tableName($tableName);
        return $tables[$tableName] = isset($tables[$tableName]) ? $tables[$tableName] :
            (!$tableName ? '' : static::getDB()->fetch_first("SHOW TABLES LIKE '{$tableName}'"));
    }

    public static function safeColumns($tableName, $columns) {
        $tabColumns = dbHelper::getTableColumns($tableName);
        if ($tabColumns) {
            foreach ($columns as $field => $value) {
                if (!isset($tabColumns[$field])) unset($columns[$field]);
            }
        }
        return $columns;
    }

    public static function getColumnsComment($tableName) {
        static $tabs;
        if (isset($tabs[$tableName])) {
            return $tabs[$tableName];
        }
        $schema = static::getTableSchema($tableName);
        $columns = [];
        if ($schema) foreach ($schema as $v) {
            $columns[$v['Field']] = $v['Comment'] ?: ucfirst($v['Field']);
        }
        return $tabs[$tableName] = $columns;
    }

    public static function getTableColumns($tableName) {
        static $tabs;
        if (isset($tabs[$tableName])) {
            return $tabs[$tableName];
        }
        $schema = static::getTableSchema($tableName);
        $columns = [];
        if ($schema) foreach ($schema as $v) {
            $columns[$v['Field']] = $v['Type'];
        }
        return $tabs[$tableName] = $columns;
    }

    public static function existsField($tableName, $field) {
        $tableName = static::tableName($tableName);
        return static::getDB()->fetch_first("SHOW FULL COLUMNS FROM {$tableName} WHERE Field LIKE '{$field}'");
    }

    /**
     * @param $tableName
     * @return array
     */
    public static function getTableSchema($tableName) {
        static $tables;
        return $tables[$tableName] = isset($tables[$tableName]) ? $tables[$tableName] :
            static::getDB()->fetch_all("SHOW FULL COLUMNS FROM ".Modelv::tableName($tableName));
    }

    /**
     * 转码成数据库编码
     * @param $data
     * @return array|string
     */
    public static function setDataToCharset($data) {
        if ($data && stripos(Lev::$app['charset'], 'gbk') === 0) {
            $data = Lev::iconvs($data, 'UTF-8', 'GBK');
        }
        return $data;
    }

    /**
     * 数据库得到的数据转码成页面编吗
     * @param $data
     * @return array|string
     */
    public static function getDataToCharset($data) {
        if ($data && stripos(Lev::$app['charset'], 'gbk') === 0) {
            $data = Lev::iconvs($data, 'GBK', 'UTF-8');
        }
        return $data;
    }

    public static function executeSql($sql) {
        $sql = static::setDataToCharset($sql);

        $tablepre = Lev::$app['db']['prefix'];
        $dbcharset = Lev::$app['db']['charset'];

        $sql = str_replace(array(' cdb_', ' `cdb_', ' pre_', ' `pre_'), array(' {tablepre}', ' `{tablepre}', ' {tablepre}', ' `{tablepre}'), $sql);
        $sql = str_replace(array(' {{%', ' `{{%', '}}'), array(' {tablepre}', ' `{tablepre}', ''), $sql);
        $sql = str_replace("\r", "\n", str_replace(array(' {tablepre}', ' `{tablepre}'), array(' '.$tablepre, ' `'.$tablepre), $sql));

        $ret = array();
        $num = 0;
        foreach(explode(";\n", trim($sql)) as $query) {
            $queries = explode("\n", trim($query));
            foreach($queries as $query) {
                $ret[$num] .= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
            }
            $num++;
        }
        unset($sql);

        foreach($ret as $query) {
            $query = trim($query);
            if($query) {

                if(substr($query, 0, 12) == 'CREATE TABLE') {
                    $name = preg_replace("/CREATE TABLE ([a-z0-9_]+) .*/is", "\\1", $query);
                    static::getDB()->query(static::formartCreateTable($query, $dbcharset));

                } else {
                    static::getDB()->query($query);
                }

            }
        }
    }

    public static function formartCreateTable($sql, $dbcharset) {
        if (static::getDBDriver()->version() > '4.1' && stripos($sql, ' TYPE=') === false) {
            if (stripos($sql, ' CHARACTER SET ') !== false || stripos($sql, ' CHARSET=') !== false)
            return $sql;
        }
        $type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
        $type = in_array($type, array('MYISAM', 'HEAP', 'INNODB')) ? $type : 'MYISAM';
        return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
            (static::getDBDriver()->version() > '4.1' ? " ENGINE=$type DEFAULT CHARSET=$dbcharset" : " TYPE=$type");
    }
}

class dbHelper extends BaseDB {}