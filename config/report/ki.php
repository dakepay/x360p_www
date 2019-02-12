<?php

return [
    'full_school_student_nums'  =>  [   //满校人数定义
        'week_ts_nums'          =>  14,  //周平均上课时段数
        'classroom_per_nums'    =>  6,  //教室平均上课人数
        'room_nums'             =>  6,  //统计标准教室数

    ],
    'transfer_student_nums' => [  //转介绍人数定义
        'from_dids' => [],          //包括的招生来源类型
    ],
    'mc_student_nums'   =>  [       //市场名单数定义：
        'from_dids' => [],          //包括的招生来源类型
	'exclude_from_dids' => []  //排除市场名单招生来源类型
    ],
    'mc_valid_student_nums' => [
        'intention_level'  => 2,  //意向级别 默认为2星以上 不包括两星
    ],
    'cr_arrange_rate'           => [    //周上课率
        'class_room_base_nums'      =>  20, //校区每间教室每周上课基数
    ]
];