<?php

/**
 * 後花園 - 後設資料處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class Metadata extends \backyard\Package
{
    /**
     * 取得單筆後設(meta)資料
     * 
     * @param string $code 模組代碼
     */
    public function getItem($code)
    {

        return $this->backyard->getUser()->getMetadata($code);
        if ($this->userType == 'master') {
            // 取得設定檔中的Master設定
            $this->backyard->config->loadConfigFile('master');
            $master = $this->backyard->config->getConfig('master');

            if (!isset($master['metadata'][$code])) {
                return array('status' => 'failed', 'message' => '找不到Master設定');
            }

            return array('status' => 'success', 'metadata' => $master['metadata'][$code]);
        } else {
            $database = new Data();
            $response = $database->getItem('module', array(), array('code' => $code));
            $metadata = ($response['status'] == 'success') ? $response['result'] : array();

            return array('status' => 'success', 'metadata' => json_decode($metadata, true));
        }
    }

    /**
     * 取得多筆後設(meta)資料
     */
    public function getItems()
    {
        if ($this->userType == 'master') {
            // 取得設定檔中的Master設定
            $config = new Config('master');
            $master = $config->getConfig('master');
            return array('status' => 'success', 'metadata' => $master['metadata']);
        } else {
            return array('status' => 'failed', 'metadata' => array());
        }
    }
}

/**
 * 後設資料類別
 */
class MetadataUnit
{

    /**
     * 模組名稱
     */
    private $name;

    /**
     * @var 模組代碼
     */
    private $code;

    /**
     * @var 是否為分類模組
     */
    private $isCategory         = false;

    /**
     * @var 分類總層數限制
     */
    private $classLevelCount    = 0;

    /**
     * @var 是否為表單頁
     */
    private $isFormPage         = false;

    /**
     * @var 基本權限設定
     */
    private $permission         = array();

    /**
     * @var 表單欄位Metadata
     */
    private $formFields         = array();

    /**
     * @var 清單欄位Metadata
     */
    private $tableFields        = array();

    /**
     * @var 搜尋欄位 Metadata
     */
    private $searchFields       = array();

    /**
     * 取得模組名稱
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 設定模組名稱
     * 
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * 取得模組代碼
     * 
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 設定模組代碼
     * 
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * 取得是否為分類模組
     * 
     * @return boolean
     */
    public function isCategory()
    {
        return $this->isCategory;
    }

    /**
     * 設定是否為分類模組
     * 
     * @param string $isCategory
     */
    public function setIsCategory($isCategory)
    {
        $this->isCategory = $isCategory;
    }

    /**
     * 取得分類總層數
     * 
     * @return integer
     */
    public function getClassLevelCount()
    {
        return $this->classLevelCount;
    }

    /**
     * 設定分類總層數
     * 
     * @param integer $classLevelCount
     */
    public function setClassLevelCount($classLevelCount)
    {
        $this->classLevelCount = $classLevelCount;
    }

    /**
     * 取得是否為表單頁
     * 
     * @return boolean
     */
    public function isFormPage()
    {
        return $this->isFormPage;
    }

    /**
     * 設定是否為表單頁
     * 
     * @param integer $isFormPage
     */
    public function setIsFormPage($isFormPage)
    {
        $this->isFormPage = $isFormPage;
    }

    /**
     * 取得基本權限設定
     * 
     * @return array
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * 設定基本權限設定
     * 
     * @param array $permission
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;
    }

    /**
     * 取得表單欄位Metadata
     * 
     * @return array
     */
    public function getFormFields()
    {
        return $this->formFields;
    }

    /**
     * 設定表單欄位Metadata
     * 
     * @param array $formFields
     */
    public function setFormFields($formFields)
    {
        $this->formFields = $formFields;
    }

    /**
     * 取得清單欄位Metadata
     * 
     * @return array
     */
    public function getTableFields()
    {
        return $this->tableFields;
    }

    /**
     * 設定清單欄位Metadata
     * 
     * @param array $tableFields
     */
    public function setTableFields($tableFields)
    {
        $this->tableFields = $tableFields;
    }

    /**
     * 取得搜尋欄位 Metadata
     * 
     * @return array
     */
    public function getSearchFields()
    {
        return $this->searchFields;
    }

    /**
     * 設定搜尋欄位 Metadata
     * 
     * @param array $searchFields
     */
    public function setSearchFields($searchFields)
    {
        $this->searchFields = $searchFields;
    }
}

class MetadataField
{

    /**
     * @var 欄位名稱
     */
    private $name              = '';

    /**
     * @var 資料庫欄位名稱
     */
    private $dbVariable         = '';

    /**
     * @var 前端欄位名稱
     */
    private $frontendVariable   = '';

    /**
     * @var 呈現的元件
     */
    private $component          = '';

    /**
     * @var 驗證器
     */
    private $vaildator          = '';

    /**
     * @var 轉換器
     */
    private $converter          = '';

    /**
     * @var 資料來源
     */
    private $source             = array();

    /**
     * @var 提示訊息
     */
    private $fieldTip           = '';

