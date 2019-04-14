<?php

namespace App;

use Moloquent\Eloquent\SoftDeletes;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ChangeRequest extends Eloquent
{
    use SoftDeletes;

    protected $fillable = ['user', 'requestor', 'req_type', 'old_value', 'new_value', 'status'];
}
