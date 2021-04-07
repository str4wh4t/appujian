<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Paket_history_orm extends Eloquent
{
    protected $table = 'paket_history';
    protected $dateFormat = 'Y-m-d H:i:s';

    public function mhs()
    {
        return $this->belongsTo('Orm\Mhs_orm', 'mahasiswa_id');
    }

    public function paket()
    {
        return $this->belongsTo('Orm\Paket_orm', 'paket_id');
    }
    
}
