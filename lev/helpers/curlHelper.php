<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-07-05 21:04
 *
 * 项目：rm  -  $  - curlHelper.php
 *
 * 作者：liwei 
 */

namespace lev\helpers;

use CURLFile;

!defined('INLEV') && exit('Access Denied LEV');

if (!function_exists('curl_file_create')) {
    /**
     * @param string $filename 被上传文件的 路径。
     * @param string $mimetype
     * @param string $postname
     * @return string
     *
     * (PHP 5 >= 5.5.0) <br/>
     * Create a CURLFile object
     * @link https://secure.php.net/manual/en/curlfile.construct.php
     * @param string $filename <p> Path to the file which will be uploaded.</p>
     * @param string $mimetype [optional] <p>Mimetype of the file.</p>
     * @param string $postname [optional] <p>Name of the file.</p>
     * @return CURLFile
     * Returns a {@link https://secure.php.net/manual/en/class.curlfile.php CURLFile} object.
     * @since 5.5.0
     */
    function curl_file_create($filename, $mimetype = 'application/octet-stream', $postname = '') {
        return "@$filename;filename=" . ($postname ?: basename($filename)) . ($mimetype ? ";type=$mimetype" : '');
    }
}

class curlHelper
{
    public static function formatHttpheader($header) {
        if (!empty($header[0])) {
            $arr = explode("\n", $header[0]);
            $header[0] = trim($arr[0]);
            unset($arr[0]);
            if ($arr) {
                foreach ($arr as $v) {
                    $header[] = trim($v);
                }
            }
        }
        return $header;
    }

    /**
     * @param $arr
     * @return bool|string
     */
    public static function doCurl2($arr) {
        if (isset($arr['timelimit'])) {
            set_time_limit(intval($arr['timelimit']));//秒
        }
        //没地址结束
        if(empty($arr['url'])) return false;

        //没用户信息自动获取
        //if ($arr['agent'] ==1)
        $arr['agent'] = isset($arr['agent']) ? $arr['agent'] : $_SERVER['HTTP_USER_AGENT'];

        //没用户IP自动获取  //ip();//$_SERVER["REMOTE_ADDR"];
        if (empty($arr['ip'])) {
            $arr['ip'] = mt_rand(99, 255).'.'.mt_rand(99, 255).'.'.mt_rand(99, 255).'.'.mt_rand(99, 255);
        }

        //没header 设置成假
        if (empty($arr['header'])) $arr['header'] = false;

        //来路设置
        if(empty($arr['referer'])) $arr['referer'] = '';

        //没cookie 保存设置成空
        if(empty($arr['cookiejar'])) $arr['cookiejar'] = '';

        //没cookie 读取设置成空
        if(empty($arr['cookiefile'])) $arr['cookiefile'] = '';

        //没发送post 设置成空
        if(empty($arr['post'])) $arr['post'] = [];

        //输出方式
        if (empty($arr['bisfer'])) $arr['bisfer'] = FALSE;

        //超时时间没有设置成常量默认
        if(empty($arr['time'])) {// $arr['time'] = 5;
            $arr['time'] = 35;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$arr['url']);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, $arr['bisfer']) ;
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, $arr['header']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-FORWARDED-FOR:{$arr['ip']}","CLIENT-IP:{$arr['ip']}"));

        //用户浏览器信息
        if ($arr['agent']) curl_setopt($ch, CURLOPT_USERAGENT, $arr['agent']);

        //读取cookie
        if ($arr['cookiefile']) curl_setopt($ch, CURLOPT_COOKIEFILE, $arr['cookiefile']);

        //保存cookie
        if ($arr['cookiejar']) curl_setopt($ch, CURLOPT_COOKIEJAR, $arr['cookiejar']);

        //来路
        if ($arr['referer']) curl_setopt($ch, CURLOPT_REFERER, $arr['referer']);

        //上传文件
        if (!empty($arr['upload'])) {
            foreach ($arr['upload'] as $k => $v) {
                if (is_file(is_array($v) ? $v['filename'] : $v)) {
                    $arr['post'][$k] = !is_array($v) ? curl_file_create($v) : curl_file_create(
                        $v['filename'],
                        empty($v['mimetype']) ? '' : $v['mimetype'],
                        empty($v['postname']) ? '' : $v['postname']
                    );
                }
            }
            //curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0

            //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); //头部要送出'Expect: '
            $arr['httpheader'][] = 'Expect:';

            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
        }

        //post数据
        if ($arr['post']) {
            //curl_setopt($ch, CURLOPT_PORT, true);
            //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            //curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr['post']);
        }

        //发送cookie
        if (empty($arr['cookie'])) $arr['cookie'] = '';
        curl_setopt($ch, CURLOPT_COOKIE, $arr['cookie']);

        //多少秒超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $arr['time']);//print_r($arr);print_r($ch);exit;

        if (!empty($arr['httpheader'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, static::formatHttpheader($arr['httpheader']));
        }else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-FORWARDED-FOR:{$arr['ip']}","CLIENT-IP:{$arr['ip']}"));
        }

