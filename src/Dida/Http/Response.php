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
 * Response
 */
class Response
{
    /**
     * Version
     */
    const VERSION = '20171129';

    /*
     * Response的内容类型
     */
    const HTML_TYPE = 'html';
    const TEXT_TYPE = 'text';
    const JSON_TYPE = 'json';
    const JSONP_TYPE = 'jsonp';

    /**
     * @var array
     */
    protected static $cookie = [];

    /**
     * @var array
     */
    protected static $session = [];

    /**
     * @var array
     */
    protected static $data = [];

    /**
     * @var array
     */
    protected static $content = [];


    /**
     * 重定向。
     *
     * @param string $url  重定向的网址，要包含http/https协议头。
     * @param int|null $refresh  需要等待的时间。
     *     如果为null，表示立即跳转，且不执行后续的php代码。
     *     如果为int，表示等待$refresh时间后再跳转，此时会继续执行后续PHP代码。
     */
    public static function redirect($url, $refresh = null)
    {
        if (is_numeric($refresh)) {
            header("Refresh: $refresh; url=$url");
        } else {
            header("Location: $url", true, 301);
            exit();
        }
    }


    /**
     * 输出一个json格式的应答。
     *
     * @param mixed $data
     */
    public static function json($data)
    {
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode($data);
    }


    /**
     * 输出一个jsonp格式的应答。
     *
     * @param mixed $data
     */
    public static function jsonp($data, $callback)
    {
        header('Content-Type:application/json; charset=utf-8');
        echo "$callback(" . json_encode($data) . ");";
    }
}
