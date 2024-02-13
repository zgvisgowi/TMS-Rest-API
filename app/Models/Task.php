<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;
    protected $fillable=['name','description','user_id','task_id'];

    public  function user():BelongsTo
    {
        return $this->BelongsTo(User::class);
    }
    public function parent():BelongsTo
    {
        return $this->BelongsTo(Task::class,'task_id');
    }
    public function children():HasMany{
        return $this->hasMany(Task::class,'task_id');
    }


}