    /**
     * 取得欄位名稱
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 設定欄位名稱
     * 
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * 取得資料庫欄位名稱
     * 
     * @return string
     */
    public function getDbVariable()
    {
        return $this->dbVariable;
    }

    /**
     * 設定資料庫欄位名稱
     * 
     * @param string $dbVariable
     */
    public function setDbVariable($dbVariable)
    {
        $this->dbVariable = $dbVariable;
    }

    /**
     * 取得前端欄位名稱
     * 
     * @return string
     */
    public function getFrontendVariable()
    {
        return $this->dbVariable;
    }

    /**
     * 設定前端欄位名稱
     * 
     * @param string $frontendVariable
     */
    public function setFrontendVariable($frontendVariable)
    {
        $this->frontendVariable = $frontendVariable;
    }

    /**
     * 取得呈現的元件
     * 
     * @return string
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * 設定呈現的元件
     * 
     * @param string $component
     */
    public function setComponent($component)
    {
        $this->component = $component;
    }

    /**
     * 取得驗證器
     * 
     * @return string
     */
    public function getVaildator()
    {
        return $this->vaildator;
    }

    /**
     * 設定驗證器
     * 
     * @param string $vaildator
     */
    public function setVaildator($vaildator)
    {
        $this->vaildator = $vaildator;
    }

    /**
     * 取得轉換器
     * 
     * @return string
     */
    public function getConverter()
    {
        return $this->converter;
    }

    /**
     * 設定轉換器
     * 
     * @param string $converter
     */
    public function setConverter($converter)
    {
        $this->converter = $converter;
    }

    /**
     * 取得資料來源
     * 
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * 設定資料來源
     * 
     * @param array $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * 取得提示訊息
     * 
     * @return string
     */
    public function getFieldTip()
    {
        return $this->fieldTip;
    }

    /**
     * 設定提示訊息
     * 
     * @param array $fieldTip
     */
    public function setFieldTip($fieldTip)
    {
        $this->fieldTip = $fieldTip;
    }
}

class MetadataSearch
{

    /**
     * @var 欄位名稱
     */
    private $name               = '';

    /**
     * @var 欄位名稱
     */
    private $variable         = '';

    /**
     * @var 呈現的元件
     */
    private $component          = '';

    /**
     * @var 驗證器
     */
    private $vaildator          = '';

    /**
     * @var 轉換器
     */
    private $converter          = '';

    /**
     * @var 資料來源
     */
    private $source             = array();

    /**
     * @var 提示訊息
     */
    private $fieldTip           = '';

    /**
     * @var SQL搜尋條件
     */
    private $sqlWhere           = '';

    /**
     * 取得欄位名稱
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 設定欄位名稱
     * 
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * 取得資料庫欄位名稱
     * 
     * @return string
     */
    public function getDbVariable()
    {
        return $this->dbVariable;
    }

    /**
     * 設定資料庫欄位名稱
     * 
     * @param string $dbVariable
     */
    public function setDbVariable($dbVariable)
    {
        $this->dbVariable = $dbVariable;
    }

    /**
     * 取得前端欄位名稱
     * 
     * @return string
     */
    public function getFrontendVariable()
    {
        return $this->dbVariable;
    }

    /**
     * 設定前端欄位名稱
     * 
     * @param string $frontendVariable
     */
    public function setFrontendVariable($frontendVariable)
    {
        $this->frontendVariable = $frontendVariable;
    }

    /**
     * 取得呈現的元件
     * 
     * @return string
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * 設定呈現的元件
     * 
     * @param string $component
     */
    public function setComponent($component)
    {
        $this->component = $component;
    }

    /**
     * 取得驗證器
     * 
     * @return string
     */
    public function getVaildator()
    {
        return $this->vaildator;
    }

    /**
     * 設定驗證器
     * 
     * @param string $vaildator
     */
    public function setVaildator($vaildator)
    {
        $this->vaildator = $vaildator;
    }

    /**
     * 取得轉換器
     * 
     * @return string
     */
    public function getConverter()
    {
        return $this->converter;
    }

    /**
     * 設定轉換器
     * 
     * @param string $converter
     */
    public function setConverter($converter)
    {
        $this->converter = $converter;
    }

    /**
     * 取得資料來源
     * 
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * 設定資料來源
     * 
     * @param array $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * 取得提示訊息
     * 
     * @return string
     */
    public function getFieldTip()
    {
        return $this->fieldTip;
    }

    /**
     * 設定提示訊息
     * 
     * @param array $fieldTip
     */
    public function setFieldTip($fieldTip)
    {
        $this->fieldTip = $fieldTip;
    }

    /**
     * 取得SQL搜尋條件
     * 
     * @return string
     */
    public function getSqlWhere()
    {
        return $this->sqlWhere;
    }

    /**
     * 設定SQL搜尋條件
     * 
     * @param array $sqlWhere
     */
    public function setSqlWhere($sqlWhere)
    {
        $this->sqlWhere = $sqlWhere;
    }
}
