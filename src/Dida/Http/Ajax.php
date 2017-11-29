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
 * Ajax 主要用于和客户端的Ajax进行交互。
 */
class Ajax
{
    /**
     * Version
     */
    const VERSION = '20171128';


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
     * 输出一个jsonp格式的ajax应答。
     *
     * @param type $data
     * @param type $callback
     */
    public static function jsonp($data, $callback)
    {
        header('Content-Type:application/json; charset=utf-8');
        echo $callback . "(" . json_encode($data) . ");";
    }
}
