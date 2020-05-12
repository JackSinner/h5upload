<?php

namespace Encore\h5upload\models;

use Illuminate\Database\Eloquent\Model;

class ResourceModel extends Model
{
    protected $table = 'h5upload_resources';
    protected $fillable = ['key', 'path_id', 'size'];
}
