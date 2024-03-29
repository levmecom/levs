<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 *
 * 创建时间：2021-12-04 11:36:26
 *
 * 项目：/levs  -  $  - BaseLevUsersLoginModel.php
 *
 * 作者：{{AUTO GENERATE}}
 */

//此文件使用程序自动生成，下次生成时会覆盖，不建议修改。

namespace modules\levs\table\lev_users_login;

use Lev;
use lev\base\Modelv;
use lev\base\SubModelv;

!defined('INLEV') && exit('Access Denied LEV');


class BaseLevUsersLoginModel extends SubModelv
{

    public static $tableName = '{{%lev_users_login}}';

    public static function safeColumnsGen($columns)
    {
        $allColumns = static::allColumns();
        if (is_array($allColumns)) {
            foreach ($columns as $field => $value) {
                if (!isset($allColumns[$field])) unset($columns[$field]);
            }
        }
        return parent::safeColumnsGen($columns); // TODO: Change the autogenerated stub
    }

    public static function allColumns() {
        return array(
                'id'       => '用户UID',

                'username' => '用户名',

                'password' => '用户密码',

                'safecode' => '安全码',

                'status'   => '状态',

                'addtime'  => '添加时间',

        );
    }

    public static function inputs($iden = '', $keyfield = '', $classify = null)
    {
        //return parent::inputs($iden, $keyfield, $classify); // TODO: Change the autogenerated stub
        return array(

            'id' => array (
                    'moduleidentifier' => 'levs',
                    'classify'         => '',
                    'title'            => '用户UID',
                    'placeholder'      => '',
                    'inputname'        => 'id',
                    'inputtype'        => 'text',
                    'inputvalue'       => '',
                    'settings'         => '',
                    'displayorder'     => '0',
                    'status'           => '0',
            ),
            'username' => array (
                    'moduleidentifier' => 'levs',
                    'classify'         => '',
                    'title'            => '用户名',
                    'placeholder'      => '',
                    'inputname'        => 'username',
                    'inputtype'        => 'text',
                    'inputvalue'       => '',
                    'settings'         => '',
                    'displayorder'     => '0',
                    'status'           => '0',
            ),
            'password' => array (
                    'moduleidentifier' => 'levs',
                    'classify'         => '',
                    'title'            => '用户密码',
                    'placeholder'      => '',
                    'inputname'        => 'password',
                    'inputtype'        => 'text',
                    'inputvalue'       => '',
                    'settings'         => '',
                    'displayorder'     => '0',
                    'status'           => '0',
            ),
            'safecode' => array (
                    'moduleidentifier' => 'levs',
                    'classify'         => '',
                    'title'            => '安全码',
                    'placeholder'      => '',
                    'inputname'        => 'safecode',
                    'inputtype'        => 'text',
                    'inputvalue'       => '',
                    'settings'         => '',
                    'displayorder'     => '0',
                    'status'           => '0',
            ),
            'status' => array (
                    'moduleidentifier' => 'levs',
                    'classify'         => '',
                    'title'            => '状态',
                    'placeholder'      => '',
                    'inputname'        => 'status',
                    'inputtype'        => 'text',
                    'inputvalue'       => '',
                    'settings'         => '',
                    'displayorder'     => '0',
                    'status'           => '0',
            ),
            'addtime' => array (
                    'moduleidentifier' => 'levs',
                    'classify'         => '',
                    'title'            => '添加时间',
                    'placeholder'      => '',
                    'inputname'        => 'addtime',
                    'inputtype'        => 'text',
                    'inputvalue'       => '',
                    'settings'         => '',
                    'displayorder'     => '0',
                    'status'           => '0',
            ),
        );
    }

    public static function setupDesc()
    {
        //return parent::setupDesc(); // TODO: Change the autogenerated stub
        return [];//"{{setupDesc}}";
    }

    public static function getNextSetup()
    {
        return parent::getNextSetup(); // TODO: Change the autogenerated stub
    }

    public static function inputsSetup()
    {
        return parent::inputsSetup(); // TODO: Change the autogenerated stub
    }

    /**
     * 额外的表单字段，不会存入数据库
     * @return array inputs
     */
    public static function extInputs() {
        return [];
    }

    //eg: <div class="card card-header"></div>
    /**
     * 表单头部htm
     * @return string
     */
    public static function headerHtm() {
        return !Lev::isDeveloper(Lev::$app['iden']) ? '' :
            '<tips class="gray inblk scale8">自定义headerHtm：文件位置：'.__DIR__ . '/levUsersLoginModelHelper.php'.'</tips>';
    }

    /**
     * 表单底部htm
     * <tips class="gray inblk scale8">自定义footerHtm</tips>
     * @return string
     */
    public static function footerHtm() {
        return '';
    }

    /**
     * 表单底部内部htm
     * <tips class="gray inblk scale8">自定义footerFormInnerHtm</tips>
     * @return string
     */
    public static function footerFormInnerHtm() {
        return '';
    }

}