<?php
namespace app\validate;

use think\Validate;
class ValidateRule extends Validate
{
    protected $rule =   [
        'site|采集网站'         => 'require|max:100',
        'site_url|采集网站域名'  => 'require|url',
        'title_rule|标题规则'   => 'require|max:255',
        'content_rule|内容规则' => 'require|max:255',
        'page_url|分页链接'     => 'max:100',
        'page_num|采集分页数目'  => 'number',
    ];
    
    protected $message  =   [
        'site.require'         => "采集网站名称必须填写",
        'site.max'             => '网站名称最多不能超过100个字符',
        'site_url.require'     => "网站域名必须填写",
        'site_url.url'         => '采集网站域名格式不正确',
        'title_rule.require'   => '标题规则必须填写',
        'title_rule.max'       => '标题规则不得超过255个字符',
        'content_rule.require' => '内容规则必须填写',
        'content_rule.max'     => '内容规则不得超过255个字符',
        'page_url.max'         => '分页链接长度不得超过100个字符',
        'page_num.number'      => '采集分页数目必须填写数字',
        
    ];
}
