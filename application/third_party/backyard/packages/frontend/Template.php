<?php

/**
 * 後花園 - 前端頁面處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\packages\frontend;

class Template extends \backyard\Package
{
    /**
     * 取得版面後設資料
     * 
     * @param string $code 代碼
     */
    public function getMetadata($code)
    {
        $tempalte = $this->backyard->getUser()->getMetadataOfTemplate($code);
        if ($tempalte['status'] != 'success') {
            return '版面載入錯誤';
        }

        return $tempalte;
    }
}
