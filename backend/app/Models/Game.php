<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'description',
        'author_id',
        'current_version',
        'thumbnail',
        'game_path'
    ];

    protected $casts = [
        'upload_timestamp' => 'datetime'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }
}