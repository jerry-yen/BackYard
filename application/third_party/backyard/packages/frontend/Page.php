<?php

/**
 * 後花園 - 前端頁面處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\packages\frontend;

class Page extends \backyard\Package
{
    /**
     * @var View路徑
     */
    private $viewPath = '';

    /**
     * 修正HTML內容中的資源檔的路徑
     * 
     * @param string $content HTML內容
     * 
     * @return string
     */
    private function refinePathInHtmlContent($content)
    {
        // 待處理：根據 htaccess 環境變數的設定，來決定資源檔取代的路徑
        // $path = new \backyard\libraries\Path();
        // echo $path->relative($this->viewPath);
        $content = str_replace('{adminlte}', '/adminlte', $content);
        return $content;
    }

    /**
     * 取得頁面後設資料
     * 
     * @param string $code 代碼
     */
    public function getMetadata($code)
    {
        $page = $this->backyard->getUser()->getMetadataOfPage($code);
        if ($page['status'] != 'success') {
            return '頁面載入錯誤';
        }

        return $page;
    }

    /**
     * 取得頁面HTML語法
     * 
     * @param string $code 模組代碼
     */
    public function render($code)
    {
        // 取得View基本路徑
        $this->backyard->config->loadConfigFile('frontend');
        $this->viewPath = $this->backyard->config->getConfig('frontend')['viewPath'];

        // 取得頁面後設資料
        $page = $this->getMetadata($code);

        $content = file_get_contents($this->viewPath . '/full.html');
        $content = $this->refinePathInHtmlContent($content);
        $content = str_replace('{pageTitle}', $page['metadata']['name'], $content);

        return $content;
    }
}
