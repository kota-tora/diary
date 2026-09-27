<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['content', 'img_name'])]
#[Table(
    key: 'diary_id'
)]
class Diary extends Model
{
    use HasFactory;
    use SoftDeletes;
}