        $c_url = curl_exec($ch);
        if (isset($arr['_error'])) {
            $errtip = date('Y-m-d H:i:s', \Lev::$app['timestamp']).' '.curl_errno($ch).'：'.curl_error($ch)."\r\n";
            file_put_contents(__DIR__ . '/.docurl2_error.txt', $errtip, FILE_APPEND);
        }
        curl_close($ch);
        return $c_url;
    }

    /**
     * @param $arr
     * @return bool|string
     */
    public static function doCurl($arr) {
        if (isset($arr['timelimit'])) {
            set_time_limit(intval($arr['timelimit']));//秒
        }
        //没地址结束
        if(empty($arr['url'])) return false;

        //没用户信息自动获取
        //if ($arr['agent'] ==1)
        $arr['agent'] = isset($arr['agent']) ? $arr['agent'] : $_SERVER['HTTP_USER_AGENT'];

        //没用户IP自动获取  //ip();//$_SERVER["REMOTE_ADDR"];
        if (empty($arr['ip'])) {
            $arr['ip'] = mt_rand(99, 255).'.'.mt_rand(99, 255).'.'.mt_rand(99, 255).'.'.mt_rand(99, 255);
        }

        //没header 设置成假
        if (empty($arr['header'])) $arr['header'] = false;

        //来路设置
        if(empty($arr['referer'])) $arr['referer'] = '';

        //没cookie 保存设置成空
        if(empty($arr['cookiejar'])) $arr['cookiejar'] = '';

        //没cookie 读取设置成空
        if(empty($arr['cookiefile'])) $arr['cookiefile'] = '';

        //没发送post 设置成空
        if(empty($arr['post'])) $arr['post'] = [];

        //输出方式
        if (empty($arr['bisfer'])) $arr['bisfer'] = FALSE;

        //超时时间没有设置成常量默认
        if(empty($arr['time'])) {// $arr['time'] = 5;
            $arr['time'] = 35;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$arr['url']);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, $arr['bisfer']) ;
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, $arr['header']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-FORWARDED-FOR:{$arr['ip']}","CLIENT-IP:{$arr['ip']}"));

        //用户浏览器信息
        if ($arr['agent']) curl_setopt($ch, CURLOPT_USERAGENT, $arr['agent']);

        //读取cookie
        if ($arr['cookiefile']) curl_setopt($ch, CURLOPT_COOKIEFILE, $arr['cookiefile']);

        //保存cookie
        if ($arr['cookiejar']) curl_setopt($ch, CURLOPT_COOKIEJAR, $arr['cookiejar']);

        //来路
        if ($arr['referer']) curl_setopt($ch, CURLOPT_REFERER, $arr['referer']);

        //上传文件
        if (!empty($arr['upload'])) {
            foreach ($arr['upload'] as $k => $v) {
                if (is_file(is_array($v) ? $v['filename'] : $v)) {
                    $arr['post'][$k] = !is_array($v) ? curl_file_create($v) : curl_file_create(
                        $v['filename'],
                        empty($v['mimetype']) ? '' : $v['mimetype'],
                        empty($v['postname']) ? '' : $v['postname']
                    );
                }
            }
            //curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0); //强制协议为1.0

            //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:')); //头部要送出'Expect: '
            $arr['httpheader'][] = 'Expect:';

            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); //强制使用IPV4协议解析域名
        }

        //post数据
        if ($arr['post']) {
            //curl_setopt($ch, CURLOPT_PORT, true);
            //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            //curl_setopt($ch, CURLOPT_POST, true);
            $arr['httpheader'][] = 'Expect:';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr['post']);
        }

        //发送cookie
        if (empty($arr['cookie'])) $arr['cookie'] = '';
        curl_setopt($ch, CURLOPT_COOKIE, $arr['cookie']);

        //多少秒超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $arr['time']);//print_r($arr);print_r($ch);exit;

        if (!empty($arr['httpheader'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $arr['httpheader']);
        }

        $c_url = curl_exec($ch);
        if (isset($arr['_error'])) echo curl_error($ch);
        curl_close($ch);
        return $c_url;
    }

    //static::curl_custom_postfields($ch, $arr['post'], $arr['upload']);
//    public static function curl_custom_postfields($ch, array $assoc = array(), array $files = array()) {
//
//        // invalid characters for "name" and "filename"
//        static $disallow = array("\0", "\"", "\r", "\n");
//
//        // build normal parameters
//        foreach ($assoc as $k => $v) {
//            $k = str_replace($disallow, "_", $k);
//            $body[] = implode("\r\n", array(
//                "Content-Disposition: form-data; name=\"{$k}\"",
//                "",
//                filter_var($v),
//            ));
//        }
//
//        // build file parameters
//        foreach ($files as $k => $v) {
//            switch (true) {
//                case false === $v = realpath(filter_var($v)):
//                case !is_file($v):
//                case !is_readable($v):
//                    continue; // or return false, throw new InvalidArgumentException
//            }
//            $data = file_get_contents($v);
//            $v = call_user_func("end", explode(DIRECTORY_SEPARATOR, $v));
//            $k = str_replace($disallow, "_", $k);
//            $v = str_replace($disallow, "_", $v);
//            $body[] = implode("\r\n", array(
//                "Content-Disposition: form-data; name=\"{$k}\"; filename=\"{$v}\"",
//                "Content-Type: application/octet-stream",
//                "",
//                $data,
//            ));
//        }
//
//        // generate safe boundary
//        do {
//            $boundary = "---------------------" . md5(mt_rand() . microtime());
//        } while (preg_grep("/{$boundary}/", $body));
//
//        // add boundary for each parameters
//        array_walk($body, function (&$part) use ($boundary) {
//            $part = "--{$boundary}\r\n{$part}";
//        });
//
//        // add final boundary
//        $body[] = "--{$boundary}--";
//        $body[] = "";
//
//        // set options
//        return @curl_setopt_array($ch, array(
//            CURLOPT_POST       => true,
//            CURLOPT_POSTFIELDS => implode("\r\n", $body),
//            CURLOPT_HTTPHEADER => array(
//                "Expect: 100-continue",
//                "Content-Type: multipart/form-data; boundary={$boundary}", // change Content-Type
//            ),
//        ));
//    }
}
