<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Paket_orm extends Eloquent
{
    protected $table = 'paket';
    protected $dateFormat = 'Y-m-d H:i:s';


    public function mhs()
    {
        return $this->belongsToMany('Orm\Mhs_orm','paket_history', 'paket_id', 'mahasiswa_id');
    }
    
    public function paket_history()
    {
        return $this->hasMany('Orm\Paket_history_orm', 'paket_id');
    }

    public function paket_matkul()
    {
        return $this->hasMany('Orm\Paket_matkul_orm', 'paket_id');
    }

    public function matkul()
    {
        return $this->belongsToMany('Orm\Matkul_orm','paket_matkul', 'paket_id', 'matkul_id');
    }
    
}
