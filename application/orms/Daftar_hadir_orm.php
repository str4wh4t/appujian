<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Daftar_hadir_orm extends Eloquent
{
    protected $table = 'daftar_hadir';
	protected $dateFormat = 'Y-m-d H:i:s';
	
    public function pengawas()
    {
        return $this->belongsTo('Orm\Users_groups_orm','absen_by', 'id');
    }
    
    public function mhs_ujian()
    {
        return $this->hasOne('Orm\Mhs_ujian_orm', 'id', 'mahasiswa_ujian_id');
    }
    
}
