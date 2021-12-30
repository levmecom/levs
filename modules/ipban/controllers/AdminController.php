<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-09-20 11:04
 *
 * 项目：rm  -  $  - AdminController.php
 *
 * 作者：liwei 
 */

namespace modules\levs\modules\ipban\controllers;

use Lev;
use lev\base\Adminv;
use lev\base\Assetsv;
use lev\base\Controllerv;
use lev\base\Viewv;
use modules\levs\modules\ipban\helpers\ipbanSetHelper;
use modules\levs\modules\ipban\ipbanHelper;
use modules\levs\modules\ipban\ipRecordCacheHelper;

!defined('INLEV') && exit('Access Denied LEV');

Adminv::checkAccess();
Assetsv::registerSuperman();

class AdminController extends Controllerv
{

    public static function actionIndex() {
        echo 'hello!';
    }

    public static function actionIpRecord() {

        Lev::$app['title'] = 'IP访问记录 - IP统计';

        //print_r(ipRecordCacheHelper::getRecordIpFile());
        //print_r(ipRecordCacheHelper::censusIp());


        Viewv::render('admin/ip_record', [
        ]);

    }

    public static function actionSetGateIpFile() {
        if (Lev::POSTv('dosubmit')) {
            static::SetGateIpFile();
            return;
        }

        Lev::$app['title'] = '设置入口文件禁止IP';

        Viewv::render('admin/set_gate_ip_file', [
            'gateFiles' => glob(Lev::$aliases['@webroot'].'/*'),
            'phpcodes'  => ipbanHelper::getIpBanCode(),
        ]);
    }

    public static function SetGateIpFile() {
        $addGateFiles = Lev::POSTv('addGateFiles');
        if ($addGateFiles) {
            $clearIpBan = Lev::POSTv('clearIpBan');
            $codes = ipbanHelper::getIpBanCode();

            $resFiles = [
                '成功：'     => null,
                '失败(检查文件目录是否可写)：'     => null,
                '未知php文件：'  => null,
                '文件不存在：'   => null,
            ];
            foreach ($addGateFiles as $rootSrc) {
                if (basename($rootSrc) == $codes['filename']) continue;

                if (is_file($rootSrc)) {
                    $needle = ipbanHelper::getRequireFile($rootSrc);
                    $data = file_get_contents($rootSrc);
                    $wrData = '';
                    if ($clearIpBan) {
                        if (ipbanHelper::checkSeted($rootSrc)) {
                            $wrData = str_replace([$codes['ipbancode'], $codes['ipbancode2']], '', $data);
                        }else {
                            $resFiles['已经清除的文件：'][] = $rootSrc;
                        }
                    }else {
                        if (ipbanHelper::checkSeted($rootSrc)) {
                            $resFiles['已经写入的文件：'][] = $rootSrc;
                        } else {
                            if (strtolower(substr($data, 0, 5)) == '<?php') {
                                $wrData = '<?php ' . $needle . substr($data, 5);
                            } else {
                                $resFiles['未知php文件：'][] = $rootSrc;
                            }
                        }
                    }
                    if ($wrData) {
                        $size = file_put_contents($rootSrc, $wrData, LOCK_EX);
                        if ($size > 0) {
                            $resFiles['成功：'][] = $rootSrc;
                        } else {
                            $resFiles['失败(检查文件目录是否可写)：'][] = $rootSrc;
                        }
                    }
                }else {
                    $resFiles['文件不存在：'][] = $rootSrc;
                }
            }
            Lev::showMessage('设置完成！<pre style="font-size: 10px !important;">'.print_r($resFiles, true));
        }
        Lev::showMessage('未提交文件');
    }
}