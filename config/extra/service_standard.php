<?php

//服务标准
return [
    'classroom' =>  [           //课堂
        'before'    => [        //课前
            [
                'name'      => 'remind',
                'title'     => '课前提醒',
                'system'    => 1,            //是否系统服务
                'rids'      => [1],           //哪些角色可以做
                'enable'    => 1,            //启用禁用
                'object'    => 0,           // 0:班级服务 1:个性服务
                'service_level' => 0,        //服务星级，对个性服务有效 ,几星级的学员必须做
                'need_attach'   => 0,       //是否需要上传附件

            ],
            [
                 'name'      => 'prepare',
                 'title'     => '备课',
                 'system'    => 1,            //是否系统服务
                 'rids'      => [1],           //哪些角色可以做
                 'enable'    => 1,            //启用禁用,
                'object'    => 0,            // 0:班级服务 1:个性服务
                'service_level' => 0,        //服务星级，对个性服务有效 ,几星级的学员必须做
                'need_attach'   => 0       //是否需要上传附件
            ],
            [
                'name'      => 'school_arrive',
                'title'     => '到校通知',
                'system'    => 1,            //是否系统服务
                'rids'      => [1],           //哪些角色可以做
                'enable'    => 1,            //启用禁用
                'object'    => 0,            // 0:班级服务 1:个性服务
                'service_level' => 0,        //服务星级，对个性服务有效 ,几星级的学员必须做
                'need_attach'   => 0       //是否需要上传附件
            ],
        ],
        'in'        => [        //课中
            [
                'name'      => 'attendance',
                'title'     => '考勤',
                'system'    => 1,            //是否系统服务
                'rids'      => [1,2,4,5],    //哪些角色可以做
                'enable'    => 1,            //启用禁用
                'object'    => 0,           // 0:班级服务 1:个性服务
                'service_level' => 0,        //服务星级，对个性服务有效 ,几星级的学员必须做
                'need_attach'   => 0,       //是否需要上传附件
            ]
        ],
        'after'     => [        //课后
            [
                'name'      => 'review',
                'title'     => '课评',
                'system'    => 1,            //是否系统服务
                'rids'      => [1],    //哪些角色可以做
                'enable'    => 1,            //启用禁用
                'object'    => 0,           // 0:班级服务 1:个性服务
                'service_level' => 0,        //服务星级，对个性服务有效 ,几星级的学员必须做
                'need_attach'   => 0,       //是否需要上传附件
            ],
            [
                'name'      => 'homework',
                'title'     => '作业',
                'system'    => 1,
                'rids'      => [1],
                'enable'    => 1,
                'object'    => 0,           // 0:班级服务 1:个性服务
                'service_level' => 0,        //服务星级，对个性服务有效 ,几星级的学员必须做
                'need_attach'   => 0,       //是否需要上传附件
            ]
        ]
    ],
    'term'      => [            //学期
        'first'     => [        //期初

        ],
        'middle'    => [        //期中
            [
                'name'      => 'return_visit',
                'title'     => '回访',
                'system'    => 1,            //是否系统服务
                'rids'      => [1],     //哪些角色可以做
                'enable'    => 1,            //启用禁用
                'object'    => 0,           // 0:班级服务 1:个性服务
                'service_level' => 0,        //服务星级，对个性服务有效 ,几星级的学员必须做
                'need_attach'   => 0,       //是否需要上传附件
            ]
        ],
        'last'      => [        //期末
            [
                'name'      => 'artwork',
                'title'     => '作品',
                'system'    => 1,
                'rids'      => [1],
                'enable'    => 1,
                'object'    => 0,            // 0:班级服务 1:个性服务
                'service_level' => 0,        //服务星级，对个性服务有效 ,几星级的学员必须做
                'need_attach'   => 0,       //是否需要上传附件
            ]
        ]
    ]
];
