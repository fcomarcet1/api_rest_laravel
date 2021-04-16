<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'title',
        'description'
    ];

    /**
     * Factories && Seeders.
     */
    /*protected static function boot()
    {
        parent::boot();
        self::creating(function ($table){
            if (!app()->runningInConsole()){
                $table->user_id = auth()->id();
            }
        });
    }*/

    /**
     * Gets user owner of the post [One To Many inverse (N:1)].
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Gets category of the post [One To Many inverse (N:1)].
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

}
