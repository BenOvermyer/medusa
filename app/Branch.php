<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Model as Eloquent;

class Branch extends Model
{

    protected $fillable = [ 'branch', 'branch_name' ];

    static function getBranchList()
    {
        foreach (Branch::all(['branch', 'branch_name']) as $branch) {
            $branches[$branch['branch']] = $branch['branch_name'];
        }

        asort($branches);

        $branches = ['' => 'Select a Branch'] + $branches;

        return $branches;
    }

    static function getNavalBranchList()
    {
        foreach (Branch::whereIn('branch', ['RMN', 'GSN', 'IAN', 'RHN'])->get(['branch', 'branch_name']) as $branch) {
            $branches[$branch['branch']] = $branch['branch_name'];
        }

        asort($branches);

        $branches = ['' => 'Select a Branch'] + $branches;

        return $branches;
    }
}
