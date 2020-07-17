<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Daftar_hadir_orm extends Eloquent
{
    protected $table = 'daftar_hadir';
	protected $dateFormat = 'Y-m-d H:i:s';
	
    public function pengawas()
    {
        return $this->hasOne('Orm\Users_groups_orm','id','absen_by');
    }
    
    public function mhs_ujian()
    {
        return $this->belongsTo('Orm\Mhs_ujian_orm','mahasiswa_ujian_id');
    }
    
}
