<?php

/*
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-08 20:18
 *
 * 项目：upload  -  $  - dzDBHelper.php
 *
 * 作者：liwei 
 */

namespace lev\dz;

use DB;
use Lev;
use lev\base\DBInterface;
use lev\helpers\dbHelper;

!defined('INLEV') && exit('Access Denied LEV');

class dzDBHelper extends DB implements DBInterface
{

    public static function getUserInfo($uid = 0) {
        return !$uid ? [] :
            static::fetch_first("SELECT uid,username,email,credits,regdate,groupid,adminid FROM ".DB::table('common_member')." WHERE uid=$uid");
    }

    public static function getUsers($uids) {
        is_array($uids) && $uids = implode(',', $uids);
        return $uids ? static::fetch_all("SELECT uid,username FROM ".DB::table('common_member')." WHERE uid IN ($uids)", [], 'uid') : [];
    }

    public static function getUserDetail($uid = 0) {
        return !$uid ? [] :
            static::fetch_first("SELECT uid,realname,resideprovince as prov,residecity as city,qq FROM ".DB::table('common_member_profile')." WHERE uid=$uid");
    }

    public static function getPluginId($iden) {
        $sql = "SELECT * FROM " . DB::table('common_plugin') . " WHERE identifier='" . $iden . "'";
        $pluginid = DB::result_first($sql);
        return $pluginid;
    }

    public static function getPluginIden($id) {
        $sql = "SELECT * FROM " . DB::table('common_plugin') . " WHERE pluginid='" . $id . "'";
        $res = DB::fetch_first($sql);
        return empty($res['identifier']) ? '' : $res['identifier'];
    }

    /**
     * 获取用户积分
     * @param $uid
     * @return array
     */
    public static function getUserScore($uid) {
        return static::fetch_first("SELECT * FROM ".DB::table('common_member_count')." WHERE uid='{$uid}'");
    }

    public static function getGroups($field = '*') {
        return dzDBHelper::fetch_all("SELECT $field FROM ". dzDBHelper::table('common_usergroup')." WHERE 1");
    }

    /**
     * 论坛非隐藏版块
     * @param array $fidArr
     * @return array
     */
    public static function forumLists($fidArr = []) {
        $fidArr && $fidArr = array_unique($fidArr);
        $where = $fidArr ? 'fid IN('.implode(', ', array_map(function ($v) { return intval($v); }, $fidArr)).') AND' : '';
        $sql = "SELECT * FROM ". DB::table('forum_forum')." WHERE $where type='forum' AND fup=1 AND status=1 ORDER BY displayorder ASC";
        $res = static::fetch_all($sql);//Lev::$db->
        return $res;
    }

    public static function threadLists($fidArr = [], $limit = 10) {
        $fidArr && $fidArr = array_unique($fidArr);
        $where = $fidArr ? 'fid IN('.implode(', ', array_map(function ($v) { return intval($v); }, $fidArr)).') AND' : '';
        $sql = "SELECT * FROM ". DB::table('forum_thread')." WHERE $where displayorder>=0 ORDER BY lastpost DESC LIMIT $limit";
        $res = static::fetch_all($sql);
        return $res;
    }

    public static function delete($table, $condition, $limit = 0, $unbuffered = true)
    {
        return parent::delete($table, dbHelper::setDataToCharset($condition), $limit, $unbuffered); // TODO: Change the autogenerated stub
    }

    public static function insert($table, $data, $return_insert_id = false, $replace = false, $silent = false)
    {
        return parent::insert($table, dbHelper::setDataToCharset($data), $return_insert_id, $replace, $silent); // TODO: Change the autogenerated stub
    }

    public static function update($table, $data, $condition = '', $unbuffered = false, $low_priority = false)
    {
        return parent::update($table, dbHelper::setDataToCharset($data), dbHelper::setDataToCharset($condition), $unbuffered, $low_priority); // TODO: Change the autogenerated stub
    }

    public static function fetch_all($sql, $arg = array(), $keyfield = '', $silent = false)
    {
        $data = parent::fetch_all(dbHelper::setDataToCharset($sql), $arg, $keyfield, $silent); // TODO: Change the autogenerated stub
        return dbHelper::getDataToCharset($data);
    }

    /**
     * @param $sql
     * @param array $arg
     * @param bool $silent
     * @return array|string
     */
    public static function fetch_first($sql, $arg = array(), $silent = false)
    {
        $data = parent::fetch_first(dbHelper::setDataToCharset($sql), $arg, $silent); // TODO: Change the autogenerated stub
        return dbHelper::getDataToCharset($data);
    }

}
