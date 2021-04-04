<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Users_orm extends Eloquent
{
    protected $table = 'users';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function groups()
    {
        return $this->belongsToMany('Orm\Groups_orm', 'users_groups', 'user_id', 'group_id');
    }
    
    public function users_groups()
    {
        return $this->hasMany('Orm\Users_groups_orm', 'user_id');
    }

    public function membership_history()
    {
        return $this->hasMany('Orm\Membership_history_orm', 'users_id');
    }

    public function membership()
    {
        return $this->belongsToMany('Orm\Membership_orm', 'membership_history', 'users_id', 'membership_id');
    }

    public function paket()
    {
        return $this->belongsToMany('Orm\Paket_orm', 'paket_history', 'users_id', 'paket_id');
    }

    public function paket_dibeli(int $paket_id)
    {
        return $this->belongsToMany('Orm\Paket_orm', 'paket_history', 'users_id', 'paket_id')->where('paket_id', $paket_id);
    }

}
