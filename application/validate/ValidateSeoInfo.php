<?php
namespace app\validate;
use think\Validate;

class ValidateSeoInfo extends Validate
{
    protected $rule =   [
        'title|站点标题'        => 'require|max:100',
        'keywords|站点关键词'    => 'max:255',
        'description|站点描述'  => 'max:255',  
    ];

    protected $message  =   [
        'title.max'          => '站点标题最多不能超过100个字符',
        'keywords.max'       => '站点关键词不能超过255个字符',
        'description.max'    => '站点描述最多不能超过255个字符',
    ];
}