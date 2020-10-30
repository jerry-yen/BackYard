<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . '/third_party/backyard/Backyard.php');
require_once(APPPATH . '/third_party/backyard/core/Metadata.php');
require_once(APPPATH . '/third_party/backyard/core/Validator.php');
require_once(APPPATH . '/third_party/backyard/core/Converter.php');
require_once(APPPATH . '/third_party/backyard/core/Security.php');
require_once(APPPATH . '/third_party/backyard/core/Config.php');
require_once(APPPATH . '/third_party/backyard/core/Data.php');
require_once(APPPATH . '/third_party/backyard/libraries/Code.php');
require_once(APPPATH . '/third_party/backyard/libraries/Path.php');

require_once(APPPATH . '/third_party/backyard/packages/frontend/Page.php');

class Master extends CI_Controller
{

	public function index()
	{
	}

	public function page()
	{
		$backyard = new backyard\Backyard();
		//$backyard->render();
		//$backyard->deleteItem();
		// $res = $backyard->insertItem();
		//$res = $backyard->getItems();
		//print_r($res);
	}
}
