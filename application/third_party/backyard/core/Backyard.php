<?php

/**
 * 後花園系統主程式
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class Backyard
{
    /**
     * @var string 使用者類型(Admin/Master)
     */
    private $userType = 'admin';

    /**
     * @var array POST + GET 輸入值
     */
    private $inputs = array();

    /**
     * @var 套件
     */
    private $packages = array();

    /**
     * 建構子
     */
    public function __construct($userType = 'admin')
    {

        // 使用者類型
        $this->userType = $userType;

        // 過濾IP
        $this->filterIPs();

        // 取得所有輸入變數(POST + GET)
        $this->getInputs();
    }

    /**
     * 魔術函式
     * 
     * @param string $method 函式名稱
     * @param array $arguments 參數
     */
    public function __call($method, $arguments)
    {
        foreach ($this->packages as $package) {
            if (method_exists($package, $method)) {
                $package->$method($arguments);
            }
        }
    }

    /**
     * 載入套件
     * 
     * @param string $packageName 套件名稱
     */
    public function loadPackage($packageName)
    {
        if (!isset($this->packages[$packageName])) {
            $this->packages[$packageName] = new $packageName();
        }
    }


    /**
     * 取得GET、POST資料
     */
    private function getInputs()
    {
        $this->inputs = array_merge(
            get_instance()->input->get(),
            get_instance()->input->post()
        );
    }

    /**
     * 過濾IP
     */
    private function filterIPs()
    {
        $security = new Security();
        $response = $security->filterIPs();
        if ($response['status'] == 'deny') {

            // [待處理]之後不能直接Exit，要轉向其他畫面
            exit('Deny Your IP');
        }
        unset($security);
    }

    /**
     * 取得單筆項目
     * 
     * @param array $exValues 額外處理過的值
     */
    public function getItem($exValues = array())
    {
        if (!isset($this->inputs['code'])) {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }
        $metadataObject = new Metadata($this->userType);
        $metadata = $metadataObject->getItem($this->inputs['code']);
        if ($metadata['status'] == 'failed') {
            return $metadata;
        } else {
            // 額外處理過的欄位值
            if (count($exValues) > 0) {
                $this->inputs = array_merge($this->inputs, $exValues);
            }

            // 驗證輸入參數
            $validator = new Validator();
            $res = $validator->checkInputs('form', $metadata['metadata'], $this->inputs);
            unset($validator);

            $database = new Database($this->userType);
            $res = $database->getItem($this->inputs['code'], array(), $res['fields']);
            unset($database);

            print_r($res);
        }
    }

    /**
     * 取得多筆項目
     * 
     * @param array $exValues 額外處理過的值
     */
    public function getItems($exValues = array())
    {
        if (!isset($this->inputs['code'])) {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }
        $metadataObject = new Metadata($this->userType);
        $metadata = $metadataObject->getItem($this->inputs['code']);
        if ($metadata['status'] == 'failed') {
            return $metadata;
        } else {
            // 額外處理過的欄位值
            if (count($exValues) > 0) {
                $this->inputs = array_merge($this->inputs, $exValues);
            }

            // 驗證輸入參數
            $validator = new Validator();
            $res = $validator->checkInputs('form', $metadata['metadata'], $this->inputs);
            unset($validator);

            // 取得資料
            $database = new Database($this->userType);
            $response = $database->getItems($this->inputs['code'], array(), $res['fields']);
            unset($database);


            if ($response['status'] != 'success') {
                return array('status' => 'failed');
            }

            // 轉換資料
            $converter = new Converter();
            foreach ($response['results'] as $key => $result) {
                $response['results'][$key] = $converter->checkOutputs('form', $metadata['metadata'], $result);
            }
            unset($converter);



            print_r($response);
        }
    }

    /**
     * 新增項目
     * 
     * @param array $exValues 額外處理過的值
     */
    public function insertItem($exValues = array())
    {
        if (!isset($this->inputs['code'])) {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }
        $metadataObject = new Metadata($this->userType);
        $metadata = $metadataObject->getItem($this->inputs['code']);
        if ($metadata['status'] == 'failed') {
            return $metadata;
        } else {

            // 額外處理過的欄位值
            if (count($exValues) > 0) {
                $this->inputs = array_merge($this->inputs, $exValues);
            }

            // 驗證輸入參數
            $validator = new Validator();
            $res = $validator->checkInputs('form', $metadata['metadata'], $this->inputs);
            unset($validator);

            if ($res['status'] == 'failed') {
                return $res;
            }

            // 輸入資料
            $database = new Database($this->userType);
            $database->insertItem($this->inputs['code'], $res['fields']);
            unset($database);
        }
    }

    /**
     * 更新項目
     * 
     * @param array $exValues 額外處理過的值
     */
    public function updateItem($exValues = array())
    {
        if (!isset($this->inputs['code'])) {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }
        $metadataObject = new Metadata($this->userType);
        $metadata = $metadataObject->getItem($this->inputs['code']);
        if ($metadata['status'] == 'failed') {
            return $metadata;
        } else {

            // 額外處理過的欄位值
            if (count($exValues) > 0) {
                $this->inputs = array_merge($this->inputs, $exValues);
            }

            // 驗證輸入參數
            $validator = new Validator();
            $res = $validator->checkInputs('form', $metadata['metadata'], $this->inputs);
            unset($validator);

            if ($res['status'] == 'failed') {
                return $res;
            }

            // 更新資料
            $database = new Database($this->userType);
            $database->updateItem($this->inputs['code'], $this->inputs['id'], $res['fields']);
            unset($database);
        }
    }


    /**
     * 刪除項目
     * 
     * @param array $exValues 額外處理過的值
     */
    public function deleteItem($exValues = array())
    {
        if (!isset($this->inputs['code'])) {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }
        $metadataObject = new Metadata($this->userType);
        $metadata = $metadataObject->getItem($this->inputs['code']);
        if ($metadata['status'] == 'failed') {
            return $metadata;
        } else {

            // 額外處理過的欄位值
            if (count($exValues) > 0) {
                $this->inputs = array_merge($this->inputs, $exValues);
            }

            // 驗證輸入參數
            $validator = new Validator();
            $res = $validator->checkInputs('form', $metadata['metadata'], $this->inputs);
            unset($validator);

            if ($res['status'] == 'failed') {
                return $res;
            }

            // 刪除資料
            $database = new Database($this->userType);
            $database->deleteItem($this->inputs['code'], $this->inputs['id']);
            unset($database);
        }
    }
}
