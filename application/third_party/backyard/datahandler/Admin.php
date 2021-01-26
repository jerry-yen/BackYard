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
     * 取得資料集後設資料
     * 
     * @param string $code 代碼
     */
    public function getDataset($code)
    {
        $response = $this->backyard->data->getItem(array('code' => $code, 'config_type' => 'dataset'));
        $dataset = ($response['status'] == 'success') ? $response['item'] : array();
        $dataset['fields'] = json_decode($dataset['fields'], true);
        return array('status' => 'success', 'dataset' => $dataset);
    }

    /**
     * 取得組件後設資料
     * 
     * @param strin $code 代碼
     */
    public function getMetadataOfWidget($code)
    {
        $response = $this->backyard->data->getItem(array('code' => $code,'config_type' => 'widget'));
        $metadata = ($response['status'] == 'success') ? $response['item'] : array();

        return array('status' => 'success', 'metadata' => $metadata);
    }

    /**
     * 取得頁面後設資料
     * 
     * @param strin $code 代碼
     */
    public function getMetadataOfPage($code)
    {
        $response = $this->backyard->data->getItem(array('code' => $code, 'config_type' => 'content'));
        return array('status' => 'success', 'metadata' => $response['item']);
    }

    public function getSystemInformation()
    {
        $response = $this->backyard->data->getItem(array('code' => '', 'config_type' => 'login'));
        return array('status' => 'success', 'metadata' => $response['item']);
    }

    /**
     * 將一般欄位的資料轉換成資料庫條件
     * 
     * @param array $value 條件值
     * 
     * @return array 資料庫資料
     */
    public function convertToWhere($value)
    {
        $module = array();
        if (isset($value['id'])) {
            $module['id'] = $value['id'];
        }
        if (isset($value['created_at'])) {
            $module['created_at'] = $value['created_at'];
        }
        if (isset($value['updated_at'])) {
            $module['updated_at'] = $value['updated_at'];
        }
        if (isset($value['code'])) {
            $module['code'] = $value['code'];
        }

        if (isset($value['config_type'])) {
            $module['config_type'] = $value['config_type'];
        }
        $table = get_instance()->db->dbprefix . 'module';
        return array('table' => $table, 'where' => $module);
    }


    /**
     * 取得版面後設資料
     * 
     * @param strin $code 代碼
     */
    public function getMetadataOfTemplate($code)
    {
        $response = $this->backyard->data->getItem(array('config_type' => $code));
        if(is_null($response['item'])){
            $response['item']['widgets'] = array();
        }
        return array('status' => 'success', 'metadata' => $response['item']);
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

    /**
     * 總管理者登入
     * @param array $data
     */
    public function login($data)
    {
        $response = $this->backyard->data->getItems(array('code' => '', 'type' => 'account'));

        if ($response['status'] != 'success') {
            return array('status' => 'failed', 'message' => '開發者設定錯誤');
        }

        if (count($response['results']) == 0) {
            return array('status' => 'failed', 'message' => '未設定管理者');
        }

        foreach ($response['results'] as $result) {
            if (
                $data['account'] == $result['account']
                && $data['password'] == $result['password']
            ) {
                return array('status' => 'success', 'message' => '登入成功', 'landing_page' => 'page/login');
            }
        }

        return array('status' => 'failed', 'message' => '帳號或密碼錯誤');
    }
}
