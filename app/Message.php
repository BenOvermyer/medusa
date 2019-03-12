<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Message extends Eloquent
{
    protected $fillable = ['source', 'severity', 'msg'];
}
