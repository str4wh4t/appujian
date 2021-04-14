<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Users_temp_orm extends Eloquent
{
    protected $table = 'users_temp';
    protected $dateFormat = 'Y-m-d H:i:s';

}
