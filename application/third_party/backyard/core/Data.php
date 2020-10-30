<?php

/**
 * 後花園 - 資料處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class Data
{
    /**
     * @var string 使用者類型(Admin/Master)
     */
    private $userType = 'admin';

    /**
     * @var 資料庫物件
     */
    private $database = null;

    /**
     * 建構子
     * 
     * @param string $userType 使用者類型(Admin/Master)
     * @param string $connectionName 連線代碼
     */
    public function __construct($userType = 'admin', $connectionName = 'default')
    {
        // 設定使用者類型
        $this->userType = $userType;

        // 從設定檔取得資料庫連線
        $config = new Config('database');
        $connectionConfigs = $config->getConfig('database');
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
    public function createTable($code, $metadata = array())
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
    public function getItem($code, $fields = array(), $where = array())
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

        // 取得結果
        $result = $this->database->get()->row_array();

        if ($this->userType == 'master') {
            $data = json_decode($result['metadata'], true);
            $result = array_merge($result, $data);
            unset($result['metadata']);
        }

        return array('status' => 'success', 'result' => $result);
    }

    /**
     * 新增記錄
     * 
     * @param string $code 模組代碼(或資料庫名稱)
     * @param array $value 欄位及值
     * 
     * @param string GUID 新增記錄的ID
     */
    public function insertItem($code, $value)
    {

        // 預設ID
        if (!isset($value['id'])) {
            $codeObject = new \backyard\libraries\Code();
            $value['id'] = $codeObject->getGUID();
        }

        // 預設建置時間
        if (!isset($value['created_at'])) {
            $value['created_at'] = date('Y-m-d H:i:s');
        }

        // 預設更新時間
        if (!isset($value['updated_at'])) {
            $value['updated_at'] = date('Y-m-d H:i:s');
        }

        if ($this->userType == 'master') {

            $module['id'] = $value['id'];
            $module['created_at'] = $value['created_at'];
            $module['updated_at'] = $value['updated_at'];
            $module['code'] = $code;
            $code = $this->database->dbprefix . 'module';

            unset($value['id']);
            unset($value['created_at']);
            unset($value['updated_at']);
            $module['metadata'] = json_encode($value, JSON_UNESCAPED_UNICODE);

            unset($value);

            // 整理好的值，重新付予給value變數
            $value = $module;
            unset($module);
        }

        // 新增記錄
        $this->database->insert($code, $value);

        return $value['id'];
    }

    /**
     * 更新記錄
     * 
     * @param string $code 模組代碼(或資料庫名稱)
     * @param string $id
     * @param array $value 欄位及值
     * 
     * @param string GUID 更新記錄的ID
     */
    public function updateItem($code, $id, $value)
    {

        if (!isset($id) || is_null($id)) {
            throw new \Exception('更新資料表記錄:缺少識別碼');
        }

        // 預設更新時間
        if (!isset($value['updated_at'])) {
            $value['updated_at'] = date('Y-m-d H:i:s');
        }

        if ($this->userType == 'master') {

            $module['id'] = $id;
            $module['updated_at'] = $value['updated_at'];
            $module['code'] = $code;
            $code = $this->database->dbprefix . 'module';

            unset($value['id']);
            unset($value['updated_at']);
            $module['metadata'] = json_encode($value, JSON_UNESCAPED_UNICODE);

            unset($value);

            // 整理好的值，重新付予給value變數
            $value = $module;
            unset($module);
        }

        // 更新記錄
        $this->database->where('id', $id);
        $this->database->update($code, $value);

        return $id;
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
