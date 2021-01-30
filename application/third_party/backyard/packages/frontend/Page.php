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
        $content = str_replace(
            '{userType}',
            $this->backyard->getUserType(),
            $content
        );
        return $content;
    }

    /**
     * 取得頁面後設資料
     * 
     * @param string $code 代碼
     */
    public function getMetadata($code)
    {
        $page = (is_null($code)) ? array('metadata' => array()) : $this->backyard->getUser()->getMetadataOfPage($code);
        $information = $this->backyard->getUser()->getSystemInformation();
        return array('status' => 'success', 'page' => $page['metadata'], 'information' => $information['metadata']);
    }

    /**
     * 取得整個頁面所需要的Javascript
     * 
     * @param string $code 頁面代碼
     * 
     * @return string
     */
    public function getScript($code)
    {

        $widgetScripts = array();
        $componentScripts = array();

        // 取得 View 路徑
        $this->backyard->config->loadConfigFile('frontend');
        $this->viewPath = $this->backyard->config->getConfig('frontend')['viewPath'];


        /* 版面各區塊所使用的組件 */

        // Logo 區塊
        $template = $this->backyard->getUser()->getMetadataOfTemplate('logo');
        if ($template['status'] == 'success') {
            $this->readScripts($template['metadata']['widgets'], $widgetScripts, $componentScripts);
        }
        // 左側區塊
        $template = $this->backyard->getUser()->getMetadataOfTemplate('leftside');
        $this->readScripts($template['metadata']['widgets'], $widgetScripts, $componentScripts);

        // 頁頭區塊
        $template = $this->backyard->getUser()->getMetadataOfTemplate('header');
        $this->readScripts($template['metadata']['widgets'], $widgetScripts, $componentScripts);

        // 頁底區塊
        $template = $this->backyard->getUser()->getMetadataOfTemplate('footer');
        $this->readScripts($template['metadata']['widgets'], $widgetScripts, $componentScripts);

        // 取得頁面後設資料
        $template = $this->getMetadata($code);
        $this->readScripts($template['page']['widgets'], $widgetScripts, $componentScripts);


        return implode("\r\n", $widgetScripts) . "\r\n" . implode("\r\n", $componentScripts);
    }

    private function readScripts($widgets, &$widgetScripts, &$componentScripts)
    {
        foreach ($widgets as $widget) {

            // 取得組件後設資料
            $widgetMetadata = $this->backyard->widget->getMetadata($widget['code']);
            if ($widgetMetadata['status'] != 'success') {
                continue;
            }

            $widgetName = $widgetMetadata['metadata']['widget']['code'];
            if (isset($widgetScripts[$widgetName])) {
                continue;
            }

            // 取得組件Script內容
            $scriptPath = $this->viewPath . '/widgets/' . $widgetName . '/script.js';
            if (!file_exists($scriptPath)) {
                continue;
            }

            $widgetScript = file_get_contents($scriptPath) . "\r\n";
            $widgetScript .= $this->readLibraries($this->viewPath . '/widgets/' . $widgetName . '/libraries.json');
            $widgetScripts[$widgetName] = $widgetScript;

            // 取得資料集後設資料
            $datasetCode = $widgetMetadata['metadata']['dataset'];
            $fieldDataset = $this->backyard->dataset->getItem($datasetCode);

            if (isset($fieldDataset['dataset'])) {
                foreach ($fieldDataset['dataset']['fields'] as $field) {
                    // 取得元件Script內容
                    $scriptPath = $this->viewPath . '/components/' . $field['component'] . '/component.js';
                    if (!file_exists($scriptPath)) {
                        continue;
                    }

                    $componentScript = file_get_contents($scriptPath) . "\r\n";
                    $componentScript .= $this->readLibraries($this->viewPath . '/components/' . $field['component'] . '/libraries.json');
                    $componentScripts[$field['component']] = $componentScript;
                }
            }
        }
    }

    /**
     * 載入引用的函式/套件
     * 
     * @param string $libraryJSONFile 函式庫路徑
     */
    private function readLibraries($libraryJSONFile)
    {
        $script = '';
        if (file_exists($libraryJSONFile)) {
            $libraries = json_decode(file_get_contents($libraryJSONFile), true);

            foreach ($libraries as $libraryName) {
                $libraryPath = $this->viewPath . '/libraries/' . $libraryName . '/' . $libraryName . '.js';
                if (file_exists($libraryPath)) {
                    $script .= file_get_contents($libraryPath) . "\r\n";
                }
            }
        }
        return $script;
    }

    /**
     * 取得整個頁面所需要的CSS
     * 
     * @param string $code 頁面代碼
     * 
     * @return string
     */
    public function getCSS($code)
    {

        $widgetStyles = array();
        $componentStyles = array();

        // 取得 View 路徑
        $this->backyard->config->loadConfigFile('frontend');
        $this->viewPath = $this->backyard->config->getConfig('frontend')['viewPath'];

        /* 版面各區塊所使用的組件 */

        // Logo 區塊
        /*
        $template = $this->backyard->getUser()->getMetadataOfTemplate('logo');
        $this->readCsses($template['metadata']['widgets'], $widgetScripts, $componentScripts);
*/
        // 左側區塊
        $template = $this->backyard->getUser()->getMetadataOfTemplate('leftside');
        $this->readCsses($template['metadata']['widgets'], $widgetScripts, $componentScripts);

        // 頁頭區塊
        $template = $this->backyard->getUser()->getMetadataOfTemplate('header');
        $this->readCsses($template['metadata']['widgets'], $widgetScripts, $componentScripts);

        // 頁底區塊
        $template = $this->backyard->getUser()->getMetadataOfTemplate('footer');
        $this->readCsses($template['metadata']['widgets'], $widgetScripts, $componentScripts);

        // 取得頁面後設資料
        $template = $this->getMetadata($code);
        $this->readCsses($template['page']['widgets'], $widgetStyles, $componentStyles);

        /*
        foreach ($pageMetadata['metadata']['widgets'] as $widget) {

            // 取得組件後設資料
            $widgetMetadata = $this->backyard->widget->getMetadata($widget['code']);
            $widgetName = $widgetMetadata['metadata']['widget'];
            if (isset($widgetStyles[$widgetName])) {
                continue;
            }

            // 取得元件Style內容
            $widgetStyle = '';
            $stylePath = $this->viewPath . '/widgets/' . $widgetName . '/style.css';
            if (file_exists($stylePath)) {
                $widgetStyle = file_get_contents($stylePath) . "\r\n";
            }
            $widgetStyle .= $this->readCSSLibraries($this->viewPath . '/widgets/' . $widgetName . '/libraries.json');
            $widgetStyles[$widgetName] = $widgetStyle;

            // 取得資料集後設資料
            $datasetCode = $widgetMetadata['metadata']['dataset'];
            $fieldDataset = $this->backyard->dataset->getItem($datasetCode);
            foreach ($fieldDataset['dataset']['fields'] as $field) {
                // 取得元件Style內容
                $componentStyle = '';
                $stylePath = $this->viewPath . '/components/' . $field['component'] . '/component.css';
                if (file_exists($stylePath)) {
                    $componentStyle = file_get_contents($stylePath) . "\r\n";
                }
                $componentStyle .= $this->readCSSLibraries($this->viewPath . '/components/' . $field['component'] . '/libraries.json');
                $componentStyles[$field['component']] = $componentStyle;
            }
        }
        */

        return implode("\r\n", $widgetStyles) . "\r\n" . implode("\r\n", $componentStyles);
    }

    private function readCsses($widgets, &$widgetStyles, &$componentStyles)
    {
        foreach ($widgets as $widget) {
            // 取得組件後設資料
            $widgetMetadata = $this->backyard->widget->getMetadata($widget['code']);
            if ($widgetMetadata['status'] != 'success') {
                continue;
            }
            $widgetName = $widgetMetadata['metadata']['widget']['code'];
            if (isset($widgetStyles[$widgetName])) {
                continue;
            }

            // 取得元件Style內容
            $widgetStyle = '';
            $stylePath = $this->viewPath . '/widgets/' . $widgetName . '/style.css';
            if (file_exists($stylePath)) {
                $widgetStyle = file_get_contents($stylePath) . "\r\n";
            }
            $widgetStyle .= $this->readCSSLibraries($this->viewPath . '/widgets/' . $widgetName . '/libraries.json');
            $widgetStyles[$widgetName] = $widgetStyle;

            // 取得資料集後設資料
            $datasetCode = $widgetMetadata['metadata']['dataset'];
            $fieldDataset = $this->backyard->dataset->getItem($datasetCode);
            if (isset($fieldDataset['dataset']['fields'])) {
                foreach ($fieldDataset['dataset']['fields'] as $field) {
                    // 取得元件Style內容
                    $componentStyle = '';
                    $stylePath = $this->viewPath . '/components/' . $field['component'] . '/component.css';
                    if (file_exists($stylePath)) {
                        $componentStyle = file_get_contents($stylePath) . "\r\n";
                    }
                    $componentStyle .= $this->readCSSLibraries($this->viewPath . '/components/' . $field['component'] . '/libraries.json');
                    $componentStyles[$field['component']] = $componentStyle;
                }
            }
        }
    }

    /**
     * 載入引用的函式/套件
     * 
     * @param string $libraryJSONFile 函式庫路徑
     */
    private function readCSSLibraries($libraryJSONFile)
    {
        $style = '';
        if (file_exists($libraryJSONFile)) {
            $libraries = json_decode(file_get_contents($libraryJSONFile), true);

            foreach ($libraries as $libraryName) {
                $libraryPath = $this->viewPath . '/libraries/' . $libraryName . '/' . $libraryName . '.css';
                if (file_exists($libraryPath)) {
                    $style .= file_get_contents($libraryPath) . "\r\n";
                }
            }
        }
        return $style;
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
        if ($page['status'] != 'success') {
            return $page['message'];
        }

        $content = file_get_contents($this->viewPath . '/full.html');
        $content = $this->refinePathInHtmlContent($content);
        $content = str_replace('{systemTitle}', $page['information']['title'], $content);
        $content = str_replace('{pageTitle}', $page['page']['name'], $content);
        $content = str_replace('{code}', $page['page']['code'], $content);

        return $content;
    }

    public function renderLoginPage()
    {
        // 取得View基本路徑
        $this->backyard->config->loadConfigFile('frontend');
        $this->viewPath = $this->backyard->config->getConfig('frontend')['viewPath'];

        // 取得頁面後設資料
        $page = $this->getMetadata(null);
        if ($page['status'] != 'success') {
            return $page['message'];
        }

        $content = file_get_contents($this->viewPath . '/login.html');
        $content = $this->refinePathInHtmlContent($content);
        $content = str_replace('{systemTitle}', $page['information']['title'], $content);

        return $content;
    }
}
