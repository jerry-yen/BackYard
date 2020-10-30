<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . '/third_party/backyard/core/Backyard.php');
require_once(APPPATH . '/third_party/backyard/core/Metadata.php');
require_once(APPPATH . '/third_party/backyard/core/Validator.php');
require_once(APPPATH . '/third_party/backyard/core/Converter.php');
require_once(APPPATH . '/third_party/backyard/core/Security.php');
require_once(APPPATH . '/third_party/backyard/core/Config.php');
require_once(APPPATH . '/third_party/backyard/core/Database.php');
require_once(APPPATH . '/third_party/backyard/libraries/Code.php');
require_once(APPPATH . '/third_party/backyard/libraries/Path.php');

require_once(APPPATH . '/third_party/backyard/packages/frontend/Page.php');
require_once(APPPATH . '/third_party/codeigniter-restserver-master/src/RestController.php');
require_once(APPPATH . '/third_party/codeigniter-restserver-master/src/Format.php');

class Api extends \chriskacerguis\RestServer\RestController
{

    public function index()
    {
    }
}
