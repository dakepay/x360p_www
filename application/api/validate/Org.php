<?php
/** 
 * Author: luo
 * Time: 2017-10-14 11:07
**/

namespace app\api\validate;

class Org extends Base
{
    protected $rule = [
        ['org_name|机构名称', 'require'],
        ['host|登录域名', 'alphaNum|length:4,10'],
        ['student_num_limit|学生数量', 'require|number|egt:100']
    ];

    protected $scene = [
    ];

}