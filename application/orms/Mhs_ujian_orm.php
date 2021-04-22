<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Mhs_ujian_orm extends Eloquent
{
    protected $table = 'mahasiswa_ujian';
	protected $dateFormat = 'Y-m-d H:i:s';
	
    public function m_ujian()
    {
        return $this->belongsTo('Orm\Mujian_orm','ujian_id');
    }

    public function mhs()
    {
        return $this->belongsTo('Orm\Mhs_orm', 'mahasiswa_id', 'id_mahasiswa');
    }
    
    public function daftar_hadir()
    {
        return $this->hasOne('Orm\Daftar_hadir_orm', 'mahasiswa_ujian_id');
    }
    
    public function h_ujian()
    {
        return $this->hasOne('Orm\Hujian_orm','mahasiswa_ujian_id', 'id');
    }

    public function h_ujian_history()
    {
        return $this->hasMany('Orm\Hujian_history_orm','mahasiswa_ujian_id', 'id');
    }
    
}
