<?php

/**
 * 後花園 - 前端頁面處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\packages\frontend;

class Page
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
     * 建構子
     */
    public function __construct()
    {
        $config = new \backyard\core\Config('frontend');
        $this->viewPath = $config->getConfig('frontend')['viewPath'];
    }

    /**
     * 取得頁面HTML語法
     */
    public function render()
    {
        $content = file_get_contents($this->viewPath . '/full.html');
        echo $this->refinePathInHtmlContent($content);
    }
}
