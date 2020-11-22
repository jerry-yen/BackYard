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
    public function createTable($code, $fields = array())
    {
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
                    KEY `code_index` (`code`),
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

        // 新增及修改開發者的自訂欄位
    }

    /**
     * 取得多筆資料記錄
     * @param string $code 模組代碼
     * @param array $fields 指定欄位
     * @param array $where 搜尋條件
     * @param array $sort 排序條件
     * @param int $count 顯示筆數
     * @param boolean $pagination 是否分頁
     * 
     * @return array
     */
    public function getItems($code, $fields = array(), $where = array(), $sort = array(), $count = 10, $pagination = false)
    {
        if ($this->userType == 'master') {
            $where['code'] = $code;
            $code = 'module';
        }

        /*
         * 搜尋條件要過濾掉資料表中沒有的欄位
         */

        // 取得資料表中的所有欄位
        $tableFields = $this->database->list_fields($this->database->dbprefix . $code);
        foreach ($tableFields as $key => $field) {
            $tableFields[$field] = true;
            unset($tableFields[$key]);
        }
        // 過濾要取得的欄位
        foreach ($fields as $key => $value) {
            if (!isset($tableFields[$value])) {
                unset($fields[$key]);
            }
        }
        // 過濾要搜尋的條件
        foreach ($where as $key => $value) {
            if (!isset($tableFields[$key])) {
                unset($where[$key]);
            }
        }

        // 欄位
        $this->database = $this->database->select((count($fields) == 0) ? '*' : (implode(',', $fields)));

        // 表單
        $this->database = $this->database->from($this->database->dbprefix . $code);

        // 條件
        $this->database = $this->database->where($where);

        // 排序
        foreach ($sort as $key => $method) {
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
            $page = isset($page) ? $page : 1;
            $offset = ($page - 1) * $count;
            $this->database = $this->database->limit($offset, $count);
        }

        // 取得結果
        $results = $this->database->get()->result_array();

        if ($this->userType == 'master') {
            foreach ($results as $key => $result) {
                $data = json_decode($result['metadata'], true);
                $result = array_merge($result, $data);
                unset($result['metadata']);
                $results[$key] = $result;
            }
        }


        return array('status' => 'success', 'total' => $total, 'results' => $results);
    }

    /**
     * 取得單筆資料記錄
     * @param string $code 模組代碼
     * @param array $fields 指定欄位
     * @param array $where 搜尋條件
     * 
     * @return array
     */
    public function getItem()
    {

        $inputs = $this->backyard->getInputs();
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
    public function insertItem($exValues = array())
    {
        $inputs = $this->backyard->getInputs();
        if (!isset($inputs['code'])) {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }
        $response = $this->backyard->dataset->getItem($inputs['code']);
        if ($response['status'] == 'failed') {
            return $response;
        }

        // 額外處理過的欄位值
        if (count($exValues) > 0) {
            $inputs = array_merge($inputs, $exValues);
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
    public function updateItem($exValues = array())
    {
        $inputs = $this->backyard->getInputs();
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

        // 額外處理過的欄位值
        if (count($exValues) > 0) {
            $inputs = array_merge($inputs, $exValues);
        }

        // 驗證輸入參數
        $response = $this->backyard->validator->checkInputs($response['dataset'], $inputs);
        if ($response['status'] == 'failed') {
            return $response;
        }
        $inputs = $response['fields'];

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
     * 
     * @param string $code 模組代碼(或資料庫名稱)
     * @param string $id
     */
    public function deleteItem($code, $id)
    {

        if (!isset($id) || is_null($id)) {
            throw new \Exception('刪除資料表記錄:缺少識別碼');
        }

        if ($this->userType == 'master') {
            $code = $this->database->dbprefix . 'module';
        }

        // 刪除記錄
        $this->database->where('id', $id);
        $this->database->delete($code);
    }

    /**
     * 儲存記錄 ( 不在存就新增，如存在就更新)
     * @param string $code 模組代碼(或資料庫名稱)
     * @param string $id
     * @param array $value 欄位及值
     * 
     * @param string GUID 更新記錄的ID
     */
    public function saveItem($code, $id, $value = array())
    {

        // 資料存在則更新
        if (!is_null($id) && trim($id) != '') {
            $response = $this->getItem($code, array('id'), array('id' => $id));
            if ($response['status'] == 'success') {
                return $this->updateItem($code, $id, $value);
            }
        }

        // 找不到資料則新增
        $value['id'] = $id;
        return $this->insertItem($code, $value);
    }
}
