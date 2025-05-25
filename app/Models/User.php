<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Client;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'verefication_code',
        'verefi_code_exp_date',
        'password',
        'prof_img',
        'active',
        'role_id',
        'device_token',
        'password_counter',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
       // 'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class, 'user_id');
    }

    public function historiesFrom(): HasMany
    {
        return $this->hasMany(History::class, 'from_user')->orderBy('id','DESC');
    }

    public function historiesTo(): HasMany
    {
        return $this->hasMany(History::class, 'to_user')->orderBy('id','DESC');
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'user_id');
    }

    public function user_vouchers(): HasMany
    {
        return $this->hasMany(User_Voucher::class, 'user_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'user_id');
    }
    public function admin_actions(): HasMany
    {
        return $this->hasMany(Admin_Action::class, 'admin_id');
    }
    public function Partner()
    {
        return $this->hasOne(Partner::class, 'user_id');
    }
    public function Cashier()
    {
        return $this->hasOne(Cashier::class, 'user_id');
    }
    public function Client()
    {
        return $this->hasOne(Client::class, 'user_id');
    }
    public function Charity()
    {
        return $this->hasOne(Charity::class, 'user_id');
    }

}
