<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . '/third_party/backyard/Backyard.php');
require_once(APPPATH . '/third_party/backyard/Package.php');

class Master extends CI_Controller
{

	protected $backyard = null;
	/**
	 * 建構子
	 */
	public function __construct()
	{
		parent::__construct();
		$this->backyard = new \backyard\Backyard();
		$this->backyard->setUser('master');
	}

	public function index()
	{
	}

	/**
	 * 載入頁面
	 */
	public function page($code = null)
	{
		$this->backyard->loadPackage('frontend');
		$htmlContent = $this->backyard->page->render($code);
		echo $htmlContent;
	}
}
