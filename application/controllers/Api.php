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

    public function metadata_get()
    {
        $backyard = new \backyard\Backyard();
        $backyard->setUser($this->get('user'));
        $metadata = $backyard->metadata->getItem($this->get('code'));
        $this->response(json_encode($metadata, JSON_UNESCAPED_UNICODE), 200);
    }
}
