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


    /**
     * 输出一个json格式应答。
     *
     * @param mixed $data
     */
    public static function json($data)
    {
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode($data);
    }
}
