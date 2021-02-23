<?php

/**
 * 後花園 - 檔案處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class File extends \backyard\Package
{
    /**
     * 取得檔案
     */
    public function getFiles($inputs = array())
    {
        $inputs['code'] = 'file';
        $response = $this->backyard->data->getItems($inputs, array(), 100000, false);
    }

    /**
     * 上傳檔案
     * 
     * @param string $code 模組代碼
     */
    public function upload($inputs = array())
    {
        if ($inputs['code'] == '') {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }

        if ($inputs['field'] == '') {
            return array('status' => 'failed', 'message' => '尚未設定上傳欄位');
        }

        // 取得 Dataset 設定
        $response = $this->backyard->dataset->getItem($inputs['code']);
        if ($response['status'] == 'failed') {
            return $response;
        }
        foreach ($response['dataset']['fields'] as $key => $field) {
            $response['dataset']['fields'][$field['frontendVariable']] = $field;
            unset($response['dataset']['fields'][$key]);
        }

        // 取得上傳欄位設定
        $field = $response['dataset']['fields'][$inputs['field']];

        // 取得檔案副檔名
        $ext = substr(strrchr($_FILES[$inputs['field']]['name'], '.'), 1);

        // 取得上傳目錄
        $this->backyard->config->loadConfigFile('file');
        $temporaryDir = $this->backyard->config->getConfig('file')['temporary_dir'];

        // 上傳Library設定
        get_instance()->load->library('upload', array(
            'upload_path'   => $temporaryDir,
            'allowed_types' => '*',
            'max_size'      => 100000000000,
            'file_name'     => uniqid('', true) . '.' . $ext
        ));

        // 上傳
        if (!get_instance()->upload->do_upload($inputs['field'])) {
            return array('status' => 'failed', 'message' => get_instance()->upload->display_errors());
        }

        // 取得相對路徑
        $file = get_instance()->upload->data();
        $file['short_path'] = str_replace($temporaryDir, '', $file['full_path']);
        $file['created_at'] = date('Y-m-d H:i:s');

        $this->backyard->loadLibrary('Code');
        if ($response['status'] == 'success') {
            return array('status' => 'success', 'file' => array(
                'id'        => $this->backyard->code->getGUID(),
                'name'      => $file['client_name'],
                'ext'       => $file['file_ext'],
                'file_type' => $file['file_type'],
                'path'      => $file['short_path'],
                'file_size' => $file['file_size'],
                'created_at' => date('Y-m-d H:i:s'),
            ));
        } else {
            return array('status' => 'failed', 'message' => '資料庫新增錯誤!');
        }
    }

    public function moveTemporaryToUploadDirectory($path)
    {
        // 取得上傳目錄
        $this->backyard->config->loadConfigFile('file');
        $baseUploadDir = $this->backyard->config->getConfig('file')['upload_dir'];
        $temporaryDir = $this->backyard->config->getConfig('file')['temporary_dir'];

        $temporary = $temporaryDir . $path;
        $upload = $baseUploadDir . $path;
        rename($temporary, $upload);
    }

    public function delete($file){
        $this->backyard->config->loadConfigFile('file');
        $baseUploadDir = $this->backyard->config->getConfig('file')['upload_dir'];
        if(file_exists($baseUploadDir . $file['path'])){
            unlink($baseUploadDir . $file['path']);
            return array('status' => 'success');
        }

        return array('status' => 'success', 'message' => '檔案不存在');
    }
}
