<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    protected $table = 'users';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'nama', 'telepon', 'alamat', 
        'tanggal_lahir', 'jenis_kelamin', 'status'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'last_login' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function pencatatanPupuk()
    {
        return $this->hasMany(PencatatanPupuk::class, 'user_id');
    }

    public function penjualanSayur()
    {
        return $this->hasMany(PenjualanSayur::class, 'user_id');
    }

    public function belanjaModal()
    {
        return $this->hasMany(BelanjaModal::class, 'user_id');
    }

    public function nutrisiPupuk()
    {
        return $this->hasMany(NutrisiPupuk::class, 'user_id');
    }

    public function dataSayur()
    {
        return $this->hasMany(DataSayur::class, 'user_id');
    }

    public function hasPermission($permission)
    {
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('nama', $permission)) {
                return true;
            }
        }
        return false;
    }
}
