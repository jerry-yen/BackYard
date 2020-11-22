<?php

/**
 * 後花園 - 資料集後設資料處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class Dataset extends \backyard\Package
{
    /**
     * 取得單筆資料集後設(meta)資料
     * 
     * @param string $code 模組代碼
     */
    public function getItem($code)
    {
        return $this->backyard->getUser()->getDataset($code);
    }
}
