<?php
/* 
 * Copyright (c) 2018-2021   All rights reserved.
 * 
 * 创建时间：2021-05-04 19:25
 *
 * 项目：upload  -  $  - Request.php
 *
 * 作者：liwei 
 */

namespace lev\base;

//!defined('LEVS_GATE') && !defined('INLEV') && exit('Access Denied LEV GATE');

class Requestv
{

    /**
     * @return mixed|null
     */
    public static function getReferer() {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }


    /**
     * Returns the user agent.
     * @return string|null user agent, null if not available
     */
    public static function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    }


    /**
     * @return mixed|null
     */
    public static function getRemoteIP()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }


    /**
     * Returns the raw HTTP request body.
     * @return string the request body
     */
    public static function getRawBody()
    {
        static $_rawBody;
        if ($_rawBody === null) {
            $_rawBody = file_get_contents('php://input');
        }

        return $_rawBody;
    }


    /**
     * @return bool
     */
    public static function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }


    /**
     * @return int|null
     */
    public static function getServerPort()
    {
        return isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : null;
    }


    /**
     * @return int|null
     */
    public static function getPort()
    {
        static $_port;
        if ($_port === null) {
            $serverPort = static::getServerPort();
            $_port = !static::getIsSecureConnection() && $serverPort !== null ? $serverPort : 80;
        }

        return $_port;
    }


    /**
     * @return int|null
     */
    public static function getSecurePort()
    {
        static $_securePort;
        if ($_securePort === null) {
            $serverPort = static::getServerPort();
            $_securePort = static::getIsSecureConnection() && $serverPort !== null ? $serverPort : 443;
        }

        return $_securePort;
    }


    /**
     * @return string eg: https://explame.com
     */
    public static function getHostInfo()
    {
        static $_hostInfo;
        if ($_hostInfo === null) {
            $secure = static::getIsSecureConnection();
            $http = $secure ? 'https' : 'http';

            if (isset($_SERVER['HTTP_HOST'])) {
                $_hostInfo = $http . '://' . $_SERVER['HTTP_HOST'];
            } elseif (isset($_SERVER['SERVER_NAME'])) {
                $_hostInfo = $http . '://' . $_SERVER['SERVER_NAME'];
                $port = $secure ? static::getSecurePort() : static::getPort();
                if (($port !== 80 && !$secure) || ($port !== 443 && $secure)) {
                    $_hostInfo .= ':' . $port;
                }
            }
        }

        return $_hostInfo;
    }


    /**
     * @return mixed eg: explame.com
     */
    public static function getHostName()
    {
        static $_hostName;
        if ($_hostName === null) {
            $_hostName = parse_url(static::getHostInfo(), PHP_URL_HOST);
        }

        return $_hostName;
    }


    /**
     * Return if the request is sent via secure channel (https).
     * @return bool if the request is sent via secure channel (https)
     */
    public static function getIsSecureConnection()
    {
        static $_isHttps;
        if (!isset($_isHttps)) {
            $_isHttps = false;
            if (isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) != "off") {
                $_isHttps = true;
            }else if (isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && strtolower($_SERVER["HTTP_X_FORWARDED_PROTO"]) == "https") {
                $_isHttps = true;
            }else if (isset($_SERVER["HTTP_SCHEME"]) && strtolower($_SERVER["HTTP_SCHEME"]) == "https") {
                $_isHttps = true;
            }else if (isset($_SERVER["HTTP_FROM_HTTPS"]) && strtolower($_SERVER["HTTP_FROM_HTTPS"]) != "off") {
                $_isHttps = true;
            }else if (isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] == 443) {
                $_isHttps = true;
            }
        }
        return $_isHttps;
    }


    /**
     * Returns the relative URL for the application.
     * This is similar to [[scriptUrl]] except that it does not include the script file name,
     * and the ending slashes are removed.
     * @return string the relative URL for the application
     * @see setScriptUrl()
     */
    public static function getBaseUrl()
    {
        static $_baseUrl;
        if ($_baseUrl === null) {
            $_baseUrl = rtrim(dirname(static::getScriptUrl()), '\\/');
        }

        return $_baseUrl;
    }


    /**
     * Returns the relative URL of the entry script.
     * The implementation of this method referenced Zend_Controller_Request_Http in Zend Framework.
     * @return string the relative URL of the entry script.
     */
    public static function getScriptUrl()
    {
        static $_scriptUrl;
        if ($_scriptUrl === null) {
            $scriptFile = static::getScriptFile();
            $scriptName = basename($scriptFile);
            if (isset($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === $scriptName) {
                $_scriptUrl = $_SERVER['SCRIPT_NAME'];
            } elseif (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) === $scriptName) {
                $_scriptUrl = $_SERVER['PHP_SELF'];
            } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName) {
                $_scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
            } elseif (isset($_SERVER['PHP_SELF']) && ($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false) {
                $_scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
            } elseif (!empty($_SERVER['DOCUMENT_ROOT']) && strpos($scriptFile, $_SERVER['DOCUMENT_ROOT']) === 0) {
                $_scriptUrl = str_replace([$_SERVER['DOCUMENT_ROOT'], '\\'], ['', '/'], $scriptFile);
            } else {
                throw new \InvalidArgumentException('无法确定入口脚本URL。 Unable to determine the entry script URL.');
            }
        }

        return $_scriptUrl;
    }


    /**
     * Returns the entry script file path.
     * The default implementation will simply return `$_SERVER['SCRIPT_FILENAME']`.
     * @return string the entry script file path
     */
    public static function getScriptFile()
    {
        static $_scriptFile;
        if (isset($_scriptFile)) {
            return $_scriptFile;
        }

        if (isset($_SERVER['SCRIPT_FILENAME'])) {
            return $_SERVER['SCRIPT_FILENAME'];
        }

        throw new \InvalidArgumentException('无法确定入口脚本文件路径。Unable to determine the entry script file path.');
    }

}