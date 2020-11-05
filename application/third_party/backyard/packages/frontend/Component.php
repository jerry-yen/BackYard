<?php

/**
 * 後花園 - 前端組件處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\packages\frontend;

class Component extends \backyard\Package
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
     * 取得元件HTML語法
     * 
     * @param string $code 模組代碼
     */
    public function getScript($code)
    {
        // 取得View基本路徑
        $this->backyard->config->loadConfigFile('frontend');
        $this->viewPath = $this->backyard->config->getConfig('frontend')['viewPath'];

        // 取得組件內容
        if (file_exists($this->viewPath . 'components/' . $code . '/component.js')) {
            $content = file_get_contents($this->viewPath . 'components/' . $code . '/component.js');
            $content = $this->refinePathInHtmlContent($content);
            return str_replace('{code}', $code, $content);
        } else {
            return '找不到' . $code . '元件腳本(' . $this->viewPath . 'components/' . $code . '/component.js)';
        }
    }
}
