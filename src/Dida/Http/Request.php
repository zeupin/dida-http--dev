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
     * @return string|bool 正常返回读取的ip，异常返回false
     */
    public static function getClientIP()
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
}
