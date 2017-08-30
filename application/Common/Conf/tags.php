<?php
return array( // 添加下面一行定义即可
    'app_init' => array(
        'Common\Behavior\InitHookBehavior',
        'Common\Behavior\ResqueHookBehavior'
    ),
    'app_begin' => array(
        'Behavior\CheckLangBehavior',
        'Common\Behavior\UrldecodeGetBehavior'
    ),
    'view_filter' => array(
        'Behavior\TokenBuildBehavior',
        'Common\Behavior\TmplStripSpaceBehavior'
    ),
    'admin_begin' => array(
        'Common\Behavior\AdminDefaultLangBehavior'
    )
)
;