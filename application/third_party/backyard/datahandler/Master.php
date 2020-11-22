<?php

/**
 * 後花園 - 開發者資料處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\datahandler;

class Master extends \backyard\Package
{
    /**
     * 取得後設資料
     * 
     * @param strin $code 代碼
     */
    public function getMetadata($code)
    {
        $this->backyard->config->loadConfigFile('master');
        $master = $this->backyard->config->getConfig('master');
        if (!isset($master['metadata'][$code])) {
            return array('status' => 'failed', 'message' => '找不到Master設定');
        } else {
            return array('status' => 'success', 'metadata' => $master['metadata'][$code]);
        }
    }

    /**
     * 取得所有後設資料
     */
    public function getMetadatas()
    {
        $this->backyard->config->loadConfigFile('master');
        $master = $this->backyard->config->getConfig('master');
        return array('status' => 'success', 'metadata' => $master['metadata']);
    }

    /**
     * 取得組件後設資料
     * 
     * @param strin $code 代碼
     */
    public function getMetadataOfWidget($code)
    {
        $this->backyard->config->loadConfigFile('master');
        $master = $this->backyard->config->getConfig('master');
        return array('status' => 'success', 'metadata' => $master['widget'][$code]);
    }

    /**
     * 取得頁面後設資料
     * 
     * @param strin $code 代碼
     */
    public function getMetadataOfPage($code)
    {
        $this->backyard->config->loadConfigFile('master');
        $master = $this->backyard->config->getConfig('master');
        return array('status' => 'success', 'metadata' => $master['page'][$code]);
    }

    /**
     * 取得版面後設資料
     * 
     * @param strin $code 代碼
     */
    public function getMetadataOfTemplate($code)
    {
        $this->backyard->config->loadConfigFile('master');
        $master = $this->backyard->config->getConfig('master');
        return array('status' => 'success', 'metadata' => $master['template'][$code]);
    }

    /**
     * 將資料庫資料轉換成一般欄位的資料
     * 
     * @param array $result 資料庫資料
     * @return array 一般欄位
     */
    public function convertToData($result)
    {
        if (isset($result['metadata'])) {
            $data = json_decode($result['metadata'], true);
            $result = array_merge($result, $data);
            unset($result['metadata']);
        }

        return $result;
    }

    /**
     * 將一般欄位的資料轉換成資料庫資料
     * 
     * @param string $table 資料表名稱
     * @param string $id 資料識別碼
     * @param array $result 資料庫的值
     * 
     * @return array 資料庫資料
     */
    public function convertToDatabase($id, $value)
    {
        $module['id'] = $id;
        $module['created_at'] = $value['created_at'];
        $module['updated_at'] = $value['updated_at'];
        $module['code'] = $value['code'];
        $table = get_instance()->db->dbprefix . 'module';

        unset($value['id']);
        unset($value['created_at']);
        unset($value['updated_at']);
        unset($value['code']);
        $module['metadata'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        unset($value);

        // 整理好的值，重新付予給value變數
        $value = $module;
        unset($module);
        return array('table' => $table, 'value' => $value);
    }
}
