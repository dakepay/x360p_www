<?php

namespace app\api\model;

class SystemVersion extends Base
{
    protected $connection = 'db_center';
    protected $table = 'pro_version';

    protected $skip_og_id_condition = true;
    
}