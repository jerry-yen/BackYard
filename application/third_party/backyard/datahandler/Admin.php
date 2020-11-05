<?php

/**
 * 後花園 - 管理者資料處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\datahandler;

class Admin extends \backyard\Package
{

    /**
     * 取得後設資料
     * 
     * @param string $code 代碼
     */
    public function getMetadata($code)
    {
        $response = $this->backyard->data->getItem('module', array(), array('code' => $code, 'type' => 'metadata'));
        $metadata = ($response['status'] == 'success') ? $response['result'] : array();

        return array('status' => 'success', 'metadata' => json_decode($metadata, true));
    }

    /**
     * 取得所有後設資料
     */
    public function getMetadatas()
    {
        return array('status' => 'success', 'metadata' => array());
    }

    /**
     * 取得組件後設資料
     * 
     * @param strin $code 代碼
     */
    public function getMetadataOfWidget($code)
    {
        $response = $this->backyard->data->getItem('module', array(), array('code' => $code, 'type' => 'widget'));
        $metadata = ($response['status'] == 'success') ? $response['result'] : array();

        return array('status' => 'success', 'metadata' => json_decode($metadata, true));
    }

    /**
     * 取得頁面後設資料
     * 
     * @param strin $code 代碼
     */
    public function getMetadataOfPage($code)
    {
        $response = $this->backyard->data->getItem('module', array(), array('code' => $code, 'type' => 'page'));
        $metadata = ($response['status'] == 'success') ? $response['result'] : array();

        return array('status' => 'success', 'metadata' => json_decode($metadata, true));
    }

    /**
     * 取得版面後設資料
     * 
     * @param strin $code 代碼
     */
    public function getMetadataOfTemplate()
    {
        $this->backyard->config->loadConfigFile('master');
        $master = $this->backyard->config->getConfig('master');
        return array('status' => 'success', 'metadata' => $master['template']);
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
    public function convertToDatabase(&$table, $id, $value)
    {
        return $value;
    }
}
