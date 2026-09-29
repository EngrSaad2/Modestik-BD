<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'model', 'model_id', 'data', 'ip', 'user_agent'];
    protected $casts = ['data' => 'array'];

    public function user() { return $this->belongsTo(User::class); }

    public static function log($action, $model = null, $modelId = null, $data = null)
    {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'data' => $data,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
