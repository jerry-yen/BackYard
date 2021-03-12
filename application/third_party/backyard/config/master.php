<?php

/*************************************
 *             資料集
 *************************************/

/**
 * 登入設定
 */
$config['master']['metadata']['login'] = array(
    'name'              => '登入設定',
    'code'              => 'login',
    'fields'        => array(
        array('name' => '系統名稱', 'dbVariable' => 'title', 'frontendVariable' => 'title', 'component' => 'Text', 'validator' => array('required', 'system.length{5,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '帳號', 'dbVariable' => 'account', 'frontendVariable' => 'account', 'component' => 'Text', 'validator' => array('required', 'length{5,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => '後台登入帳號'),
        array('name' => '密碼', 'dbVariable' => 'password', 'frontendVariable' => 'password', 'component' => 'Text', 'validator' => array('required', 'length{5,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => '後台登入密碼'),
        array('name' => '驗證碼', 'dbVariable' => 'verificationCode', 'frontendVariable' => 'verificationCode', 'component' => 'Switch', 'validator' => array('enum{Y,N}'), 'converter' => array('selectOne{Y:是,N:否}'), 'source' => '', 'fieldTip' => '後台登入是否使用驗證碼'),
    )
);

/**
 * 信箱設定
 */
$config['master']['metadata']['email'] = array(
    'name'              => '信箱設定',
    'code'              => 'email',
    'fields'        => array(
        array('name' => '用述', 'dbVariable' => 'title', 'frontendVariable' => 'title', 'component' => 'Text', 'validator' => array('require', 'length{5,30}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),

        array('name' => '信箱設定', 'dbVariable' => 'emailSetting', 'frontendVariable' => 'emailSetting', 'component' => 'Grouplabel', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '發送信箱', 'dbVariable' => 'email', 'frontendVariable' => 'email', 'component' => 'Text', 'validator' => array('require', 'email'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '發送名稱', 'dbVariable' => 'emailName', 'frontendVariable' => 'emailName', 'component' => 'Text', 'validator' => array('length{5,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '回覆信箱', 'dbVariable' => 'replyEmail', 'frontendVariable' => 'replyEmail', 'component' => 'Text', 'validator' => array('require', 'email'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => 'SMTP開關', 'dbVariable' => 'smtpGate', 'frontendVariable' => 'smtpGate', 'component' => 'Switch', 'validator' => array('require', 'enum{Y,N}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => 'SMTP設定', 'dbVariable' => 'smtpSetting', 'frontendVariable' => 'smtpSetting', 'component' => 'Grouplabel', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '主機', 'dbVariable' => 'smtpHost', 'frontendVariable' => 'smtpHost', 'component' => 'Text', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => 'Port', 'dbVariable' => 'smtpPort', 'frontendVariable' => 'smtpPort', 'component' => 'Number', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '帳號', 'dbVariable' => 'smtpAccount', 'frontendVariable' => 'smtpAccount', 'component' => 'Text', 'validator' => array('length{5,50}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '是否需要驗證', 'dbVariable' => 'isVerification', 'frontendVariable' => 'isVerification', 'component' => 'Switch', 'validator' => array('enum{Y,N}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '安全協定', 'dbVariable' => 'smtpSecure', 'frontendVariable' => 'smtpSecure', 'component' => 'Select', 'validator' => array('require', 'enum{,SSL,TLS}'), 'converter' => array(), 'source' => '[{"":"無"},{"SSL":"SSL"},{"TLS","TLS"}]', 'fieldTip' => ''),
    )
);

/*************************************
 *               組件
 *************************************/


/**
 * 登入組件
 */
$config['master']['widget']['login'] = array(
    'name'              => '登入設定',
    'code'              => 'login',
    'widget'            => 'form',
    'metadata'          => 'login',
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
    'name'              => '登入設定',
    'code'              => 'login',
    'widget'            => 'table',
    'metadata'          => 'email',
    'classLevelCount'   => 0,
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
        array('code' => 'email', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

/*************************************
 *               選單
 *************************************/

$config['master']['menu'] = array(
    'name'      => '選單設定',
    'code'      => 'login',
    'menu'   => array(
        // 一層
        array('type' => 'page', 'icon' => '', 'code' => 'login'),

        // 二層
        array('type' => 'pageClass', 'icon' => '', 'subItems' => array(
            array('type' => 'page', 'icon' => '', 'code' => 'login'),
            array('type' => 'page', 'icon' => '', 'code' => 'email'),
        )),

        // 三層
        array('type' => 'pageClass', 'icon' => '', 'subItems' => array(
            array('type' => 'pageClass', 'icon' => '', 'pages' => array(
                array('type' => 'page', 'icon' => '', 'code' => 'login'),
                array('type' => 'page', 'icon' => '', 'code' => 'email'),
            )),
            array('type' => 'pageClass', 'icon' => '', 'pages' => array(
                array('type' => 'page', 'icon' => '', 'code' => 'login'),
                array('type' => 'page', 'icon' => '', 'code' => 'email'),
            )),
        )),
    )
);
