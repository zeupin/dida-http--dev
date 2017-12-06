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
     * 内部变量
     */
    protected static $method = null;
    protected static $isAjax = null;
    protected static $clientIP = null;
    protected static $schema = null;
    protected static $post = [];
    protected static $get = [];
    protected static $server = [];
    protected static $cookie = [];
    protected static $session = [];
    protected static $headers = [];


    /**
     * 初始化Request，为后面的取值提供数据。
     */
    public static function init()
    {
        // path，query，fragment
        self::$urlinfo = parse_url($_SERVER["REQUEST_URI"]);

        // init
        self::initMethod();
        self::initIsAjax();
        self::initClientIP();
        self::initSchema();
    }


    /**
     * Request的路径。
     *
     * @return string|null|false   正常返回path，没有path则返回null，出错返回false
     */
    public static function path()
    {
        // 如果init()时，parse_url()时失败
        if (self::$urlinfo === false) {
            return false;
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
        // 如果init()时，parse_url()时失败
        if (self::$urlinfo === false) {
            return false;
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
        // 如果init()时，parse_url()时失败
        if (self::$urlinfo === false) {
            return false;
        }

        return isset(self::$urlinfo['fragment']) ? self::$urlinfo['fragment'] : null;
    }


    /**
     * 初始化method。
     */
    protected static function initMethod()
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
                self::$method = $method;
                return;
            default:
                self::$method = false;
                return;
        }
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
        return self::$method;
    }


    /**
     * 初始化isAjax。
     */
    protected static function initIsAjax()
    {
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            self::$isAjax = false;
            return;
        }

        self::$isAjax = (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }


    /**
     * 是否是Ajax请求。
     *
     * @return boolean
     */
    public static function isAjax()
    {
        return self::$isAjax;
    }


    /**
     * 初始化clientIP。
     */
    protected static function initClientIP()
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

        self::$clientIP = $ip;
    }


    /**
     * 获取客户端IP。
     *
     * @return string|false 正常返回读取到的ip，无法获取时，返回false
     */
    public static function clientIP()
    {
        return self::$clientIP;
    }


    /**
     * 初始化schema。
     */
    protected static function initSchema()
    {
        if (isset($_SERVER['REQUEST_SCHEME'])) {
            self::$schema = $_SERVER['REQUEST_SCHEME'];
        } else {
            self::$schema = false;
        }
    }


    /**
     * 获取Request的协议名(http/https)。
     *
     * @return string|false 正常返回读取到的schema，无法获取时，返回false
     */
    public static function schema()
    {
        return self::$schema;
    }


    /**
     * 表单参数，相当于$_POST。
     */
    public static function post()
    {
    }


    /**
     * 查询参数，相当于$_GET。
     */
    public static function get()
    {
    }


    /**
     * 服务器环境参数，相当于$_SERVER。
     */
    public static function server()
    {
    }


    /**
     * 上传文件参数，相当于$_FILES。
     */
    public static function files()
    {
    }


    /**
     * Cookie参数，相当于$_COOKIE。
     */
    public static function cookie()
    {
    }


    /**
     * Session参数，相当于$_SESSION。
     */
    public static function session()
    {
    }


    /**
     * 请求的报文头。
     */
    public static function headers()
    {
    }


    /**
     * 获取用户输入数据。
     *
     * 1.使用 POST、GET、COOKIE 和 SERVER 数据
     * 2.使用 php://input 流
     */
    public static function input()
    {
    }


    /**
     * 所有请求变量。
     */
    public static function all()
    {
    }


    /**
     * 只需要这些请求变量。
     */
    public static function only()
    {
    }


    /**
     * 所有的请求变量，除了以下这些。
     */
    public static function except()
    {
    }


    /**
     * 临时保存所有数据，以备后面使用。
     */
    public static function flashAll()
    {
    }


    /**
     * 临时保存如下数据，以备后面使用。
     */
    public static function flashOnly()
    {
    }


    /**
     * 临时保存所有数据，除了以下这些，以备后面使用。
     */
    public static function flashExcept()
    {
    }


    /**
     * 一个工具函数。
     * 如果数组中key存在，则返回对应的value，否则返回null。
     *
     * @param int|string $key
     * @param array $array
     *
     * @return mixed
     */
    protected function arrayValue($key, array $array)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            return null;
        }
    }
}
