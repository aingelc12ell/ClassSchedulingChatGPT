<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['name'];

    public function qualifiedSubjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject');
    }
}