<?php

/**
 * 後花園 - 帳號處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\packages\account;

class User extends \backyard\Package
{
    public function login($inputs = array())
    {
        $code = $inputs['code'];
        // 加密次數
        $count = substr($code, -1);
        $code = rtrim($code, $count);
        // 解密
        for ($i = 0; $i < $count; $i++) {
            $code = base64_decode($code);
        }
        $data = json_decode($code, true);
        $response = $this->backyard->getUser()->login($data);
        return $response;
    }
}
