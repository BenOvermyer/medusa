<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Jenssegers\Mongodb\Model as Eloquent;

class Billet extends Model
{
    protected $fillable = ['billet_name'];

    public static $rules = ['billet_name' => 'required|unique:billets'];

    static function getBillets()
    {
        $results = self::all();
        $billets = [];

        foreach ($results as $billet) {
            $billets[$billet->billet_name] = $billet->billet_name;
        }

        asort($billets, SORT_NATURAL);
        return $billets;
    }


    public function getAssignedCount()
    {
        return User::where('assignment.billet', '=', $this->billet_name)->count();
    }
}
