<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ["name"];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(PersonList::class);
    }
}
