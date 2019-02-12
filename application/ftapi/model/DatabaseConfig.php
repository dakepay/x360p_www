<?php
namespace app\ftapi\model;

class DatabaseConfig extends Base
{
    protected $connection = 'db_center';

    protected $skip_og_id_condition = true;
}