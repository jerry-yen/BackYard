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
    }

    /**
     * 取得多筆後設(meta)資料
     */
    public function getItems()
    {
        return $this->backyard->getUser()->getMetadatas();
    }
}
