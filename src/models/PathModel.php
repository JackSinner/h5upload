<?php

namespace Encore\h5upload\models;

use Illuminate\Database\Eloquent\Model;

class PathModel extends Model
{
    protected $table = 'h5upload_path';
    protected $fillable = ['title', 'pid'];
}
