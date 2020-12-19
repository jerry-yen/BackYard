<?php

/*************************************
 *             資料集
 *************************************/

/**
 * 登入設定
 */
$config['master']['dataset']['login'] = array(
    'name'              => '登入設定',
    'code'              => 'login',
    'fields'        => array(
        array('name' => '系統名稱', 'dbVariable' => 'title', 'frontendVariable' => 'title', 'component' => 'text', 'validator' => array('required', 'system.length{5,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '帳號', 'dbVariable' => 'account', 'frontendVariable' => 'account', 'component' => 'text', 'validator' => array('required', 'length{5,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => '後台登入帳號'),
        array('name' => '密碼', 'dbVariable' => 'password', 'frontendVariable' => 'password', 'component' => 'text', 'validator' => array('required', 'length{5,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => '後台登入密碼'),
        array('name' => '驗證碼', 'dbVariable' => 'verificationCode', 'frontendVariable' => 'verificationCode', 'component' => 'switch', 'validator' => array('enum{Y,N}'), 'converter' => array('selectOne{Y:是,N:否}'), 'source' => '["是","否"]', 'fieldTip' => '後台登入是否使用驗證碼'),
    )
);

/**
 * 信箱設定
 */
$config['master']['dataset']['email'] = array(
    'name'              => '信箱設定',
    'code'              => 'email',
    'fields'        => array(
        array('name' => '代碼', 'dbVariable' => '_code', 'frontendVariable' => '_code', 'component' => 'text', 'validator' => array('required', 'length{3,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '用述', 'dbVariable' => 'title', 'frontendVariable' => 'title', 'component' => 'text', 'validator' => array('required', 'length{5,30}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '信箱設定', 'dbVariable' => 'emailSetting', 'frontendVariable' => 'emailSetting', 'component' => 'grouplabel', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '發送信箱', 'dbVariable' => 'email', 'frontendVariable' => 'email', 'component' => 'text', 'validator' => array('required', 'email'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '發送名稱', 'dbVariable' => 'emailName', 'frontendVariable' => 'emailName', 'component' => 'text', 'validator' => array('length{5,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '回覆信箱', 'dbVariable' => 'replyEmail', 'frontendVariable' => 'replyEmail', 'component' => 'text', 'validator' => array('required', 'email'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => 'SMTP開關', 'dbVariable' => 'smtpGate', 'frontendVariable' => 'smtpGate', 'component' => 'switch', 'validator' => array('required', 'enum{Y,N}'), 'converter' => array(), 'source' => '["是","否"]', 'fieldTip' => ''),
        array('name' => 'SMTP設定', 'dbVariable' => 'smtpSetting', 'frontendVariable' => 'smtpSetting', 'component' => 'grouplabel', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '主機', 'dbVariable' => 'smtpHost', 'frontendVariable' => 'smtpHost', 'component' => 'text', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => 'Port', 'dbVariable' => 'smtpPort', 'frontendVariable' => 'smtpPort', 'component' => 'number', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '帳號', 'dbVariable' => 'smtpAccount', 'frontendVariable' => 'smtpAccount', 'component' => 'text', 'validator' => array('length{5,50}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '是否需要驗證', 'dbVariable' => 'isVerification', 'frontendVariable' => 'isVerification', 'component' => 'switch', 'validator' => array('enum{Y,N}'), 'converter' => array(), 'source' => '["是","否"]', 'fieldTip' => ''),
        array('name' => '安全協定', 'dbVariable' => 'smtpSecure', 'frontendVariable' => 'smtpSecure', 'component' => 'select', 'validator' => array('required', 'enum{,SSL,TLS}'), 'converter' => array(), 'source' => '{"":"無","SSL":"SSL","TLS":"TLS"}', 'fieldTip' => ''),
    )
);

/*************************************
 *               組件
 *************************************/

/**
 * 選單組件
 */
$config['master']['widget']['menu'] = array(
    'name'              => '選單',
    'code'              => 'menu',
    'widget'            => 'menu',
    'dataset'          => '',
    'permission'        => array(
        'ADD', 'MODIFY'
    ),
    'events' => array(
        'submit'        => '',
        'cancel'        => '',
        'dataSource'    => '/api/menu'
    ),
    'menu'   => array(
        // 一層
        array('type' => 'pageClass', 'icon' => '', 'title' => '系統管理', 'subItems' => array(
            array('type' => 'page', 'icon' => '', 'title' => '登入設定', 'code' => 'login'),
            array('type' => 'page', 'icon' => '', 'title' => '信箱管理', 'code' => 'email'),
            array('type' => 'page', 'icon' => '', 'title' => '帳戶管理', 'code' => 'account'),
        )),

        array('type' => 'pageClass', 'icon' => '', 'title' => '資訊管理', 'subItems' => array(
            array('type' => 'page', 'icon' => '', 'title' => '資料管理', 'code' => 'dataset'),
            array('type' => 'page', 'icon' => '', 'title' => '組件管理', 'code' => 'widget'),
        )),
        
        array('type' => 'pageClass', 'icon' => '', 'title' => '版面管理', 'subItems' => array(
            array('type' => 'page', 'icon' => '', 'title' => 'LOGO管理', 'code' => 'logo'),
            array('type' => 'page', 'icon' => '', 'title' => '頁頭管理', 'code' => 'header'),
            array('type' => 'page', 'icon' => '', 'title' => '頁面管理', 'code' => 'page'),
            array('type' => 'page', 'icon' => '', 'title' => '側欄管理', 'code' => 'leftside'),
            array('type' => 'page', 'icon' => '', 'title' => '頁尾管理', 'code' => 'footer'),
        )),

/*
        // 二層
        array('type' => 'pageClass', 'icon' => '', 'title' => '系統管理', 'subItems' => array(
            array('type' => 'page', 'icon' => '', 'title' => '登入設定', 'code' => 'login'),
            array('type' => 'page', 'icon' => '', 'title' => '信箱管理', 'code' => 'email'),
        )),

        // 三層
        array('type' => 'pageClass', 'icon' => '', 'title' => '系統管理',  'subItems' => array(
            array('type' => 'pageClass', 'icon' => '', 'title' => '網站管理',  'subItems' => array(
                array('type' => 'page', 'icon' => '', 'title' => '登入設定', 'code' => 'login'),
                array('type' => 'page', 'icon' => '', 'title' => '信箱管理', 'code' => 'email'),
            )),
            array('type' => 'pageClass', 'icon' => '', 'title' => '不要管理', 'subItems' => array(
                array('type' => 'page', 'icon' => '', 'title' => '登入設定', 'code' => 'login'),
                array('type' => 'page', 'icon' => '', 'title' => '信箱管理', 'code' => 'email'),
            )),
        )),
*/
    )
);


/**
 * 登入組件
 */
$config['master']['widget']['login'] = array(
    'name'              => '登入設定',
    'code'              => 'login',
    'widget'            => 'form',
    'dataset'          => 'login',
    'permission'        => array(
        'ADD', 'MODIFY'
    ),
    'events' => array(
        'submit'        => '',
        'cancel'        => '',
        'dataSource'    => ''
    )
);

/**
 * 信箱設定組件
 */
$config['master']['widget']['email'] = array(
    'name'              => '信箱管理',
    'code'              => 'email',
    'widget'            => 'data',
    'dataset'           => 'email',
    'classLevelCount'   => 0,
    'listfields'            => array(
        array('name' => '代碼', 'dbVariable' => '_code', 'frontendVariable' => '_code', 'component' => 'text', 'validator' => array('require', 'length{5,30}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '用述', 'dbVariable' => 'title', 'frontendVariable' => 'title', 'component' => 'text', 'validator' => array('require', 'length{5,30}'), 'converter' => array(), 'source' => '', 'fieldTip' => '')
    ),
    'permission'        => array(
        'ADD', 'MODIFY', 'DELETE'
    ),
    'events' => array(
        'add'           => '',
        'modify'        => '',
        'delete'        => '',
        'batchDelete'   => '',
        'dataSource'    => '',
    )
);

/**
 * 頁尾組件
 */
$config['master']['widget']['logo'] = array(
    'name'              => '頁尾',
    'code'              => 'logo',
    'widget'            => 'logo',
    'dataset'          => '',
    'classLevelCount'   => 0,
    'permission'        => array(),
    'events' => array()
);

/**
 * 頁尾組件
 */
$config['master']['widget']['footer'] = array(
    'name'              => '頁尾',
    'code'              => 'footer',
    'widget'            => 'footer',
    'dataset'          => '',
    'classLevelCount'   => 0,
    'permission'        => array(),
    'events' => array()
);


/*************************************
 *               版面
 *************************************/

// 左側版面
$config['master']['template']['logo'] = array(
    'name'      => 'LOGO',
    'code'      => 'logo',
    'widgets'   => array(
        array('code' => 'logo', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

// 左側版面
$config['master']['template']['leftside'] = array(
    'name'      => '選單',
    'code'      => 'menu',
    'widgets'   => array(
        array('code' => 'menu', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

// 頁頭版面
$config['master']['template']['header'] = array(
    'name'      => '選單',
    'code'      => 'menu',
    'widgets'   => array(
        array('code' => 'notify', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

// 頁尾版面
$config['master']['template']['footer'] = array(
    'name'      => '頁尾',
    'code'      => 'footer',
    'widgets'   => array(
        array('code' => 'footer', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);


/*************************************
 *               頁面
 *************************************/
/**
 * 登入頁面
 */
$config['master']['page']['login'] = array(
    'name'      => '登入設定',
    'code'      => 'login',
    'widgets'   => array(
        array('code' => 'login', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

/**
 * 信箱管理
 */
$config['master']['page']['email'] = array(
    'name'      => '信箱管理',
    'code'      => 'email',
    'widgets'   => array(
        array('code' => 'email', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);
