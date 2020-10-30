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
     * 
     * @param strin $code 代碼
     */
    public function getMetadatas()
    {
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
    public function convertToDatabase(& $table, $id, $value)
    {
        $module['id'] = $id;
        $module['created_at'] = $value['created_at'];
        $module['updated_at'] = $value['updated_at'];
        $module['code'] = $table;
        $table = $this->database->dbprefix . 'module';

        unset($value['id']);
        unset($value['created_at']);
        unset($value['updated_at']);
        $module['metadata'] = json_encode($value, JSON_UNESCAPED_UNICODE);

        unset($value);

        // 整理好的值，重新付予給value變數
        $value = $module;
        unset($module);
        return $value;
    }
}
