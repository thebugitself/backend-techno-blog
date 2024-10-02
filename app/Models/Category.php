<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'description',
    ];

    /**
     * Get the articles for the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'category_id', 'id');
    }


}
