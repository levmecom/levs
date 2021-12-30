<?php
/**
 * Copyright (c) 2021-2222   All rights reserved.
 *
 * 创建时间：2021-12-27 13:53
 *
 * 项目：levs  -  $  - CmdComposerHelper.php
 *
 * 作者：liwei
 */

//!defined('INLEV') && exit('Access Denied LEV');


namespace modules\levs\modules\composer;

class CmdComposerHelper
{
    public static $composer = null;

    public static function postUpdate($event)
    {
        $composer = $event->getComposer();
        //var_dump($composer);
        // do stuff
    }

    /**
     * @param $event
     */
    public static function postPackageInstall($event)
    {
//        $installedPackage = $event->getOperation()->getPackage();
//        if (empty(static::$composer)) {
//            static::$composer = $event->getComposer();
//        }
//        var_dump(static::$composer->getConfig());

        static::copyWebFiles();

    }

    public static function copyWebFiles() {

        $appv_vendor = realpath(__DIR__ . '/../../../..');

        $webroot = realpath(dirname(__DIR__) . '/../web');

        $configFile = __DIR__ . '/const.config.php';

        $replaceVar = [
            '{{#vendor}}' => $appv_vendor,
            '{{#configFile}}' => $configFile,
        ];
        $configData = file_get_contents($configFile);
        foreach ($replaceVar as $k => $r) {
            $configData = str_replace($k, $r, $configData);
        }
        file_put_contents($configFile, $configData, LOCK_EX);

        $editFilesToWebroot = [
            $webroot . '/gate.php',
            $webroot . '/install_lev.php',
            $webroot . '/levs.php',
        ];

        foreach ($editFilesToWebroot as $file) {
            if (is_file($file)) {
                $data = file_get_contents($file);
                if ($data && stripos(trim($data), '<?php') === 0) {
                    foreach ($replaceVar as $k => $r) {
                        $data = str_replace($k, $r, $data);
                    }
                    $data = str_ireplace('<?php', '<?php include '.$configFile.'; ', $data);
                    file_put_contents($file, $data, LOCK_EX);
                }
            }
        }

        echo "\n".'恭喜，文件下载成功！系统使用及安装步骤：'."\n".'1.将【'.$webroot.'】整个目录下文件移动到网站根目录；'."\n".'2.然后访问目录进入安装界面！';
    }

    public static function installLevs() {

    }

    public static function setWebroot() {

    }

}