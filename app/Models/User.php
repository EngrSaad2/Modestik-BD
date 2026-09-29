<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'avatar', 'status', 'is_vip',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_vip' => 'boolean',
        ];
    }

    public function getLifetimePurchaseAttribute()
    {
        return $this->orders()->where('status', 'delivered')->sum('total');
    }

    public function scopeVip($query)
    {
        return $query->where('is_vip', true);
    }

    // Relationships
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCustomers($query)
    {
        return $query->whereHas('roles', function($q) {
            $q->where('name', 'customer');
        })->orWhereDoesntHave('roles');
    }

    // Accessors
    public function getAvatarUrlAttribute()
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : asset('images/admin_avatar.png');
    }

    public function getWalletBalanceAttribute()
    {
        return $this->walletTransactions()
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'credit' THEN amount ELSE -amount END), 0) as balance")
            ->value('balance');
    }

    public function getTotalLoyaltyPointsAttribute()
    {
        return $this->loyaltyPoints()
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'earned' THEN points WHEN type = 'redeemed' THEN -points ELSE 0 END), 0) as total")
            ->value('total');
    }
}
