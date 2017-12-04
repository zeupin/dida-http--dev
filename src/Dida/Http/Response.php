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
            header("Cache-control: no-cache");
            header("Refresh: $refresh; url=$url");
        } else {
            header("Cache-control: no-cache");
            header("Location: $url", true, 307);
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


    /**
     * 输出一个文件下载。
     *
     * @param string $srcfile   服务器上源文件的文件名。
     * @param string $name      下载时的文件名。如果为null，则默认使用srcfile的文件名。
     * @param boolean $mime     是否需要设置文件的mime。
     *
     * @return boolean
     */
    public static function download($srcfile, $name = null, $mime = false)
    {
        // 检查待下载的源文件是否存在。
        if (file_exists($srcfile)) {
            $realfile = $srcfile;
        } else {
            // 测试是否是因为中文字符编码导致的问题
            $realfile = iconv('UTF-8', 'GBK', $srcfile);
            if (!file_exists($realfile)) {
                return false;
            }
        }

        // 下载的文件名。
        // 本来是用PHP自带basename()函数，但是basename()处理中文文件名时要先setlocale(LC_ALL, 'PRC')，
        // 不然会处理错误。而setlocale()函数不一定所有服务器都能支持，所以换成用如下代码填坑。
        if (!is_string($name)) {
            $name = $srcfile;
        }
        $name = str_replace('\\', '/', $name);
        $basename = mb_strrchr($name, '/');
        if ($basename) {
            $name = mb_substr($basename, 1);
        }

        // 对下载文件名按照RFC3896进行rawurlencode编码，以支持中文文件名。
        $name = rawurlencode($name);

        // 如果需要自动设置mime，调用php的mime_content_type()函数来处理。
        if ($mime) {
            $mimetype = mime_content_type($realfile);
        } else {
            $mimetype = 'application/force-download';
        }

        // 文件大小
        $filesize = filesize($realfile);

        // 输出
        header("Content-Type: $mimetype");
        header("Content-Disposition: attachment; filename*=\"$name\"");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header("Content-Length: $filesize");
        ob_clean();
        flush();
        readfile($realfile);
        exit();
    }
}
