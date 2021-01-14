<?php
$widget['data']['metadata'] = array(
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
