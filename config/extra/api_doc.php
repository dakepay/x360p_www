<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | Company: YG | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2017/2/28 18:13
// +----------------------------------------------------------------------
// | TITLE:文档列表
// +----------------------------------------------------------------------

return [
    '1' => ['name' => '机构PC端', 'id' => '1', 'parent' => '0', 'class'=>'','readme' =>''],//下面有子列表为一级目录
    '2' => ['name' => '说明', 'id' => '2', 'parent' => '1', 'class'=>'','readme' => '/md/api/readme.md'],//没有接口的文档，加载markdown文档
    '3' => ['name' => '公共接口', 'id' => '3', 'parent' => '1', 'readme' => '/md/api/api_open.md','class'=>\app\api\controller\Open::class],//开放接口文档
    '4'	=> ['name'	=> '用户接口','id'=>'4','parent'=>'1','readme'=>'','class'=>\app\api\controller\Users::class],//用户接口文档
    '5'	=> ['name'	=> '系统配置','id'=>'5','parent'=>'1','readme'=>'','class'=>\app\api\controller\Configs::class],//系统配置文档
    '6'	=> ['name'	=> '客户管理','id'=>'6','parent'=>'1','readme'=>'','class'=>\app\api\controller\Customers::class],//客户管理文档
];