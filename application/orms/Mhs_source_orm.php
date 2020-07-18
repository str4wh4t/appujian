<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Mhs_source_orm extends Eloquent
{
    protected $table = 'mahasiswa_source';
    protected $primaryKey = 'id_mahasiswa';
    protected $dateFormat = 'Y-m-d H:i:s';
    
}
