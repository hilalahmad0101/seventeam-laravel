<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{

    protected $table = 'videos';
    protected $fillable = [
        'title',
        'description',
        'category_id',
        'file_name',
        'file_path',
        'current_chunk',
        'thumbnail',
        'total_chunks',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
