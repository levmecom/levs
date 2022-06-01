<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-04 19:20
 *
 * 项目：upload  -  $  - BaseView.php
 *
 * 作者：liwei 
 */

namespace lev\base;

!defined('INLEV') && exit('Access Denied LEV');

use Lev;
use lev\helpers\cacheFileHelpers;

class Viewv
{

    /**
     * Ajax 请求输出json数据，供popup弹窗调用
     * @param $_FileName_
     * @param array $_Param_
     */
    public static function renders($_FileName_, $_Param_ = []) {
        if (Lev::GETv('ziframescreen') == '5' || Lev::isAjax()) {
            Lev::showMessages(Lev::responseMsg(1, '', ['htms'=>Viewv::renderPartial($_FileName_, $_Param_)]));
        }else {
            Viewv::render($_FileName_, $_Param_);
        }
    }

    /**
     * @param $_FileName_
     * @param array $_Param_
     */
    public static function render($_FileName_, $_Param_ = []) {
        extract($_Param_, EXTR_OVERWRITE);

        $_View_File = static::findViewFile($_FileName_);

        include Lev::getAlias(Lev::$app['layout']);
    }

    public static function renderHtml($filename, $param = [], $htmlRoute = '') {
        $html = static::renderPhpFile(static::findViewFile($filename), $param, Lev::getAlias(Lev::$app['layout']));
        if ($htmlRoute) {
            $dir = rtrim(Lev::$aliases['@htmlroot'].'/'.Lev::$app['iden'].'/'.
                ($htmlRoute == 'default/index' ? '' : (basename($htmlRoute) == 'index' ? dirname($htmlRoute) : $htmlRoute)), '/');
            cacheFileHelpers::mkdirv($dir);
            file_put_contents($dir . DIRECTORY_SEPARATOR . 'index.html', $html);
        }
        return $html;
    }

    public static function renderPartial($filename, $param = []) {
        return static::renderPhpFile(static::findViewFile($filename), $param);
    }

    public static function renderFile($_fileroot_, $_params_ = []) {
        extract($_params_, EXTR_OVERWRITE);
        include static::findViewFile($_fileroot_);
    }

    /**
     * Renders a view file as a PHP script.
     *
     * This method treats the view file as a PHP script and includes the file.
     * It extracts the given parameters and makes them available in the view file.
     * The method captures the output of the included view file and returns it as a string.
     *
     * This method should mainly be called by view renderer or [[renderPartial()]].
     *
     * @param $_fileroot_
     * @param array $_params_ the parameters (name-value pairs) that will be extracted and made available in the view file.
     * @return string the rendering result
     * @throws \Throwable
     */
    public static function renderPhpFile($_View_File, $_params_ = [], $_layout_file_ = '')
    {
        $_obInitialLevel_ = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        try {
            require $_layout_file_ ?: $_View_File;
            return ob_get_clean();
        } catch (\Exception $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        } catch (\Throwable $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
    }

    /**
     * Finds the view file based on the given view name.
     * @param string $view the view name or the [path alias](guide:concept-aliases) of the view file. Please refer to [[render()]]
     * on how to specify this parameter.
     * @return string the view file path. Note that the file may not exist.
     */
    public static function findViewFile($view)
    {
        if (strncmp($view, '@', 1) === 0) {
            //别名路径 e.g. "@app/views/main"
            $file = Lev::getAlias($view);
        }elseif (strncmp($view, '/', 1) === 0) {
            //真实路径 e.g. "/root/site/index"
            $file = $view;
        } else {
            //视图路径
            $renders = Lev::$aliases['@renders'];
            $file = $renders . DIRECTORY_SEPARATOR . $view;
        }

        if (substr($file, -4) == '.php' || pathinfo($file, PATHINFO_EXTENSION) !== '') {
            $path = $file;
        }else {
            $path = $file . '.php';
            $view.= '.php';
        }

        return is_file($view) ? $view : $path;
    }

}