<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';


    protected $fillable = [
        'name',
    ];

    /**
     * Factories && Seeders.
     */
    protected static function boot()
    {
        parent::boot();
        self::creating(function ($table){
            if (!app()->runningInConsole()){
                $table->user_id = auth()->id();
            }
        });
    }

    /**
     * get all posts in a category (1:N).
     *
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

}
