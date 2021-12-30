<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-07 20:56
 *
 * 项目：upload  -  $  - WidgetvInterface.php
 *
 * 作者：liwei 
 */

namespace lev\base;

!defined('INLEV') && exit('Access Denied LEV');


interface DBInterface
{

    /**
     * 连接池
     * @return mixed
     */
    public static function object();

    /**
     * 查询
     * @param $sql
     * @return mixed
     */
    public static function query($sql);

    /**
     * 查询多条
     * @param $sql
     * @return array
     */
    public static function fetch_all($sql);

    /**
     * 查询一条
     * @param $sql
     * @return array
     */
    public static function fetch_first($sql);

    /**
     * 插入一条
     * @param $table
     * @param $data
     * @return mixed
     */
    public static function insert($table, $data);

    /**
     * 更新
     * @param $table
     * @param $data
     * @param string $condition
     * @return mixed
     */
    public static function update($table, $data, $condition);

    /**
     * 删除
     * @param $table
     * @param $condition
     * @return mixed
     */
    public static function delete($table, $condition);

    public static function getUserInfo($uid = 0);

    public static function getUsers($uids);
}