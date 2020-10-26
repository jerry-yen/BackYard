<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . '/third_party/backyard/core/Backyard.php');
require_once(APPPATH . '/third_party/backyard/core/Security.php');
require_once(APPPATH . '/third_party/backyard/core/Database.php');
require_once(APPPATH . '/third_party/backyard/core/Config.php');

class Install extends CI_Controller
{

	public function index()
	{
		$database = new backyard\core\Database();
		$database->install();
	}
}
