<?php
namespace app\admin\model;

use think\Model;

/**
 * description:公共Model,保存一些公共属性
 * @author wuyanwen
 */
class Common extends Model
{
    const DEL_STATUS = 1;
    const NORMAL_STATUS = 0;
}