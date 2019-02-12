<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/6/30
 * Time: 17:54
 */
namespace app\api\validate;

use think\Validate;

class Goods extends Validate
{
    protected $rule = [
        ['lid|课程ID', 'require|number'],
        ['title|商品名称', 'require'],
        ['is_global|应用到所有校区', 'in:0,1'],
        ['bids|应用到的校区', 'array'],
        ['short_desc|简要描述', 'require'],
        ['goods_image|商品图片URL', 'require'],
        ['content|商品介绍', 'require'],
        ['sale_price|销售价', 'require|number'],
        ['limit_nums|限制报名人数', 'number'],
        ['order_limit_time|报名截止时间', 'require|date'],
        ['status|商品状态', 'require|in:0,1'],
        ['is_top|是否置顶', 'require|in:0,1'],
        ['is_hot|是否热门课程', 'require|in:0,1'],
    ];

    protected $scene = [
        'edit' => ['title', 'short_desc', 'goods_image', 'content', 'sale_price', 'on_time', 'off_time', 'order_limit_time', 'status', 'is_top', 'is_hot', 'year', 'season','limit_nums'],
        'status' => ['status'],
        'hot' => ['is_hot'],
        'top' => ['is_top'],
    ];
}