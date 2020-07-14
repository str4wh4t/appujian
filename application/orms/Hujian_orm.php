<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Hujian_orm extends Eloquent
{
    protected $table = 'h_ujian';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function m_ujian()
    {
        return $this->belongsTo('Orm\Mujian_orm','ujian_id');
    }
    
    public function mhs()
    {
        return $this->belongsTo('Orm\Mhs_orm','mahasiswa_id');
    }
    
    public function jawaban_ujian()
    {
        return $this->hasMany('Orm\Jawaban_ujian_orm','ujian_id');
    }
    
    public function soal()
    {
        return $this->belongsToMany('Orm\Soal_orm','jawaban_ujian','ujian_id','soal_id');
    }
    
}
