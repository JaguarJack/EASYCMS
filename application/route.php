<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    'ruili/:id'     => ['home/Category/index', ['method' => 'get'],['id' => '\d{2}']],
    'detail/:id'    => ['home/Category/detail',['method' => 'get'],['id' => '\d+']],
    'usercenter'    => ['home/Usercenter/index',['method' => 'get']],
    'edituserinfo'  => ['home/Usercenter/editUserInfo',['method' => 'get']],
    'storearticle'  => ['home/Usercenter/storeArticle',['method' => 'get']],
 
];
