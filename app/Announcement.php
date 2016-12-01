<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Model as Eloquent;

class Announcement extends Model
{

    public $fillable = [
        'user_id',
        'summary',
        'body',
        'is_published',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getPublishLabels()
    {
        return [  0  => 'Unpublished' , 1 => 'Publish' , ];
    }
}
