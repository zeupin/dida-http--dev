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
 * HttpStatusCode
 */
class HttpStatusCode
{
    /**
     * Version
     */
    const VERSION = '20171206';


    /**
     * 检查是否是一个有效的HTTP状态码。
     *
     * 一个有效的 HTTP 状态码是一个三位数，由 RFC 2616 规范定义的，参阅百度百科“HTTP状态码”词条。
     * 1xx  消息
     * 2xx  成功
     * 3xx  重定向
     * 4xx  请求错误
     * 5xx  服务器错误
     * 6xx  其它错误
     *
     * @param int|string  $http_status_code
     *
     * @return boolean
     */
    public static function validate($http_status_code)
    {
        if (!is_int($http_status_code)) {
            // 如果不是数字
            if (!is_numeric($http_status_code)) {
                return false;
            };

            // 检查是否是整数
            $code = intval($http_status_code);
            if ("$code" !== "$http_status_code") {
                return false;
            }
        } else {
            $code = $http_status_code;
        }

        // 检查范围
        if ($code > 99 && $code < 700) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 发送一个特定的应答码
     * @param int $response_code
     */
    public static function send($response_code)
    {
        http_response_code($response_code);
    }
}
