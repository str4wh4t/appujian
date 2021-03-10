<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Membership_orm extends Eloquent
{
    protected $table = 'membership';
    protected $dateFormat = 'Y-m-d H:i:s';
    
}
