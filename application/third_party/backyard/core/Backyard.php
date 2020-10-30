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
     * 魔術函式 - 動態函式呼叫
     * 
     * @param string $method 函式名稱
     * @param array $arguments 參數
     */
    public function __call($method, $arguments)
    {
        foreach ($this->packages as $classes) {
            foreach ($classes as $classObject) {
                if (method_exists($classObject, $method)) {
                    $classObject->$method($arguments);
                    return;
                }
            }
        }
    }

    /**
     * 魔術函式 - 動態變數值取得
     * 
     * @param string $name 變數名稱
     * 
     * @return Object
     */
    public function __get($name)
    {
        foreach ($this->packages as $classes) {
            foreach ($classes as $className => $classObject) {
                if (strtolower($name) == strtolower($className)) {
                    return $classObject;
                }
            }
        }

        return null;
    }

    /**
     * 載入套件
     * 
     * @param string $packageName 套件名稱
     */
    public function loadPackage($packageName)
    {
        $namespace = '\\backyard\\packages\\' . $packageName;

        if (!isset($this->packages[$packageName])) {
            $this->packages[$packageName] = array();
        }

        $packagePath = dirname(dirname(__FILE__)) . '/packages/' . $packageName;
        $classFiles = scandir($packagePath);

        foreach ($classFiles as $file) {
            // 不處理目錄
            if (
                in_array($file, array('.', '..')) ||
                is_dir($packagePath . '/' . $file)
            ) {
                continue;
            }

            $ext = substr(strrchr($file, '.'), 1);
            if (in_array($ext, array('php'))) {
                // 載入套件檔案
                require_once($packagePath . '/' . $file);
                $dot = strripos($file, '.');
                $className = substr($file, 0, ($dot !== false) ? $dot : strlen($file));

                if (!isset($this->packages[$packageName][$className])) {
                    $classPath = $namespace . '\\' . $className;
                    $this->packages[$packageName][$className] = new $classPath();
                }
            }
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

            $data = new Data($this->userType);
            $res = $data->getItem($this->inputs['code'], array(), $res['fields']);
            unset($data);

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
            $data = new Data($this->userType);
            $response = $data->getItems($this->inputs['code'], array(), $res['fields']);
            unset($data);


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
            $data = new Data($this->userType);
            $data->insertItem($this->inputs['code'], $res['fields']);
            unset($data);
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
            $data = new Data($this->userType);
            $data->updateItem($this->inputs['code'], $this->inputs['id'], $res['fields']);
            unset($data);
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
            $data = new Data($this->userType);
            $data->deleteItem($this->inputs['code'], $this->inputs['id']);
            unset($data);
        }
    }
}
