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
        return $widget;
    }

    /**
     * 取得組件清單
     */
    public function getList()
    {
        // 取得View基本路徑
        $this->backyard->config->loadConfigFile('frontend');
        $this->viewPath = $this->backyard->config->getConfig('frontend')['viewPath'];
        $widgets = array();
        if (file_exists($this->viewPath . 'widgets/')) {
            $dirs = scandir($this->viewPath . 'widgets/');
            foreach($dirs as $dirOrFile){
                if(in_array($dirOrFile, array('.','..'))){
                    continue;
                }
                $widgets[] = $dirOrFile;
            }
        }

        return $widgets;
    }

    /**
     * 取得組件後設資料
     * 
     * @param string $code 代碼
     */
    public function getDefineMetadata($code)
    {
        // 取得View基本路徑
        $this->backyard->config->loadConfigFile('frontend');
        $this->viewPath = $this->backyard->config->getConfig('frontend')['viewPath'];
        // 取得組件內容
        if (file_exists($this->viewPath . 'widgets/' . $code . '/metadata.php')) {
            include_once($this->viewPath . 'widgets/' . $code . '/metadata.php');
            return array('status' => 'success', 'metadata' => $metadata);
        }


        return array('status' => 'failed');
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
            $content = $this->refinePathInHtmlContent($content);
            return str_replace('{code}', $code, $content);
        } else {
            return '找不到' . $widget . '組件介面(' . $this->viewPath . 'widgets/' . $widget . '/template.php)';
        }
    }
}
