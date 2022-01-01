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
        //$installedPackage = $event->getOperation()->getPackage();
        if (empty(static::$composer)) {
            static::$composer = $event->getComposer();
        }

        $name = basename($event->getName());
        $name = (array)$event;
        $message = "########################\n{$name}\n".print_r($name, true);
        if ($name === 'levs') {
            static::copyWebFiles();
            $message .= "\n文件初始化完成\n########################";
        }

        echo $message;
    }

    public static function copyWebFiles() {

        $appv_vendor = realpath(__DIR__ . '/../../../..');

        $webroot = realpath(dirname(__DIR__) . '/../web');

        $runtimeroot = dirname($webroot) . '/runtime';
        is_dir($runtimeroot) || mkdir($runtimeroot);

        $configFile = '/const.config.php';
        $configFileroot = $runtimeroot . '/composer.const.config.php';

        $replaceVar = [
            '{{#vendor}}' => $appv_vendor,
            '{{#configFile}}' => $configFileroot,
        ];
        $configData = file_get_contents(__DIR__ . $configFile);
        foreach ($replaceVar as $k => $r) {
            $configData = str_replace($k, $r, $configData);
        }
        file_put_contents($configFileroot, $configData, LOCK_EX);

        $editFilesToWebroot = [
            $webroot . '/gate.php',
            $webroot . '/install_lev.php',
            $webroot . '/levs.php',
        ];

        foreach ($editFilesToWebroot as $file) {
            if (is_file($file)) {
                $data = ltrim(file_get_contents($file));
                if ($data && stripos($data, '<?php') === 0) {
                    foreach ($replaceVar as $k => $r) {
                        $data = str_replace($k, $r, $data);
                    }
                    //$data = str_ireplace('<?php', '<?php include \''.$configFile.'\'; ', $data);
                    $data = '<?php include \''.$configFileroot.'\'; '.substr($data, 5);
                    file_put_contents($file, $data, LOCK_EX);
                }
            }
        }

        echo "\n".'恭喜，文件下载成功！系统使用及安装步骤：'
            ."\n".'1.将【'.$webroot.'】整个目录下文件复制到网站根目录；'
            ."\n".'2.然后访问目录进入安装界面！'
            ."\n";
    }

    public static function installLevs() {

    }

    public static function setWebroot() {

    }

}