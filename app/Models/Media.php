<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['filename', 'path', 'disk', 'mime_type', 'size', 'alt', 'folder'];

    public function getUrlAttribute() { return asset('storage/' . $this->path); }
    public function getSizeFormattedAttribute() {
        $bytes = $this->size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
