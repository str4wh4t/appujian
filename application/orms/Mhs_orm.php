<?php
namespace Orm;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Orm\Tahun;

class Mhs_orm extends Eloquent
{
    protected $table = 'mahasiswa';
    protected $primaryKey = 'id_mahasiswa';
    protected $dateFormat = 'Y-m-d H:i:s';


    public function users(){
        return $this->hasOne('Orm\Uses_orm', 'username', 'nim');
    }
    
    public function matkul()
    {
        return $this->belongsToMany('Orm\Matkul_orm','mahasiswa_matkul','mahasiswa_id','matkul_id');
    }
    
    public function h_ujian()
    {
        return $this->hasMany('Orm\Hujian_orm','mahasiswa_id');
    }

    public function h_ujian_history()
    {
        return $this->hasMany('Orm\Hujian_history_orm','mahasiswa_id');
    }
    
    public function mhs_matkul()
    {
        return $this->hasMany('Orm\Mhs_matkul_orm','mahasiswa_id');
    }

    public function membership_history()
    {
        return $this->hasMany('Orm\Membership_history_orm', 'mahasiswa_id');
    }

    public function membership_aktif()
    {
        return $this->hasOne('Orm\Membership_history_orm', 'mahasiswa_id', 'id_mahasiswa')->where('stts', MEMBERSHIP_STTS_AKTIF);
    }

    public function membership()
    {
        return $this->belongsToMany('Orm\Membership_orm', 'membership_history', 'mahasiswa_id', 'membership_id');
    }

    public function paket()
    {
        return $this->belongsToMany('Orm\Paket_orm', 'paket_history', 'mahasiswa_id', 'paket_id');
    }

    public function paket_history()
    {
        return $this->hasMany('Orm\Paket_history_orm', 'mahasiswa_id');
    }

    public function trx_payment()
    {
        return $this->hasMany('Orm\Trx_payment_orm', 'mahasiswa_id');
    }

    // public static function boot(){
	// 	parent::boot();

	// 	static::saving(function ($model) {
	// 		$model->tahun = Tahun::get_tahun_aktif();
	// 	});
	// }

    // protected static function booted(){
	// 	static::addGlobalScope(new Tahun);
	// }
    
}
