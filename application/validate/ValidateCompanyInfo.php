<?php
namespace app\validate;
use think\Validate;

class ValidateCompanyInfo extends Validate
{
    protected $rule =   [
        'company|公司名称'      => 'require|max:100',
        'addr|公司地址'         => 'max:255',
        'phone|联系方式'        => 'max:255',  
        'qq|QQ号码'            => 'max:255',
        'email|站长邮箱'        => 'email',
    ];

    protected $message  =   [
        'company_name.require' => '公司名称不能为空',
        'company_name.max'     => '公司名称最多不能超过100个字符',
        'link.max'             => '联系方式最多不能超过255个字符',
        'qq.max'               => 'qq号码最多不超过255个字符',
        'email.email'          => '邮箱格式不正确',
    ];
}