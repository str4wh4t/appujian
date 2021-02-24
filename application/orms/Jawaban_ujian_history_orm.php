<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Jawaban_ujian_history_orm extends Eloquent
{
    protected $table = 'jawaban_ujian_history';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function h_ujian_history()
    {
        return $this->belongsTo('Orm\Hujian_history_orm','ujian_id');
    }
    
    public function soal()
    {
        return $this->belongsTo('Orm\Soal_orm','soal_id');
    }
    
}
