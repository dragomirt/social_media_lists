<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeopleList extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ["name"];

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'people_list_people');
    }
}
