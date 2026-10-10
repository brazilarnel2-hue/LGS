<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['logo_path'];

    /**
     * Get the single settings row, creating it if it doesn't exist yet.
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}