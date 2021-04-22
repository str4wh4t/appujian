<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Mhs_matkul_orm extends Eloquent
{
    protected $table = 'mahasiswa_matkul';
	protected $dateFormat = 'Y-m-d H:i:s';
    
    public function mhs()
    {
        return $this->belongsTo('Orm\Mhs_orm','mahasiswa_id','id_mahasiswa');
    }

    public function matkul()
    {
        return $this->belongsTo('Orm\Matkul_orm', 'matkul_id', 'id_matkul');
    }
    
}
