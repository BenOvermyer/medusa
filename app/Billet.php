<?php

namespace App;

use Moloquent\Eloquent\Model as Eloquent;

class Billet extends Eloquent
{
    protected $fillable = ['billet_name'];

    public static $rules = ['billet_name' => 'required|unique:billets'];

    public static function getBillets()
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
