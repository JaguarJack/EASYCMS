<?php
namespace app\validate;
use think\Validate;

class ValidateRegisterInfo extends Validate
{
    protected $rule =   [
        'account|账号'      => 'require|alphaNum|max:12|min:6/',
        'name|昵称'         => 'require|chsAlpha|max:15|min:3',
        'password|密码'     => 'require|alphaDash|max:16|min:6',  
        'email|邮箱'        => 'email',
        'code|验证码'        => 'require',
    ];

    protected $message  =   [
        'account.require'      => '账号必须填写',
        'account.alphaNum'     => '账号必须是字母和数字组合',
        'account.max'          => '账号长度最大不超过12个字符',
        'account.min'          => '账号长度最小不低于6个字符',
        'name.require'         => '昵称必须填写',
        'name.chsAlpha'        => '昵称是字符与中文字符组合',
        'name.max'             => '昵称最大不超过15个字符',
        'name.min'             => '昵称最小不低于3个字符',
        'pwd.require'          => '密码必须填写',
        'pwd.alphaDash'        => '密码只能是字母、数字和下划线_及破折号-',
        'pwd.max'              => '密码最大长度不得超过16个字符',
        'pwd.min'              => '密码最小长度不得低于6个字符',
        'email.email'          => '邮箱格式不正确',
        'code.require'         => '请填写验证码',
    ];
}