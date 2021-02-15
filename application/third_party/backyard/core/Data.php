<?php

/**
 * 後花園 - 資料處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class Data extends \backyard\Package
{
    /**
     * @var 資料庫物件
     */
    private $database = null;

    /**
     * 建構子
     */
    public function __construct(&$backyard)
    {
        parent::__construct($backyard);
        $this->connection();
    }

    public function connection($connectionName = 'default')
    {
        // 從設定檔取得資料庫連線
        $this->backyard->config->loadConfigFile('database');
        $connectionConfigs = $this->backyard->config->getConfig('database');
        $connectionConfig = isset($connectionConfigs[$connectionName]) ?
            $connectionConfigs[$connectionName] :
            $connectionConfigs['default'];
        get_instance()->load->database($connectionConfig);
        $this->database = get_instance()->db;
    }

    /**
     * 基本資料表安裝
     * 
     * @return null
     */
    public function install()
    {
        // 從設定檔取得建置基本資料表的SQL語法
        $config = new Config('install');
        $sqls = $config->getConfig('install');
        foreach ($sqls as $key => $sql) {
            $this->database->query($sql);
        }
    }

    /**
     * 新增資料表
     * 
     * @param string $code 模組代碼(資料表名稱)
     * @param array $fields 欄位資訊
     */
    public function createTable($inputs = array())
    {
        $code = $inputs['_code'];
        $fields = json_decode($inputs['fields'], true);

        // 表單名稱
        $tableName = $this->database->dbprefix . $code;

        // 新增表單，並設定內建的基本欄位
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $tableName . '(
                    id VARCHAR(40) NOT NULL PRIMARY KEY COMMENT "識別碼" COLLATE utf8_unicode_ci,
                    parent_id VARCHAR(40) NOT NULL COMMENT "上層識別碼" COLLATE utf8_unicode_ci,
                    domain_id VARCHAR(40) NULL COMMENT "網域識別碼" COLLATE utf8_unicode_ci,
                    member_id VARCHAR(40) NULL COMMENT "使用者識別碼" COLLATE utf8_unicode_ci,
                    visibility INT(11) NULL COMMENT "可見度：0:公開,1:私人,2:上級可看" COLLATE utf8_unicode_ci,
                    level INT(11) NULL COMMENT "層數(分類使用)" COLLATE utf8_unicode_ci,
                    created_at DATETIME COMMENT "建置時間" COLLATE utf8_unicode_ci,
                    updated_at DATETIME COMMENT "更新時間" COLLATE utf8_unicode_ci,
                    sorted_at DATETIME NULL COMMENT "排序時間" COLLATE utf8_unicode_ci,
                    sequence INT(11) NULL COMMENT "排列順序" COLLATE utf8_unicode_ci,
                    top_at DATETIME NULL COMMENT "置頂時間" COLLATE utf8_unicode_ci,
                    KEY `parent_id_index` (`parent_id`),
                    KEY `domain_id_index` (`domain_id`),
                    KEY `member_id_index` (`member_id`),
                    KEY `created_at_index` (`created_at`),
                    KEY `updated_at_index` (`updated_at`),
                    KEY `sorted_at_index` (`sorted_at`),
                    KEY `sequence_index` (`sequence`),
                    KEY `top_at_index` (`top_at`)
                ) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
        ';

        $this->database->query($sql);

        $defualtFields = array();
        $defualtFields['id'] = true;
        $defualtFields['parent_id'] = true;
        $defualtFields['domain_id'] = true;
        $defualtFields['member_id'] = true;
        $defualtFields['visibility'] = true;
        $defualtFields['level'] = true;
        $defualtFields['created_at'] = true;
        $defualtFields['updated_at'] = true;
        $defualtFields['sorted_at'] = true;
        $defualtFields['sequence'] = true;
        $defualtFields['top_at'] = true;

        // 抓出這個表單的所有欄位
        $dbFields = $this->database->list_fields($tableName);
        foreach ($dbFields as $key => $field) {
            $dbFields[$field] = $field;
            unset($dbFields[$key]);
        }


        // 刪除不在這次設定的欄位
        foreach ($fields as $key => $field) {
            $fieldType = 'varchar(10)';
            switch ($field['component']) {
                case 'slider':
                case 'number':
                    $fieldType = 'int(10)';
                    break;
                case 'select':
                case 'text':
                    $fieldType = 'varchar(100)';
                    break;
                case 'textarea':
                    $fieldType = 'varchar(255)';
                    break;
                case 'switch':
                    $fieldType = 'varchar(1)';
                    break;
                case 'text':
                    $fieldType = 'varchar(100)';
                    break;
                default:
                    $fieldType = 'varchar(100)';
            }

            $isNull = true;
            foreach ($field['validator'] as $validator) {
                if ($validator == 'required') {
                    $isNull = false;
                    break;
                }
            }

            // 新增
            if (!isset($dbFields[$field['dbVariable']]) && !isset($defualtFields[$field['dbVariable']])) {
                $sql = 'ALTER TABLE ' . $tableName . ' ADD COLUMN ' . $field['dbVariable'] . ' ' . $fieldType . ((!$isNull) ? ' NOT' : '') . ' NULL COMMENT "' . $field['name'] . '";';
            }
            // 修改
            else if (isset($dbFields[$field['dbVariable']])) {
                $sql = 'ALTER TABLE ' . $tableName . ' MODIFY COLUMN ' . $field['dbVariable'] . ' ' . $fieldType . ((!$isNull) ? ' NOT' : '') . ' NULL COMMENT "' . $field['name'] . '";';
            }

            $this->database->query($sql);

            $fields[$field['dbVariable']] = $field;
        }

        foreach ($dbFields as $field => $value) {
            if (!isset($fields[$field]) && !isset($defualtFields[$field])) {
                $sql = 'ALTER TABLE ' . $tableName . ' DROP COLUMN ' . $field . ';';
                $this->database->query($sql);
            }
        }
    }

    /**
     * 取得多筆資料記錄
     * @param int $count 顯示筆數
     * @param boolean $pagination 是否分頁
     * 
     * @return array
     */
    public function getItems($inputs = array(), $sort = array(), $count = 10, $pagination = true)
    {
        if (!isset($inputs['code'])) {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }

        $response = $this->backyard->getUser()->convertToWhere($inputs);
        $where = $response['where'];


        /*
         * 搜尋條件要過濾掉資料表中沒有的欄位
         */

        // 取得資料表中的所有欄位
        $tableFields = $this->database->list_fields($response['table']);
        foreach ($tableFields as $key => $field) {
            $tableFields[$field] = true;
            unset($tableFields[$key]);
        }

        // 過濾要取得的欄位
        if (isset($fields) && count($fields) > 0) {
            foreach ($fields as $key => $value) {
                if (!isset($tableFields[$value])) {
                    unset($fields[$key]);
                }
            }
        } else {
            $fields = array();
        }

        // 過濾要搜尋的條件
        if (isset($where) && count($where) > 0) {
            foreach ($where as $key => $value) {
                if (!isset($tableFields[$key])) {
                    unset($where[$key]);
                }
            }
        } else {
            $where = array();
        }
        // 欄位
        $this->database = $this->database->select((count($fields) == 0) ? '*' : (implode(',', $fields)));

        // 表單
        $this->database = $this->database->from($response['table']);

        // 條件
        $this->database = $this->database->where($where);

        // 排序
        if (!isset($sort) || !is_array($sort) || count($sort) == 0) {
            $sort = array(
                'top_at' => 'DESC',
                'sorted_at' => 'ASC',
                'sequence' => 'ASC',
                'created_at' => 'DESC',
                'updated_at' => 'DESC',
            );
        }


        foreach ($sort as $key => $method) {
            if(!isset($tableFields[$key])){
                continue;
            }
            $this->database = $this->database->order_by($key, $method);
        }


        // 取得總筆數
        $total = $this->database->count_all_results('', false);

        // 分頁處理
        if ($pagination) {
            $inputPage = get_instance()->input->get('page');
            $page = isset($inputPage) ?
                get_instance()->input->get('page') :
                get_instance()->input->post('page');

            $inputCount = get_instance()->input->get('count');
            $inputCount = isset($inputCount) ?
                get_instance()->input->get('count') :
                get_instance()->input->post('count');
            $count = isset($inputCount) ? $inputCount : $count;
            if ($count > $total || $count == -1) {
                $count = $total;
            }

            $totalPage = ceil($total / $count);
            $page = isset($page) ? $page : 1;
            $page = ($page < 1) ? 1 : $page;
            $page = ($page > $totalPage) ? $totalPage : $page;

            $offset = ($page - 1) * $count;
            $this->database = $this->database->limit($count, $offset);
        }

        // 取得結果
        $results = $this->database->get()->result_array();

        // 根據不同使用者，進行資料格式的轉換
        foreach ($results as $key => $result) {
            $results[$key] = $this->backyard->getUser()->convertToData($result);
        }

        return array(
            'status' => 'success',
            'total' => $total,
            'total_page' => ceil($total / $count),
            'current_page' => (int)(isset($page) ? $page : 1),
            'results' => $results
        );
    }

    /**
     * 更新多筆資料記錄
     * 
     * @return array
     */
    public function updateItems($inputs = array())
    {
        foreach ($inputs['condition'] as $key => $input) {
            $data = array(
                'code' => $inputs['code'],
                'id'   => $input,
            );

            foreach ($inputs['value'][$key] as $fieldName => $value) {
                $data[$fieldName] = $value;
            }

            $response = $this->updateItem($data, true);
            if ($response['status'] != 'success') {
                return $response;
            }
        }

        return array('status' => 'success');
    }

    /**
     * 取得單筆資料記錄
     * @param string $code 模組代碼
     * @param array $fields 指定欄位
     * @param array $where 搜尋條件
     * 
     * @return array
     */
    public function getItem($inputs = array())
    {
        /*
        if (!isset($inputs['code'])) {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }
        */
        $response = $this->backyard->getUser()->convertToWhere($inputs);
        $where = $response['where'];
        /*
         * 搜尋條件要過濾掉資料表中沒有的欄位
         */

        // 取得資料表中的所有欄位
        $tableFields = $this->database->list_fields($response['table']);
        foreach ($tableFields as $key => $field) {
            $tableFields[$field] = true;
            unset($tableFields[$key]);
        }
        // 過濾要取得的欄位
        if (isset($fields) && count($fields) > 0) {
            foreach ($fields as $key => $value) {
                if (!isset($tableFields[$value])) {
                    unset($fields[$key]);
                }
            }
        } else {
            $fields = array();
        }
        // 過濾要搜尋的條件
        if (isset($where) && count($where) > 0) {
            foreach ($where as $key => $value) {
                if (!isset($tableFields[$key])) {
                    unset($where[$key]);
                }
            }
        } else {
            $where = array();
        }

        // 欄位
        $this->database = $this->database->select((count($fields) == 0) ? '*' : (implode(',', $fields)));

        // 表單
        $this->database = $this->database->from($response['table']);

        // 條件
        $this->database = $this->database->where($where);

        // 取得結果
        $item = $this->database->get()->row_array();

        // 根據不同使用者，進行資料格式的轉換
        $item = $this->backyard->getUser()->convertToData($item);

        return array('status' => 'success', 'item' => $item);
    }

    /**
     * 新增記錄
     * 
     * @param array $exValues 額外處理過的值
     * 
     * @param string GUID 新增記錄的ID
     */
    public function insertItem($inputs = array())
    {
        if (!isset($inputs['code'])) {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }
        $response = $this->backyard->dataset->getItem($inputs['code']);
        if ($response['status'] == 'failed') {
            return $response;
        }

        // 驗證輸入參數
        $response = $this->backyard->validator->checkInputs($response['dataset'], $inputs);
        if ($response['status'] == 'failed') {
            return $response;
        }
        $inputs = $response['fields'];

        // 預設ID
        if (!isset($inputs['id'])) {
            $this->backyard->loadLibrary('Code');
            $inputs['id'] = $this->backyard->code->getGUID();
        }

        // 預設建置時間
        if (!isset($inputs['created_at'])) {
            $inputs['created_at'] = date('Y-m-d H:i:s');
        }

        // 預設更新時間
        if (!isset($inputs['updated_at'])) {
            $inputs['updated_at'] = date('Y-m-d H:i:s');
        }

        $response = $this->backyard->getUser()->convertToDatabase($inputs);

        // 新增記錄
        $this->database->insert($response['table'], $response['value']);

        return array('status' => 'success', 'id' => $inputs['id']);
    }

    /**
     * 更新記錄
     * 
     * @param array $exValues 額外處理過的值
     * 
     * @param string GUID 更新記錄的ID
     */
    public function updateItem($inputs = array(), $ignoreValidation = false)
    {
        if (!isset($inputs['code'])) {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }

        if (!isset($inputs['id']) || is_null($inputs['id'])) {
            return array('status' => 'failed', 'message' => '更新資料表記錄:缺少識別碼');
        }

        $response = $this->backyard->dataset->getItem($inputs['code']);
        if ($response['status'] == 'failed') {
            return $response;
        }

        if (!$ignoreValidation) {
            // 驗證輸入參數
            $response = $this->backyard->validator->checkInputs($response['dataset'], $inputs);
            if ($response['status'] == 'failed') {
                return $response;
            }

            $inputs = $response['fields'];
        }

        // 預設更新時間
        if (!isset($value['updated_at'])) {
            $value['updated_at'] = date('Y-m-d H:i:s');
        }

        $response = $this->backyard->getUser()->convertToDatabase($inputs);
        $value = $response['value'];
        
        // 更新記錄
        $this->database->where('id', $value['id']);
        $this->database->update($response['table'], $value);

        return array('status' => 'success', 'id' => $value['id']);
    }

    /**
     * 刪除記錄
     */
    public function deleteItem($inputs = array())
    {
        if (!isset($inputs['code'])) {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }

        if (!isset($inputs['id']) || is_null($inputs['id'])) {
            return array('status' => 'failed', 'message' => '刪除資料表記錄:缺少識別碼');
        }

        $response = $this->backyard->getUser()->convertToDatabase($inputs);
        $value = $response['value'];

        // 刪除記錄
        $this->database->where('id', $value['id']);
        $this->database->delete($response['table']);

        return array('status' => 'success');
    }
}
