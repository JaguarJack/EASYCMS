<?php
namespace app\validate;
use think\Validate;

class ValidateArticle extends Validate
{
    protected $rule =   [
        'cid|文章栏目'          => 'require',
        'title|文章标题'        => 'require|max:100',
        'intro|文章简介'        => 'require|max:255',
        'content|文章内容'      => 'require',  
    ];

    protected $message  =   [
        'cid.require'          => '文章所属栏目必须选择',
        'title.require'        => '文章标题必须填写',
        'title.max'            => '文章标题最多不能超过100个字符',
        'intro.require'        => '文章简介必须填写',
        'intro.max'            => '文章简介最多不超过255个字符',
        'content.require'      => '文章内容必须填写',
    ];
}