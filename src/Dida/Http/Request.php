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
    const VERSION = '20171206';

    /**
     * @var boolean   是否执行过初始化。
     */
    protected static $initialized = false;

    /**
     * @var array   对Url的解析
     * [
     *    'path'     =>
     *    'query'    =>
     *    'fragment' =>
     * ]
     */
    protected static $urlinfo = null;

    /*
     * 内部数组。
     */
    protected static $post = [];
    protected static $get = [];
    protected static $cookie = [];
    protected static $session = [];
    protected static $server = [];
    protected static $headers = [];

    /*
     * 内部变量。
     */
    protected static $method = null;
    protected static $isAjax = null;
    protected static $clientIP = null;
    protected static $schema = null;


    /**
     * 初始化Request，为后面的取值提供数据。
     */
    public static function init()
    {
        // path，query，fragment
        self::$urlinfo = parse_url($_SERVER["REQUEST_URI"]);

        // 统一移除path末尾的/，以便对 “.../foo” 和 “.../foo/” 处理一致。
        self::$urlinfo["path"] = rtrim(self::$urlinfo['path'], "/\\");

        // init
        self::initMethod();
        self::initIsAjax();
        self::initClientIP();
        self::initSchema();

        // POST,GET,COOKIE,SERVER,SESSION
        self::$post = $_POST;
        self::$get = $_GET;
        self::$cookie = $_COOKIE;
        self::$server = $_SERVER;

        // 特别处理一下session，因为不一定session_start()被执行。
        self::$session = (isset($_SESSION)) ? $_SESSION : [];

        // headers
        if (function_exists("apache_request_headers")) {
            $headers = apache_request_headers();
            if (is_array($headers)) {
                self::$headers = $headers;
            }
        }

        // 设置标志
        self::$initialized = false;
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
     * 返回路径中去除了基准路径后的剩余部分。
     *
     * @param string $basePath 基准路径
     *
     * @return false|string
     *      URL路径不是以基准路径开头的，返回false。
     *      返回去除基准路径后的剩余部分。
     *      URL路径等于基准路径，返回空串。
     */
    public static function relativePath($basePath)
    {
        $path = self::path();

        // URL路径等于基准路径，返回空串。
        if ($path === $basePath) {
            return '';
        }

        $len = mb_strlen($basePath);
        if (mb_substr($path, 0, $len) === $basePath) {
            return mb_substr($path, $len);
        } else {
            return false;
        }
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
        } elseif (isset($_SERVER["HTTP_X_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_X_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_X_CLUSTER_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_X_CLUSTER_CLIENT_IP"];
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
     *
     * @return array|mixed
     */
    public static function post($name = null)
    {
        if (is_null($name)) {
            return self::$post;
        }

        return self::arrayValue($name, self::$post);
    }


    /**
     * 查询参数，相当于$_GET。
     *
     * @return array|mixed
     */
    public static function get($name = null)
    {
        if (is_null($name)) {
            return self::$get;
        }

        return self::arrayValue($name, self::$get);
    }


    /**
     * 上传文件参数，相当于$_FILES。
     */
    public static function files($index = null)
    {
    }


    /**
     * Cookie参数，相当于$_COOKIE。
     */
    public static function cookie($index = null)
    {
        if (is_null($index)) {
            return self::$cookie;
        }

        return self::arrayValue($index, self::$cookie);
    }


    /**
     * Session参数，相当于$_SESSION。
     */
    public static function session($index = null)
    {
        if (is_null($index)) {
            return self::$session;
        }

        return self::arrayValue($index, self::$session);
    }


    /**
     * 服务器环境参数，相当于$_SERVER。
     */
    public static function server($index = null)
    {
        if (is_null($index)) {
            return self::$server;
        }

        return self::arrayValue($index, self::$server);
    }


    /**
     * 请求的报文头。
     */
    public static function headers($index = null)
    {
        if (is_null($index)) {
            return self::$headers;
        }

        return self::arrayValue($index, self::$headers);
    }


    /**
     * 获取用户输入数据。
     *
     * 1.使用 POST、GET、COOKIE。
     * 2.使用 php://input 流。
     */
    public static function input($index = null)
    {
        if (is_null($index)) {
            return array_merge(self::$cookie, self::$get, self::$post);
        }

        if (array_key_exists($indx, self::$post)) {
            return self::$post[$index];
        } elseif (array_key_exists($indx, self::$get)) {
            return self::$get[$index];
        } elseif (array_key_exists($indx, self::$cookie)) {
            return self::$cookie[$index];
        } else {
            return null;
        }
    }


    /**
     * 从指定数组中选取这些键。
     *
     * @param string|array $array
     * @param string $indexN
     *
     * @return array|false   正常返回一个数组，有错返回false。
     */
    public static function only($array, $indexN)
    {
        // $array是字符串
        if (is_string($array)) {
            switch ($array) {
                case 'post':
                    $array = self::$post;
                    break;
                case 'get':
                    $array = self::$get;
                    break;
                case 'cookie':
                    $array = self::$cookie;
                    break;
                case 'session':
                    $array = self::$session;
                    break;
                case 'server':
                    $array = self::$server;
                    break;
                case 'headers':
                    $array = self::$headers;
                    break;
                default:
                    return false;
            }
        } elseif (!is_array($array)) {
            return false;
        }

        // 结果
        $result = [];

        // 要获取的键
        $keys = [];
        $cnt = func_num_args();
        if ($cnt === 2) {
            if (is_array($indexN)) {
                $keys = $indexN;
            } elseif (is_string($indexN)) {
                $keys[] = $indexN;
            } else {
                return false;
            }
        } elseif ($cnt > 2) {
            for ($i = 1; $i < $cnt; $i++) {
                $index = func_get_arg($i);
                if (is_string($index) || is_int($index)) {
                    $keys[] = $index;
                } else {
                    return false;
                }
            }
        }

        // 名称
        foreach ($keys as $key) {
            $result[$key] = self::arrayValue($key, $array);
        }

        // 返回
        return $result;
    }


    /**
     * 从指定数组的删除以下这些键，返回其余的。
     *
     * @param string|array $array
     * @param string $indexN
     *
     * @return array|false   正常返回一个数组，有错返回false。
     */
    public static function except($array, $indexN)
    {
        // $array是字符串
        if (is_string($array)) {
            switch ($array) {
                case 'post':
                    $array = self::$post;
                    break;
                case 'get':
                    $array = self::$get;
                    break;
                case 'cookie':
                    $array = self::$cookie;
                    break;
                case 'session':
                    $array = self::$session;
                    break;
                case 'server':
                    $array = self::$server;
                    break;
                case 'headers':
                    $array = self::$headers;
                    break;
                default:
                    return false;
            }
        } elseif (!is_array($array)) {
            return false;
        }

        // 准备
        $result = $array;

        // 要排除的键
        $keys = [];
        $cnt = func_num_args();
        if ($cnt === 2) {
            if (is_array($indexN)) {
                $keys = $indexN;
            } elseif (is_string($indexN)) {
                $keys[] = $indexN;
            } else {
                return false;
            }
        } elseif ($cnt > 2) {
            for ($i = 1; $i < $cnt; $i++) {
                $index = func_get_arg($i);
                if (is_string($index) || is_int($index)) {
                    $keys[] = $index;
                } else {
                    return false;
                }
            }
        }

        // 名称
        foreach ($keys as $key) {
            unset($result[$key]);
        }

        // 返回
        return $result;
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
    protected static function arrayValue($key, array $array)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            return null;
        }
    }
}
