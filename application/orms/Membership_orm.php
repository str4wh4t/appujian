<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Membership_orm extends Eloquent
{
    protected $table = 'membership';
    protected $dateFormat = 'Y-m-d H:i:s';


    public function mhs()
    {
        return $this->belongsToMany('Orm\Mhs_orm','membership_history', 'membership_id', 'mahasiswa_id');
    }
    
    public function membership_history()
    {
        return $this->hasMany('Orm\Membership_history_orm', 'membership_id');
    }

    
}
