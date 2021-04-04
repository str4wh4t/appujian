<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Membership_orm extends Eloquent
{
    protected $table = 'membership';
    protected $dateFormat = 'Y-m-d H:i:s';


    public function users()
    {
        return $this->belongsToMany('Orm\Users_orm','membership_history', 'membership_id', 'users_id');
    }
    
    public function membership_history()
    {
        return $this->hasMany('Orm\Membership_history_orm', 'membership_id');
    }

    
}
