<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $fillable = ['term'];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'curriculum_subject');
    }
}