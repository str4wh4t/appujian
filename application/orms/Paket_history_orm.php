<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Paket_history_orm extends Eloquent
{
    protected $table = 'paket_history';
    protected $dateFormat = 'Y-m-d H:i:s';

    public function users()
    {
        return $this->belongsTo('Orm\Users_orm', 'users_id');
    }

    public function paket()
    {
        return $this->belongsTo('Orm\Paket_orm', 'paket_id');
    }
    
}
