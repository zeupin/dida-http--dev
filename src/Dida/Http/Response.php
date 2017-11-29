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
     * @param string $url
     */
    public static function redirect($url)
    {
        header("Location: $url");
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
