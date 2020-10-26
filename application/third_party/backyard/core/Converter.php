<?php

/**
 * 後花園 - 欄位(資料)轉換
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

require_once(APPPATH . '/third_party/backyard/converters/System.php');

class Converter
{

    /**
     * 轉換
     * 
     * @param string $fieldType 要轉換的欄位類型(form:表單輸出轉換,table:清單輸出轉換,search:搜尋輸出轉換)
     * @param array $metadata 後設資料
     * @param array $output 輸出資料
     * 
     * @return array status(success:成功,failed:失敗), message[$key:欄位變數](錯誤訊息)
     */
    public function checkOutputs($fieldType = 'form', $metadata, $output)
    {
        $fields = $metadata[$fieldType . 'Fields'];

        if (!isset($fields) || !is_array($fields)) {
            return array();
        }

        foreach ($fields as $field) {

            if (!isset($output[$field['dbVariable']])) {
                continue;
            }

            // 欄位值
            $value = $output[$field['dbVariable']];

            $output[$field['dbVariable']] =  $this->convert($value, $field['converter']);
        }

        return $output;
    }

    /**
     * 轉換
     * 
     * @param string $name 欄位名稱
     * @param string $variable 變數
     * @param string $value 值
     * @param array $converters 轉換器
     * 
     * @param boolean
     */
    private function convert($value, $converters)
    {

        // 轉換
        foreach ($converters as $converter) {

            $params = array();

            // 格式分析 (有些轉換指令，會有參數，有些沒有，例如：system.selectOne{Y:是,N:否} 或 system.absPath)
            if (preg_match('/(.*?)\{(.*?)\}/i', $converter, $res)) {
                $converter = $res[1];
                $params = explode(',', $res[2]);
            } else {
                $params = array();
            }

            $parts = explode('.', $converter);

            // 未指定類別
            if (count($parts) == 1) {
                $parts[1] = $parts[0];
                $parts[0] = 'System';
            } else {
                $parts[0] = ucfirst($parts[0]);
            }

            // 宣告轉換類別
            $classPath = '\\backyard\\converters\\' . $parts[0];
            $converterClass = new $classPath();

            // 確認轉換函數是否存在
            if (method_exists($converterClass, $parts[1])) {
                $response = $converterClass->{$parts[1]}($value, $params);
                if ($response['status'] == 'success') {
                    $value = $response['value'];
                }
            }
        }

        return $value;
    }
}
