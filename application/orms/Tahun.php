<?php
namespace Orm;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Orm\Setting;

class Tahun implements Scope
{
	
    public static function get_tahun_aktif(){
    	$tahun = Setting::where(['variabel' => 'tahun_aktif', 'flag' => FLAG_AKTIF])->first()->nilai;
    	return $tahun;
	}
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
    	$tahun = self::get_tahun_aktif();
        $builder->where('tahun', '=', $tahun);
    }
    
}
