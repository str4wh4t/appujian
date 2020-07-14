<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Users_groups_orm extends Eloquent
{
    protected $table = 'users_groups';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function users()
    {
        return $this->belongsTo('Orm\Users_orm','user_id');
    }
}
