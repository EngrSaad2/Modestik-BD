<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'label', 'name', 'phone', 'division', 'district',
        'area', 'address', 'zip', 'is_default',
    ];

    protected $casts = ['is_default' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute()
    {
        return collect([$this->address, $this->area, $this->district, $this->division, $this->zip])
            ->filter()->join(', ');
    }
}
