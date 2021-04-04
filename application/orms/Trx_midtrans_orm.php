<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Trx_midtrans_orm extends Eloquent
{
    protected $table = 'trx_midtrans';
    protected $dateFormat = 'Y-m-d H:i:s';
    
}
