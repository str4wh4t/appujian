<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Login_log_orm extends Eloquent
{
    protected $table = 'login_log';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    public function users()
    {
        return $this->belongsTo('Orm\Users_orm');
    }
}
