<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'hp',
        'access',
        'jenis_kelamin',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function bid()
    {
      return $this->hasMany(Bid::class);
    }
    public function userAddress()
    {
      return $this->hasMany(UserAddress::class);
    }
    public function scopeMember($query)
    {
        return $query->where('access','member');
    }
    public function favorites()
{
    return $this->hasMany(Favorite::class);
}

}
