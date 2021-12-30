<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-11-07 13:38
 *
 * 项目：rm  -  $  - Rewritev.php
 *
 * 作者：liwei 
 */

namespace lev\base;

//!defined('LEVS_GATE') && !defined('INLEV') && exit('Access Denied LEV GATE');

require_once __DIR__ . '/Requestv.php';

class Rewritev extends Requestv
{
    public static $headers = null;
    public static $requestUri = null;

    public static function setGET() {
        $uri = urldecode(static::resolveRequestUri());
        if ($uri) {
            ($baseUri = static::getBaseUrl()) && $uri = explode($baseUri, $uri)[1];
            $param = explode('/', trim(explode('?', $uri)[0], '/'));
            static::setGETiden($param[0]);
            if (isset($param[1])) {
                if (is_numeric($param[1][0])) {
                    static::setGETpms($param[1]);
                }else {
                    static::setGETroute($param[1]);
                    if (isset($param[2])) {
                        if (is_numeric($param[2][0])) {
                            static::setGETpms($param[2]);
                        }else {
                            static::setGETroute($param[2]);
                        }
                    }
                }
                isset($param[3]) && static::setGETpms($param[3]);
                isset($_GET['##r']) && $_GET['##r'] = implode('/', $_GET['##r']);
            }

            isset($_GET['opid']) || $_GET['opid'] = $_GET['##opid'];
            isset($_GET['id'])   || $_GET['id']   = $_GET['##id'];
            isset($_GET['r'])    || $_GET['r']    = $_GET['##r'];
        }
    }

    public static function setGETiden($string) {
        $arr = explode('-', $string);
        $_GET['##id'] = $arr[0];
        if (!empty($arr[1])) {
            if (strpos($arr[1], '.html') !== false) {
                $_GET['##opid'] = explode('.html', $arr[1])[0];
            }else {
                $_GET['##id'] .= ':'.$arr[1];
                if ($arr = array_slice($arr, 2)) {
                    static::setGETpms($arr);
                }
            }
        }
    }

    public static function setGETroute($string) {
        if (strpos($string, '.html') !== false && strpos($string, '-') !== false) {
            $arr = explode('-', explode('.html', $string)[0]);
            $string = implode('-', array_slice($arr, 0, -1));
            $_GET['##opid'] = end($arr);
        }
        $_GET['##r'][] = $string;
    }

    public static function setGETpms($string) {
        $arr = is_array($string) ? $string : explode('-', explode('.html', $string)[0]);
        $arr = array_chunk($arr, 2);
        foreach ($arr as $v) {
            $key = isset($v[1]) ? $v[1] : 'opid';
            $_GET['##'.$key] = explode('.html', $v[0])[0];
            isset($_GET[$key]) || $_GET[$key] = $_GET['##'.$key];
        }
    }

    /**
     * Resolves the request URI portion for the currently requested URL.
     * This refers to the portion that is after the [[hostInfo]] part. It includes the [[queryString]] part if any.
     * The implementation of this method referenced Zend_Controller_Request_Http in Zend Framework.
     * @return string|bool the request URI portion for the currently requested URL.
     * Note that the URI returned may be URL-encoded depending on the client.
     * @exit if the request URI cannot be determined due to unusual server configuration
     */
    public static function resolveRequestUri()
    {
        if (static::$requestUri === null) {
            if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // IIS
                $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
            } elseif (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) { // IIS 7
                $requestUri = $_SERVER['HTTP_X_ORIGINAL_URL'];
            } elseif (isset($_SERVER['REQUEST_URI'])) {
                $requestUri = $_SERVER['REQUEST_URI'];
                if ($requestUri !== '' && $requestUri[0] !== '/') {
                    $requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
                }
            } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0 CGI
                $requestUri = $_SERVER['ORIG_PATH_INFO'];
                if (!empty($_SERVER['QUERY_STRING'])) {
                    $requestUri .= '?' . $_SERVER['QUERY_STRING'];
                }
            } else {
                exit('抱歉， 无法确定请求URI。');
            }

            static::$requestUri = $requestUri;
        }

        return static::$requestUri;
    }

    /**
     * Returns the header collection.
     * The header collection contains incoming HTTP headers.
     */
    public static function getHttpHeaders()
    {
        if (static::$headers === null) {
            if (function_exists('getallheaders')) {
                static::$headers = getallheaders();
            } elseif (function_exists('http_get_request_headers')) {
                static::$headers = http_get_request_headers();
            } else {
                foreach ($_SERVER as $name => $value) {
                    if (strncmp($name, 'HTTP_', 5) === 0) {
                        $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                        static::$headers[$name] = $value;
                    }
                }
            }
        }

        return static::$headers;
    }

}
