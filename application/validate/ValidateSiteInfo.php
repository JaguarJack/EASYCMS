<?php
namespace app\validate;
use think\Validate;

class ValidateSiteInfo extends Validate
{
    protected $rule =   [
        'site_name|站点名称'   => 'require|max:100',
        'site_link|站点地址'   => 'require|url',
        'record|备案信息'      => 'max:255',  
        'copyright|版权信息'   => 'max:255',
    ];

    protected $message  =   [
        'name.require'      => '站点名称不能为空',
        'name.max'          => '站点名称最多不能超过100个字符',
        'site_link.require' => '站点地址不能为空',
        'site_link.url'     => '不是有效的URL地址',
        'record.max'        => '备案信息最多不能超过255个字符',
        'copyright.max'     => '版权信息最多不能超过255个字符',    
    ];
}