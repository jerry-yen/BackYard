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
        // "/application/third_party/backyard/packages/frontend/views/"
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
