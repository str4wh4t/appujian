<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Membership_history_orm extends Eloquent
{
    protected $table = 'membership_history';
    protected $dateFormat = 'Y-m-d H:i:s';

    public function mhs()
    {
        return $this->belongsTo('Orm\Mhs_orm', 'mahasiswa_id');
    }

    public function membership()
    {
        return $this->belongsTo('Orm\Membership_orm', 'membership_id');
    }
    
}
