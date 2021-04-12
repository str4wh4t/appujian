<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Data_daerah_orm extends Eloquent
{
    protected $table = 'data_daerah';
    protected $dateFormat = 'Y-m-d H:i:s';
    
}
