<?php

namespace app\common\validate;

use think\Validate;

class User extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
    protected $rule = [
        'username' => 'require|max:20',
        'password' => 'require|max:40',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'username.require'      => "username_require",
        'username.max'          => "username_too_long",

        'password.require'      => "password_require",
        'password.max'          => "password_too_long",
    ];
}
