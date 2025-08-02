<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['name', 'curriculum_id'];

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }
}