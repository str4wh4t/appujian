<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Paket_matkul_orm extends Eloquent
{
    protected $table = 'paket_matkul';
    protected $dateFormat = 'Y-m-d H:i:s';

    public function matkul()
    {
        return $this->belongsTo('Orm\Matkul_orm', 'matkul_id', 'id_matkul');
    }

    public function paket()
    {
        return $this->belongsTo('Orm\Paket_orm', 'paket_id');
    }
    
}
