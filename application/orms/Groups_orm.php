<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Groups_orm extends Eloquent
{
    protected $table = 'groups';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function users()
    {
        return $this->belongsToMany('Orm\Users_orm','users_groups','group_id','user_id');
    }
    
    public function users_groups()
    {
        return $this->hasMany('Orm\Users_groups_orm','group_id');
    }
}
