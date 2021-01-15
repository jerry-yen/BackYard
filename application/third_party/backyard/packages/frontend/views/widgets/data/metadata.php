<?php
$metadata = array(
    'fields'   => array(
        array(
            'name' => '操作權限', 
            'dbVariable' => 'permission', 
            'frontendVariable' => 'permission', 
            'component' => 'checkbox', 
            'validator' => array(), 
            'converter' => array(), 
            'source' => '{"ADD":"新增","MODIFY":"修改","DELETE":"刪除","SORT":"排序","EXPORT":"匯出","IMPORT":"匯入"}',
            'fieldTip' => ''
        ),
        array(
            'name' => '清單欄位', 
            'dbVariable' => 'listfields', 
            'frontendVariable' => 'listfields', 
            'component' => 'text', 
            'validator' => array(), 
            'converter' => array(), 
            'source' => '', 
            'fieldTip' => ''
        ),
    ),
    'events' => array(
        array(
            'name' => '新增事件API', 
            'dbVariable' => 'add', 
            'frontendVariable' => 'add', 
            'component' => 'text', 
            'validator' => array(), 
            'converter' => array(), 
            'source' => '', 
            'fieldTip' => ''
        ),

        array(
            'name' => '修改事件API', 
            'dbVariable' => 'modify', 
            'frontendVariable' => 'modify', 
            'component' => 'text', 
            'validator' => array(), 
            'converter' => array(), 
            'source' => '', 
            'fieldTip' => ''
        ),

        array(
            'name' => '刪除事件API', 
            'dbVariable' => 'delete', 
            'frontendVariable' => 'delete', 
            'component' => 'text', 
            'validator' => array(), 
            'converter' => array(), 
            'source' => '', 
            'fieldTip' => ''
        ),

        array(
            'name' => '資料來源API', 
            'dbVariable' => 'dataSource', 
            'frontendVariable' => 'dataSource', 
            'component' => 'text', 
            'validator' => array(), 
            'converter' => array(), 
            'source' => '', 
            'fieldTip' => ''
        )
    ),
);
