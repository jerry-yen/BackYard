<?php

/**
 * 後花園系統主程式
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard;

class Backyard
{
    /**
     * var Object 使用者
     */
    private $user = null;

    /**
     * @var array POST + GET 輸入值
     */
    private $inputs = array();

    /**
     * @var 套件
     */
    private $packages = array();

    /**
     * @var 函式
     */
    private $libraries = array();

    /**
     * 建構子
     */
    public function __construct()
    {

        $this->loadCorePackage();

        // 過濾IP
        $this->filterIPs();

        // 取得所有輸入變數(POST + GET)
        $this->mergeInputs();
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

        foreach ($this->libraries as $className => $classObject) {
            if (strtolower($name) == strtolower($className)) {
                return $classObject;
            }
        }

        return null;
    }

    /**
     * 載入套件
     * 
     * @param string $packageName 套件名稱
     * @param string $namespace 命名空間
     */
    private function loadingPackage($packageName, $namespace, $packagePath)
    {
        if (!isset($this->packages[$packageName])) {
            $this->packages[$packageName] = array();
        }

        $packagePath = $packagePath . '/' . $packageName;
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
                    $this->packages[$packageName][$className] = new $classPath($this);
                }
            }
        }
    }

    /**
     * 載入核心套件
     * 
     * @param string $packageName 套件名稱
     */
    private function loadCorePackage()
    {
        $packageName = 'core';
        $namespace = '\\backyard\\' . $packageName;
        $packagePath = dirname(__FILE__);
        $this->loadingPackage($packageName, $namespace, $packagePath);
    }

    /**
     * 載入擴充套件
     * 
     * @param string $packageName 套件名稱
     */
    public function loadPackage($packageName)
    {
        $namespace = '\\backyard\\packages\\' . $packageName;
        $packagePath = dirname(__FILE__) . '/packages';
        $this->loadingPackage($packageName, $namespace, $packagePath);
    }

    public function loadLibrary($libraryName)
    {
        $namespace = '\\backyard\\libraries';
        $libraryPath = dirname(__FILE__) . '/libraries';

        // 載入套件檔案
        require_once($libraryPath . '/' . $libraryName . '.php');

        if (!isset($this->libraries[$libraryName])) {
            $classPath = $namespace . '\\' . $libraryName;
            $this->libraries[$libraryName] = new $classPath();
        }
    }

    /**
     * 設定使用者
     * 
     * @param Object $user (master:開發者, admin:管理者)
     */
    public function setUser($userType = 'admin')
    {
        $namespace = '\\backyard\\datahandler';
        $className = ucfirst($userType);
        require_once(dirname(__FILE__) . '/datahandler/' . $className . '.php');

        $className = $namespace . '\\' . $className;
        $this->user = new $className($this);
    }

    /**
     * 取得使用者類型
     * 
     * @return Object
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * 取得GET、POST資料
     */
    private function mergeInputs()
    {
        // 專用於 Restful API時，取得 GET 所使用
        if (method_exists(\CI_Controller::get_instance(), 'get')) {
            $this->inputs = array_merge(
                \CI_Controller::get_instance()->get(),
                get_instance()->input->post()
            );
        } else {
            $this->inputs = array_merge(
                get_instance()->input->get(),
                get_instance()->input->post()
            );
        }
    }

    /**
     * 過濾IP
     */
    private function filterIPs()
    {
        $response = $this->security->filterIPs();
        if ($response['status'] == 'deny') {

            // [待處理]之後不能直接Exit，要轉向其他畫面
            exit('Deny Your IP');
        }
        unset($security);
    }

    public function getInputs()
    {
        return $this->inputs;
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

        $metadata = $this->user->getMetadata($this->inputs['code']);
        if ($metadata['status'] == 'failed') {
            return $metadata;
        } else {
            // 額外處理過的欄位值
            if (count($exValues) > 0) {
                $this->inputs = array_merge($this->inputs, $exValues);
            }

            // 驗證輸入參數
            $validator = new \backyard\core\Validator();
            $res = $validator->checkInputs($metadata['metadata'], $this->inputs);
            unset($validator);

            $data = new \backyard\core\Data($this->userType);
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
        $metadataObject = new \backyard\core\Metadata($this->userType);
        $metadata = $metadataObject->getItem($this->inputs['code']);
        if ($metadata['status'] == 'failed') {
            return $metadata;
        } else {
            // 額外處理過的欄位值
            if (count($exValues) > 0) {
                $this->inputs = array_merge($this->inputs, $exValues);
            }

            // 驗證輸入參數
            $validator = new \backyard\core\Validator();
            $res = $validator->checkInputs('form', $metadata['metadata'], $this->inputs);
            unset($validator);

            // 取得資料
            $data = new \backyard\core\Data($this->userType);
            $response = $data->getItems($this->inputs['code'], array(), $res['fields']);
            unset($data);


            if ($response['status'] != 'success') {
                return array('status' => 'failed');
            }

            // 轉換資料
            $converter = new \backyard\core\Converter();
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
     * @param string $code 模組代碼
     * @param array $exValues 額外處理過的值
     */
    public function insertItem($code, $exValues = array())
    {
        if (!isset($code)) {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }
        $metadataObject = new \backyard\core\Metadata($this->userType);
        $metadata = $metadataObject->getItem($code);
        if ($metadata['status'] == 'failed') {
            return $metadata;
        } else {

            // 額外處理過的欄位值
            if (count($exValues) > 0) {
                $this->inputs = array_merge($this->inputs, $exValues);
            }

            // 驗證輸入參數
            $validator = new \backyard\core\Validator();
            $res = $validator->checkInputs('form', $metadata['metadata'], $this->inputs);
            unset($validator);

            if ($res['status'] == 'failed') {
                return $res;
            }

            // 輸入資料
            $data = new \backyard\core\Data($this->userType);
            $data->insertItem($code, $res['fields']);
            unset($data);
        }
    }

    /**
     * 更新項目
     * 
     * @param string $code 模組代碼
     * @param array $exValues 額外處理過的值
     */
    public function updateItem($code, $exValues = array())
    {
        if (!isset($this->inputs['code'])) {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }
        $metadataObject = new \backyard\core\Metadata($this->userType);
        $metadata = $metadataObject->getItem($this->inputs['code']);
        if ($metadata['status'] == 'failed') {
            return $metadata;
        } else {

            // 額外處理過的欄位值
            if (count($exValues) > 0) {
                $this->inputs = array_merge($this->inputs, $exValues);
            }

            // 驗證輸入參數
            $validator = new \backyard\core\Validator();
            $res = $validator->checkInputs('form', $metadata['metadata'], $this->inputs);
            unset($validator);

            if ($res['status'] == 'failed') {
                return $res;
            }

            // 更新資料
            $data = new \backyard\core\Data($this->userType);
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
        $metadataObject = new \backyard\core\Metadata($this->userType);
        $metadata = $metadataObject->getItem($this->inputs['code']);
        if ($metadata['status'] == 'failed') {
            return $metadata;
        } else {

            // 額外處理過的欄位值
            if (count($exValues) > 0) {
                $this->inputs = array_merge($this->inputs, $exValues);
            }

            // 驗證輸入參數
            $validator = new \backyard\core\Validator();
            $res = $validator->checkInputs('form', $metadata['metadata'], $this->inputs);
            unset($validator);

            if ($res['status'] == 'failed') {
                return $res;
            }

            // 刪除資料
            $data = new \backyard\core\Data($this->userType);
            $data->deleteItem($this->inputs['code'], $this->inputs['id']);
            unset($data);
        }
    }
}
