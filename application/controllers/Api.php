<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . '/third_party/backyard/Backyard.php');
require_once(APPPATH . '/third_party/backyard/Package.php');
require_once(APPPATH . '/third_party/codeigniter-restserver-master/src/RestController.php');
require_once(APPPATH . '/third_party/codeigniter-restserver-master/src/Format.php');

class Api extends \chriskacerguis\RestServer\RestController
{

    public function index()
    {
    }

    /**
     * 取得後設資料
     * 
     * @param string code 代碼
     * @param string user 使用者類型(master, admin)
     * 
     * @return json 後設資料
     */
    public function metadata_get()
    {
        $backyard = new \backyard\Backyard();
        $backyard->setUser($this->get('user'));
        $metadata = $backyard->metadata->getItem($this->get('code'));
        $this->response(json_encode($metadata, JSON_UNESCAPED_UNICODE), 200);
    }

    /**
     * 取得所有後設資料
     * 
     * @param string user 使用者類型(master, admin)
     * 
     * @return json 後設資料
     */
    public function metadatas_get()
    {
        $backyard = new \backyard\Backyard();
        $backyard->setUser($this->get('user'));
        $metadata = $backyard->metadata->getItems();
        $this->response(json_encode($metadata, JSON_UNESCAPED_UNICODE), 200);
    }

    /**
     * 取得組件資料
     * 
     * @param string code 代碼
     * @param string user 使用者類型(master, admin)
     * 
     * @return json 組件資料
     */
    public function widget_get()
    {
        $backyard = new \backyard\Backyard();
        $backyard->setUser($this->get('user'));

        $backyard->loadPackage('frontend');
        $htmlContent = $backyard->widget->render($this->get('code'));
        $this->response(json_encode(array('content' => $htmlContent), JSON_UNESCAPED_UNICODE), 200);
    }

    public function widgets_get()
    {
    }

    /**
     * 取得頁面資料
     * 
     * @param string code 代碼
     * @param string user 使用者類型(master, admin)
     * 
     * @return json 組件資料
     */
    public function page_get()
    {
        $backyard = new \backyard\Backyard();
        $backyard->setUser($this->get('user'));

        $backyard->loadPackage('frontend');
        $htmlContent = $backyard->page->render($this->get('code'));
        $this->response(json_encode(array('content' => $htmlContent), JSON_UNESCAPED_UNICODE), 200);
    }

    public function pages_get()
    {
    }

    public function menu_get()
    {
    }

    public function item_get()
    {
    }

    public function items_get()
    {
    }
}
