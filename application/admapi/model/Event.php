<?php
/** 
 * Author: luo
 * Time: 2017-12-18 11:45
**/

namespace app\admapi\model;

class Event extends Base
{
    protected $type = [
        'event_start_time' => 'timestamp',
        'event_end_time' => 'timestamp',
    ];

}