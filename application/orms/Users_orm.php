<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Users_orm extends Eloquent
{
    protected $table = 'users';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function groups()
    {
        return $this->belongsToMany('Orm\Groups_orm','users_groups','user_id','group_id');
    }
    
    public function users_groups()
    {
        return $this->hasMany('Orm\Users_groups_orm','user_id');
    }
}
