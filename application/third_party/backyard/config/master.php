<?php

/*************************************
 *             開發者帳密
 *************************************/

$config['master']['login'] = array(
    'account'   => 'develop',
    'password'  => '520726428',
);

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
    'name'              => '信箱管理',
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

/**
 * 帳號管理
 */
$config['master']['dataset']['account'] = array(
    'name'              => '帳號管理',
    'code'              => 'account',
    'fields'        => array(
        array('name' => '代碼', 'dbVariable' => '_code', 'frontendVariable' => '_code', 'component' => 'text', 'validator' => array('required', 'length{3,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '用述', 'dbVariable' => 'title', 'frontendVariable' => 'title', 'component' => 'text', 'validator' => array('required', 'length{5,30}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '密碼', 'dbVariable' => 'password', 'frontendVariable' => 'password', 'component' => 'text', 'validator' => array('required', 'length{5,30}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
    )
);

/**
 * 資料管理
 */
$config['master']['dataset']['dataset'] = array(
    'name'              => '資料管理',
    'code'              => 'dataset',
    'fields'        => array(
        array('name' => '代碼', 'dbVariable' => '_code', 'frontendVariable' => '_code', 'component' => 'text', 'validator' => array('required', 'length{3,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '名稱', 'dbVariable' => 'name', 'frontendVariable' => 'name', 'component' => 'text', 'validator' => array('required', 'length{3,10}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '欄位', 'dbVariable' => 'fields', 'frontendVariable' => 'fields', 'component' => 'datasetfields', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
    )
);

/**
 * 組件管理
 */
$config['master']['dataset']['widget'] = array(
    'name'              => '組件管理',
    'code'              => 'widget',
    'fields'        => array(
        array('name' => '代碼', 'dbVariable' => '_code', 'frontendVariable' => '_code', 'component' => 'text', 'validator' => array('required', 'length{3,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '名稱', 'dbVariable' => 'name', 'frontendVariable' => 'name', 'component' => 'text', 'validator' => array('required', 'length{3,10}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '引用資料集', 'dbVariable' => 'source', 'frontendVariable' => 'source', 'component' => 'widgetfields', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '組件', 'dbVariable' => 'widget', 'frontendVariable' => 'widget', 'component' => 'widget', 'validator' => array(), 'converter' => array(), 'source' => 'api://widgetlist/user/master', 'fieldTip' => ''),
    )
);

/**
 * 頁頭組件
 */
$config['master']['dataset']['header'] = array(
    'name'              => '頁頭組件',
    'code'              => 'header',
    'fields'        => array(
        array('name' => '組件代碼', 'dbVariable' => '_code', 'frontendVariable' => '_code', 'component' => 'pagewidget', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '桌面', 'dbVariable' => 'desktop', 'frontendVariable' => 'desktop', 'component' => 'slider', 'validator' => array(), 'converter' => array(), 'source' => '{"min":1, "max":12}', 'fieldTip' => ''),
        array('name' => '平板', 'dbVariable' => 'pad', 'frontendVariable' => 'pad', 'component' => 'slider', 'validator' => array(), 'converter' => array(), 'source' => '{"min":1, "max":12}', 'fieldTip' => ''),
        array('name' => '手機', 'dbVariable' => 'mobile', 'frontendVariable' => 'mobile', 'component' => 'slider', 'validator' => array(), 'converter' => array(), 'source' => '{"min":1, "max":12}', 'fieldTip' => ''),
    )
);

/**
 * 頁面組件
 */
$config['master']['dataset']['content'] = array(
    'name'              => '頁面組件',
    'code'              => 'content',
    'fields'        => array(
        array('name' => '頁面代碼', 'dbVariable' => '_code', 'frontendVariable' => '_code', 'component' => 'text', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '頁面名稱', 'dbVariable' => 'name', 'frontendVariable' => 'name', 'component' => 'text', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '組件', 'dbVariable' => 'widgets', 'frontendVariable' => 'widgets', 'component' => 'pagewidgets', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
    )
);

/**
 * 側欄組件
 */
$config['master']['dataset']['leftside'] = array(
    'name'              => '頁面組件',
    'code'              => 'leftside',
    'fields'        => array(
        array('name' => '組件代碼', 'dbVariable' => '_code', 'frontendVariable' => '_code', 'component' => 'pagewidget', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '桌面', 'dbVariable' => 'desktop', 'frontendVariable' => 'desktop', 'component' => 'slider', 'validator' => array(), 'converter' => array(), 'source' => '{"min":1, "max":12}', 'fieldTip' => ''),
        array('name' => '平板', 'dbVariable' => 'pad', 'frontendVariable' => 'pad', 'component' => 'slider', 'validator' => array(), 'converter' => array(), 'source' => '{"min":1, "max":12}', 'fieldTip' => ''),
        array('name' => '手機', 'dbVariable' => 'mobile', 'frontendVariable' => 'mobile', 'component' => 'slider', 'validator' => array(), 'converter' => array(), 'source' => '{"min":1, "max":12}', 'fieldTip' => ''),
    )
);

/**
 * 頁尾組件
 */
$config['master']['dataset']['footer'] = array(
    'name'              => '頁尾組件',
    'code'              => 'footer',
    'fields'        => array(
        array('name' => '組件代碼', 'dbVariable' => '_code', 'frontendVariable' => '_code', 'component' => 'pagewidget', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '桌面', 'dbVariable' => 'desktop', 'frontendVariable' => 'desktop', 'component' => 'slider', 'validator' => array(), 'converter' => array(), 'source' => '{"min":1, "max":12}', 'fieldTip' => ''),
        array('name' => '平板', 'dbVariable' => 'pad', 'frontendVariable' => 'pad', 'component' => 'slider', 'validator' => array(), 'converter' => array(), 'source' => '{"min":1, "max":12}', 'fieldTip' => ''),
        array('name' => '手機', 'dbVariable' => 'mobile', 'frontendVariable' => 'mobile', 'component' => 'slider', 'validator' => array(), 'converter' => array(), 'source' => '{"min":1, "max":12}', 'fieldTip' => ''),
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
    'dataset'          => '',
    'widget'            => array(
        'code'  => 'menu',
        'event_dataSource' => '/api/menu'
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
            array('type' => 'page', 'icon' => '', 'title' => '頁頭管理', 'code' => 'header'),
            array('type' => 'page', 'icon' => '', 'title' => '頁面管理', 'code' => 'content'),
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
    'dataset'          => 'login',
    'widget'            => array(
        'code'  => 'form',
        'event_submit' => '',
        'event_cancel' => '',
        'event_dataSource' => ''
    ),
);

/**
 * 信箱管理組件
 */
$config['master']['widget']['email'] = array(
    'name'              => '信箱管理',
    'code'              => 'email',
    'dataset'           => 'email',
    'widget'            => array(
        'code'  => 'data',
        'permission' => array('ADD', 'MODIFY', 'DELETE'),
        'listfields' => array(
            '_code' => '代碼',
            'title' => '用述'
        ),
        'classLevelCount' => 0,
        'event_add' => '',
        'event_modify' => '',
        'event_delete' => '',
        'event_batchDelete' => '',
        'event_dataSource' => ''
    ),
);

/**
 * 帳號管理組件
 */
$config['master']['widget']['account'] = array(
    'name'              => '帳號管理',
    'code'              => 'account',
    'dataset'           => 'account',
    'widget'            => array(
        'code'  => 'data',
        'permission' => array('ADD', 'MODIFY', 'DELETE'),
        'listfields' => array(
            'title' => '姓名',
            'account' => '帳號'
        ),
        'classLevelCount' => 0,
        'event_add' => '',
        'event_modify' => '',
        'event_delete' => '',
        'event_batchDelete' => '',
        'event_dataSource' => ''
    ),
);

/**
 * 資料管理組件
 */
$config['master']['widget']['dataset'] = array(
    'name'              => '資料管理',
    'code'              => 'dataset',
    'dataset'           => 'dataset',
    'widget'            => array(
        'code'  => 'data',
        'permission' => array('ADD', 'MODIFY', 'DELETE'),
        'listfields' => array(
            '_code' => '代碼',
            'name' => '名稱'
        ),
        'classLevelCount' => 0,
        'event_add' => '',
        'event_modify' => '',
        'event_delete' => '',
        'event_batchDelete' => '',
        'event_dataSource' => ''
    ),
);

/**
 * 組件管理組件
 */
$config['master']['widget']['widget'] = array(
    'name'              => '組件管理',
    'code'              => 'widget',
    'dataset'           => 'widget',
    'widget'            => array(
        'code'  => 'data',
        'permission' => array('ADD', 'MODIFY', 'DELETE'),
        'listfields' => array(
            '_code' => '代碼',
            'name' => '名稱'
        ),
        'classLevelCount' => 0,
        'event_add' => '',
        'event_modify' => '',
        'event_delete' => '',
        'event_batchDelete' => '',
        'event_dataSource' => ''
    ),
);

/**
 * 頁頭組件
 */
$config['master']['widget']['header'] = array(
    'name'              => '頁頭組件',
    'code'              => 'header',
    'dataset'          => 'header',
    'widget'            => array(
        'code'  => 'data',
        'permission' => array('ADD', 'MODIFY', 'DELETE'),
        'listfields' => array(
            '_code' => '組件代碼',
            'desktop' => '桌面',
            'pad' => '平板',
            'mobile' => '手機'
        ),
        'classLevelCount' => 0,
        'event_add' => '',
        'event_modify' => '',
        'event_delete' => '',
        'event_batchDelete' => '',
        'event_dataSource' => ''
    ),
);

/**
 * 頁面組件
 */
$config['master']['widget']['content'] = array(
    'name'              => '頁面組件',
    'code'              => 'content',
    'dataset'          => 'content',
    'widget'            => array(
        'code'  => 'data',
        'permission' => array('ADD', 'MODIFY', 'DELETE'),
        'listfields' => array(
            '_code' => '頁面代碼',
            'name' => '頁面名稱'
        ),
        'event_add' => '',
        'event_modify' => '',
        'event_delete' => '',
        'event_batchDelete' => '',
        'event_dataSource' => ''
    ),
);

/**
 * 側欄組件
 */
$config['master']['widget']['leftside'] = array(
    'name'              => '側欄組件',
    'code'              => 'leftside',
    'dataset'          => 'leftside',
    'widget'            => array(
        'code'  => 'data',
        'permission' => array('ADD', 'MODIFY', 'DELETE'),
        'listfields' => array(
            '_code' => '組件代碼',
            'desktop' => '桌面',
            'pad' => '平板',
            'mobile' => '手機'
        ),
        'classLevelCount' => 0,
        'event_add' => '',
        'event_modify' => '',
        'event_delete' => '',
        'event_batchDelete' => '',
        'event_dataSource' => ''
    ),
);

/**
 * 頁尾組件
 */
$config['master']['widget']['footer'] = array(
    'name'              => '頁尾組件',
    'code'              => 'footer',
    'dataset'          => 'footer',
    'widget'            => array(
        'code'  => 'data',
        'permission' => array('ADD', 'MODIFY', 'DELETE'),
        'listfields' => array(
            '_code' => '組件代碼',
            'desktop' => '桌面',
            'pad' => '平板',
            'mobile' => '手機'
        ),
        'classLevelCount' => 0,
        'event_add' => '',
        'event_modify' => '',
        'event_delete' => '',
        'event_batchDelete' => '',
        'event_dataSource' => ''
    ),
);

/**
 * 頁尾組件
 */
$config['master']['widget']['pagefooter'] = array(
    'name'              => '頁尾組件',
    'code'              => 'pagefooter',
    'dataset'          => '',
    'widget'            => array(
        'code'  => 'footer'
    ),
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
        array('code' => 'pagefooter', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
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

/**
 * 帳號管理
 */
$config['master']['page']['account'] = array(
    'name'      => '帳號管理',
    'code'      => 'account',
    'widgets'   => array(
        array('code' => 'account', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

/**
 * 資料管理
 */
$config['master']['page']['dataset'] = array(
    'name'      => '資料管理',
    'code'      => 'dataset',
    'widgets'   => array(
        array('code' => 'dataset', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

/**
 * 組件管理
 */
$config['master']['page']['widget'] = array(
    'name'      => '組件管理',
    'code'      => 'widget',
    'widgets'   => array(
        array('code' => 'widget', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);


/**
 * 頁頭管理
 */
$config['master']['page']['header'] = array(
    'name'      => '頁頭管理',
    'code'      => 'header',
    'widgets'   => array(
        array('code' => 'header', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);


/**
 * 頁面管理
 */
$config['master']['page']['content'] = array(
    'name'      => '頁面管理',
    'code'      => 'content',
    'widgets'   => array(
        array('code' => 'content', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);


/**
 * 側欄管理
 */
$config['master']['page']['leftside'] = array(
    'name'      => '側欄管理',
    'code'      => 'leftside',
    'widgets'   => array(
        array('code' => 'leftside', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);


/**
 * 頁尾管理
 */
$config['master']['page']['footer'] = array(
    'name'      => '頁尾管理',
    'code'      => 'footer',
    'widgets'   => array(
        array('code' => 'footer', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);
