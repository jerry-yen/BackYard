<?php

/**
 * 後花園 - 設定管理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class Config
{
    /**
     * @var 設定目錄
     */
    private $configRootPath = APPPATH . 'third_party/backyard/config';

    /**
     * @var 設定值
     */
    private $config = array();

    /**
     * 建構子
     * 
     * @param string $configFile 設定檔名稱(不需副檔名)
     */
    public function __construct($configFile)
    {
        require_once($this->configRootPath . '/' . $configFile . '.php');
        $this->config = $config;
    }

    /**
     * 取得設定值
     * @param string $field 欄位名稱
     * 
     * @return $value
     */
    public function getConfig($field)
    {
        return $this->config[$field];
    }
}
