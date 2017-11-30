<?php
/**
 * Dida Framework  -- A Rapid Development Framework
 * Copyright (c) Zeupin LLC. (http://zeupin.com)
 *
 * Licensed under The MIT License.
 * Redistributions of files must retain the above copyright notice.
 */

namespace Dida\Http;

/**
 * Request
 */
class Request
{
    /**
     * Version
     */
    const VERSION = '20171130';

    /**
     * @var array
     * [
     *    'path'     =>
     *    'query'    =>
     *    'fragment' =>
     * ]
     */
    protected static $urlinfo = null;


    /**
     * 解析url
     */
    protected static function parseUrl()
    {
        // 不重复初始化
        if (self::$urlinfo) {
            return;
        }

        // path，query，fragment
        self::$urlinfo = parse_url($_SERVER["REQUEST_URI"]);
    }


    /**
     * Request的路径。
     *
     * @return string|null|false   正常返回path，没有path则返回null，出错返回false
     */
    public static function path()
    {
        if (self::$urlinfo === false) {
            return false;
        }

        if (self::$urlinfo === null) {
            self::parseUrl();
        }

        return isset(self::$urlinfo['path']) ? self::$urlinfo['path'] : null;
    }


    /**
     * Request的查询串。
     *
     * @return string|null|false   正常返回查询串，没有则返回null，出错返回false
     */
    public static function queryString()
    {
        if (self::$urlinfo === false) {
            return false;
        }

        if (self::$urlinfo === null) {
            self::parseUrl();
        }

        return isset(self::$urlinfo['query']) ? self::$urlinfo['query'] : null;
    }


    /**
     * Request的页面书签。
     *
     * @return string|null|false   正常返回fragment，没有则返回null，出错返回false
     */
    public static function fragment()
    {
        if (self::$urlinfo === false) {
            return false;
        }

        if (self::$urlinfo === null) {
            self::parseUrl();
        }

        return isset(self::$urlinfo['fragment']) ? self::$urlinfo['fragment'] : null;
    }


    /**
     * 获取Request的method。
     *
     * 如果有POST的DIDA_REQUEST_METHOD字段，则以此字段为准。
     * 没有这个字段，则看是普通的get还是post。
     * 正常返回get，post，put，patch，delete，head，options之一；如果非法，返回false。
     *
     * @return string|false
     */
    public static function method()
    {
        if (isset($_POST['DIDA_REQUEST_METHOD'])) {
            $method = strtolower($_POST['DIDA_REQUEST_METHOD']);
        } elseif (isset($_SERVER['REQUEST_METHOD'])) {
            $method = strtolower($_SERVER['REQUEST_METHOD']);
        }

        // 只能为：get，post，put，patch，delete，head，options之一
        switch ($method) {
            case 'get':      // 获取资源
            case 'post':     // 新建资源
            case 'put':      // 更新整个资源
            case 'patch':    // 更新资源的个别字段
            case 'delete':   // 删除资源
            case 'head':     // 查询资源头
            case 'options':  // 查询可选操作
                return $method;
            default:
                return false;
        }
    }


    /**
     * 是否是Ajax请求
     *
     * @return boolean
     */
    public static function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) &&
            (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }


    /**
     * 获取客户端IP
     *
     * @return string|false 正常返回读取到的ip，无法获取时，返回false
     */
    public static function clientIP()
    {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["REMOTE_ADDR"])) {
            $ip = $_SERVER["REMOTE_ADDR"];
        } else {
            $ip = false; // ip未定义
        }

        // 返回结果
        return $ip;
    }


    /**
     * 获取Request的协议名(http/https)
     *
     * @return string|false 正常返回读取到的schema，无法获取时，返回false
     */
    public static function schema()
    {
        if (isset($_SERVER['REQUEST_SCHEME'])) {
            return $_SERVER['REQUEST_SCHEME'];
        } else {
            return false;
        }
    }
}
