<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Dosen_matkul_orm extends Eloquent
{
    protected $table = 'dosen_matkul';
	protected $dateFormat = 'Y-m-d H:i:s';
}
