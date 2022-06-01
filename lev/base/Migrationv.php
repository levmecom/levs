<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-07 12:12
 *
 * 项目：upload  -  $  - Migrationv.php
 *
 * 作者：liwei 
 */


namespace lev\base;

use Lev;

!defined('INLEV') && exit('Access Denied LEV');

class Migrationv
{

    /**
     * Generates a batch INSERT SQL statement.
     *
     * For example,
     *
     * ```php
     * $sql = Migrationv::batchInsert('user', ['name', 'age'], [
     *     ['Tom', 30],
     *     ['Jane', 20],
     *     ['Linda', 25],
     * ]);
     * ```
     *
     * Note that the values in each row must match the corresponding column names.
     *
     * The method will properly escape the column names, and quote the values to be inserted.
     *
     * @param string $table the table that new rows will be inserted into.
     * @param array $columns the column names
     * @param array|\Generator $rows the rows to be batch inserted into the table
     * @return string the batch INSERT SQL statement
     */
    public static function batchInsert($table, $columns, $rows)
    {
        if (empty($rows)) {
            return '';
        }

        $values = [];
        foreach ($rows as $row) {
            $vs = [];
            foreach ($row as $i => $value) {
                if (is_string($value)) {
                    $value = static::quoteValue($value);
                } elseif (is_float($value)) {
                    // ensure type cast always has . as decimal separator in all locales
                    $value = Lev::floatToString($value);
                } elseif ($value === false) {
                    $value = 0;
                } elseif ($value === null) {
                    $value = 'NULL';
                }
                $vs[] = $value;
            }
            $values[] = '(' . implode(', ', $vs) . ')';
        }
        if (empty($values)) {
            return '';
        }

        $columns = static::quoteColumnName($columns);

        return 'INSERT INTO ' . static::quoteTableName($table) . ' (' . implode(', ', $columns) . ') VALUES ' . implode(', ', $values);
    }

    public static function quoteTableName($tableName = '', $prefix = true) {
        if ($tableName && $tableName != '{{%}}' && strpos($tableName, '{{%') === 0 && substr($tableName, -2) === '}}') {
            $tableName = ($prefix ? Lev::$app['db']['prefix'] : '').str_replace('%', '', substr($tableName, 3, -2));
        }
        return $tableName;
    }

    public static function preTableName($tableName, $pre = 'pre_') {
        return strpos($tableName, $pre) === 0 ? '{{%'. substr($tableName, strlen($pre)) . '}}' : $tableName;
    }

    public static function quoteColumnName($field) {
        if (is_array($field)) {
            foreach ($field as $k => $v) {
                $field[$k] = static::quoteColumnName($v);
            }
        } else {
            if (strpos($field, '`') !== false)
                $field = str_replace('`', '', $field);
            $field = '`' . $field . '`';
        }
        return $field;
    }

    /**
     * Quotes a string value for use in a query.
     * Note that if the parameter is not a string, it will be returned without change.
     * @param string $str string to be quoted
     * @return string the properly quoted string
     * @see https://secure.php.net/manual/en/function.PDO-quote.php
     */
    public static function quoteValue($str)
    {
        if (!is_string($str)) {
            return $str;
        }

        // the driver doesn't support quote (e.g. oci)
        return "'" . addcslashes(str_replace("'", "''", $str), "\000\n\r\\\032") . "'";
    }

    public static function setMyISAMtableOptions() {
        return ' CHARACTER SET '.Lev::$app['db']['charset'].' ENGINE=MyISAM ';
    }

    public static function setInnoDBtableOptions() {
        return ' CHARACTER SET '.Lev::$app['db']['charset'].' ENGINE=InnoDB ';
    }

