<?php

/**
 * 後花園 - 前端組件處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\packages\frontend;

class Widget extends \backyard\Package
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
     * 取得組件後設資料
     * 
     * @param string $code 代碼
     */
    public function getMetadata($code)
    {
        $widget = $this->backyard->getUser()->getMetadataOfWidget($code);
        if ($widget['status'] != 'success') {
            return '頁面載入錯誤';
        }

        return $widget;
    }

    /**
     * 取得組件HTML語法
     * 
     * @param string $code 模組代碼
     */
    public function render($code)
    {
        // 取得View基本路徑
        $this->backyard->config->loadConfigFile('frontend');
        $this->viewPath = $this->backyard->config->getConfig('frontend')['viewPath'];

        // 取得組件後設資料
        $metadata = $this->getMetadata($code);

        // 組件名稱
        $widget = $metadata['metadata']['widget'];

        // 取得組件內容
        if (file_exists($this->viewPath . 'widgets/' . $widget . '/template.php')) {
            $content = file_get_contents($this->viewPath . 'widgets/' . $widget . '/template.php');
            return $this->refinePathInHtmlContent($content);
        } else {
            return '找不到' . $widget . '組件介面(' . $this->viewPath . 'widgets/' . $widget . '/template.php' . ')';
        }
    }
}
