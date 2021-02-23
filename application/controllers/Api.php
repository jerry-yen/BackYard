<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . '/third_party/backyard/Backyard.php');
require_once(APPPATH . '/third_party/backyard/Package.php');
require_once(APPPATH . '/third_party/codeigniter-restserver-master/src/RestController.php');
require_once(APPPATH . '/third_party/codeigniter-restserver-master/src/Format.php');

class Api extends \chriskacerguis\RestServer\RestController
{

    private $backyard = null;
    private $inputs = array();

    /**
     * 建構子
     */
    public function __construct()
    {
        parent::__construct();
        $this->backyard = new \backyard\Backyard();
        $this->backyard->setUser($this->get('user'));
        $this->inputs = $this->backyard->getInputs();
    }

    public function login_post()
    {
        $this->backyard->loadPackage('account');
        $response = $this->backyard->user->login($this->inputs);
        $this->response($response, 200);
    }

    /**
     * 取得資料集後設資料
     * 
     * @param string code 代碼
     * @param string user 使用者類型(master, admin)
     * 
     * @return json 後設資料
     */
    public function dataset_get()
    {
        $dataset = $this->backyard->dataset->getItem($this->get('code'));
        $this->response($dataset, 200);
    }

    /**
     * 取得組件後設資料
     * 
     * @param string code 代碼
     * @param string user 使用者類型(master, admin)
     * 
     * @return json 組件資料
     */
    public function widget_get()
    {
        $this->backyard->loadPackage('frontend');
        $metadata = $this->backyard->widget->getMetadata($this->get('code'));
        $this->response($metadata, 200);
    }

    /**
     * 取得組件自訂義的後設資料
     * 
     * @param string code 代碼
     * @param string user 使用者類型(master, admin)
     * 
     * @return json 組件資料
     */
    public function definewidget_get()
    {
        $this->backyard->loadPackage('frontend');
        $metadata = $this->backyard->widget->getDefineMetadata($this->get('code'));
        $this->response($metadata, 200);
    }

    /**
     * 取得組件清單
     * @param string code 代碼
     * @param string user 使用者類型(master, admin)
     */
    public function widgetlist_get()
    {
        $this->backyard->loadPackage('frontend');
        $metadata = $this->backyard->widget->getList($this->get('code'));
        $this->response($metadata, 200);
    }

    /**
     * 取得組件HTML資料
     * 
     * @param string code 代碼
     * @param string user 使用者類型(master, admin)
     * 
     * @return json 組件資料
     */
    public function widgethtml_get()
    {
        $this->backyard->loadPackage('frontend');
        $htmlContent = $this->backyard->widget->render($this->get('code'));
        $this->response(array('content' => $htmlContent), 200);
    }

    /**
     * 取得頁面HTML資料
     * 
     * @param string code 代碼
     * @param string user 使用者類型(master, admin)
     * 
     * @return json 組件資料
     */
    public function pagehtml_get()
    {
        $this->backyard->loadPackage('frontend');
        $htmlContent = $this->backyard->page->render($this->get('code'));
        $this->response(json_encode(array('content' => $htmlContent), JSON_UNESCAPED_UNICODE), 200);
    }

    /**
     * 取得版面後設資料
     * 
     * @param string user 使用者類型(master, admin)
     * 
     * @return json 組件資料
     */
    public function template_get()
    {
        $this->backyard->loadPackage('frontend');
        $metadata = $this->backyard->template->getMetadata($this->get('code'));
        $this->response($metadata['metadata'], 200);
    }

    /**
     * 取得內容後設資料
     * 
     * @param string code 代碼
     * @param string user 使用者類型(master, admin)
     * 
     * @return json 組件資料
     */
    public function content_get()
    {
        $this->backyard->loadPackage('frontend');
        $metadata = $this->backyard->page->getMetadata($this->get('code'));
        $this->response($metadata['page'], 200);
    }


    /**
     * 取得內容後設資料
     * 
     * @param string code 代碼
     * @param string user 使用者類型(master, admin)
     * 
     * @return json 組件資料
     */
    public function pages_get()
    {
        $this->backyard->loadPackage('frontend');
        $metadata = $this->backyard->page->getMetadatas();
        $this->response($metadata['page'], 200);
    }

    /**
     * 取得整個頁面所需要的Javascript套件
     * @param string $code 元件名稱
     */
    public function script_get()
    {
        $this->backyard->loadPackage('frontend');
        $script = $this->backyard->page->getScript($this->get('code'));
        header('Content-Type: application/javascript');
        echo $script;
    }

    /**
     * 取得整個頁面所需要的Javascript套件
     * @param string $code 元件名稱
     */
    public function css_get()
    {
        $this->backyard->loadPackage('frontend');
        $script = $this->backyard->page->getCSS($this->get('code'));
        header("Content-type: text/css");
        echo $script;
    }

    /**
     * 取得元件
     * @param string $code 元件名稱
     */
    public function component_get()
    {
        $this->backyard->loadPackage('frontend');
        $metadata = $this->backyard->component->getScript($this->get('code'));
        header('Content-Type: application/javascript');
        echo $metadata;
    }

    /**
     * 取得項目
     */
    public function item_get()
    {
        $response = $this->backyard->data->getItem($this->inputs);
        $this->response($response, 200);
    }

    /**
     * 新增項目
     */
    public function item_post()
    {
        if ($this->backyard->getUserType() == 'master' && $this->get('code') == 'dataset') {
            $this->backyard->data->createTable($this->inputs);
        }
        $response = $this->backyard->data->insertItem($this->inputs);
        $this->response($response, 200);
    }

    /**
     * 更新項目
     */
    public function item_put()
    {
        if ($this->backyard->getUserType() == 'master' && $this->get('code') == 'dataset') {
            $this->backyard->data->createTable($this->inputs);
        }
        $response = $this->backyard->data->updateItem($this->inputs);
        $this->response($response, 200);
    }

    /**
     * 刪除項目
     */
    public function item_delete()
    {
        $response = $this->backyard->data->deleteItem($this->inputs);
        $this->response($response, 200);
    }

    /**
     * 取得項目清單
     */
    public function items_get()
    {
        $response = $this->backyard->data->getItems($this->inputs);
        $this->response($response, 200);
    }

    /**
     * 更新項目清單
     */
    public function items_put()
    {
        $response = $this->backyard->data->updateItems($this->inputs);
        $this->response($response, 200);
    }

    public function files_get()
    {
        $response = $this->backyard->file->getItems($this->inputs);
        $this->response($response, 200);
    }

    public function file_post()
    {
        $response = $this->backyard->file->upload($this->inputs);
        $this->response($response, 200);
    }
}