    public static function getLevBaseTableCreateSql($tableOptions = null) {

        $charset = Lev::$app['db']['charset'];
        $tableOptions === null &&
        $tableOptions = ' CHARACTER SET '.$charset.' ENGINE=MyISAM';
        $tableOptionsInnoDB = ' CHARACTER SET '.$charset.' ENGINE=InnoDB';
        //$tableOptionsMyISAM = ' CHARACTER SET '.$charset.' ENGINE=MyISAM';

        //DROP TABLE IF EXISTS `pre_lev_modules`;
        return <<<EOF

CREATE TABLE IF NOT EXISTS `{{%lev_users_login}}` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户UID',
  `username` char (32) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` char (32) NOT NULL DEFAULT '' COMMENT '用户密码',
  `safecode` char (8) NOT NULL DEFAULT '' COMMENT '安全码',
  `status` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态',
  `addtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) {$tableOptionsInnoDB} COMMENT '用户登陆子表';

CREATE TABLE IF NOT EXISTS `{{%lev_users}}` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户UID',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名',
  `groupid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户组ID',
  `adminid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '管理组ID',
  `wxopenid` varchar(220) NOT NULL DEFAULT '' COMMENT '微信公众号开放ID',
  `alipayuserid` varchar(220) NOT NULL DEFAULT '' COMMENT '支付宝开放ID',
  `cnymoney` varchar(220) NOT NULL DEFAULT '' COMMENT '人民币余额',
  `rname` varchar(220) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `country` varchar(220) NOT NULL DEFAULT '' COMMENT '国家',
  `city` varchar(220) NOT NULL DEFAULT '' COMMENT '城市',
  `prov` varchar(220) NOT NULL DEFAULT '' COMMENT '省份',
  `cardno` varchar(220) NOT NULL DEFAULT '' COMMENT '身份证号',
  `mobile` varchar(220) NOT NULL DEFAULT '' COMMENT '手机号',
  `email` varchar(220) NOT NULL DEFAULT '' COMMENT '邮箱Email',
  `qq` varchar(220) NOT NULL DEFAULT '' COMMENT 'QQ',
  `safepwd` varchar(220) NOT NULL DEFAULT '' COMMENT '二级安全密码',
  `ip` varchar(220) NOT NULL DEFAULT '' COMMENT 'IP',
  `settings` mediumtext NOT NULL COMMENT '通用设置',
  `status` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态',
  `uptime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `addtime` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) {$tableOptionsInnoDB};

CREATE TABLE IF NOT EXISTS `{{%lev_modules}}` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `typeid` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分类',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '模块名称',
  `identifier` varchar(220) NOT NULL DEFAULT '' COMMENT '唯一标识',
  `classdir` varchar(255) NOT NULL DEFAULT '',
  `descs` varchar(255) NOT NULL DEFAULT '' COMMENT '简短描述',
  `copyright` varchar(255) NOT NULL DEFAULT '' COMMENT '版权',
  `version` varchar(255) NOT NULL DEFAULT '' COMMENT '版本号',
  `versiontime` bigint(20) NOT NULL DEFAULT '0' COMMENT '版本时间号',
  `settings` mediumtext NOT NULL COMMENT '通用设置',
  `displayorder` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态',
  `uptime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `addtime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) $tableOptions;

CREATE TABLE IF NOT EXISTS `{{%lev_settings}}` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `moduleidentifier` varchar(220) NOT NULL DEFAULT '' COMMENT '模块标识符',
  `classify` varchar(32) NOT NULL DEFAULT '' COMMENT '设置分类',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `placeholder` text NOT NULL COMMENT '输入框内提示',
  `inputname` varchar(255) NOT NULL DEFAULT '' COMMENT '输入框名',
  `inputtype` varchar(255) NOT NULL DEFAULT '' COMMENT '输入框类型',
  `inputvalue` mediumtext NOT NULL COMMENT '输入框值',
  `settings` mediumtext NOT NULL COMMENT '通用设置',
  `displayorder` smallint(6) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态',
  `uptime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
  `addtime` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `moduleidentifier` (`moduleidentifier`)
) $tableOptions;

EOF;

    }

    public static function getLevBaseTables()
    {
        return [
            '{{%lev_users_login}}',
            '{{%lev_users}}',
            '{{%lev_modules}}',
            '{{%lev_settings}}',
        ];
    }
}