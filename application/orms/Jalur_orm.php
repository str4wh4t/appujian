<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Jalur_orm extends Eloquent
{
    protected $table = 'jalur';
    protected $dateFormat = 'Y-m-d H:i:s';
    
}
