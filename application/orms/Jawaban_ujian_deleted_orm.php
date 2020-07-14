<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Jawaban_ujian_deleted_orm extends Eloquent
{
    protected $table = 'jawaban_ujian_deleted';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function h_ujian_deleted()
    {
        return $this->belongsTo('Orm\Hujian_deleted_orm','ujian_id');
    }
    
    public function soal()
    {
        return $this->belongsTo('Orm\Soal_orm','soal_id');
    }
    
}
