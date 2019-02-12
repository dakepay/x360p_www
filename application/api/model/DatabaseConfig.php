<?php
/**
 * Created by PhpStorm.
 * User: yaorui
 * Date: 2017/11/25
 * Time: 15:29
 */
namespace app\api\model;

class DatabaseConfig extends Base
{
    protected $connection = 'db_center';

    protected $skip_og_id_condition = true;
}