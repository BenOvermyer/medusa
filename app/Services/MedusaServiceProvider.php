<?php
namespace App\Services;

use Illuminate\Support\ServiceProvider;
use App\Validators\MedusaValidators;

class MedusaServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        $this->app->validator->resolver(
            function ($translator, $data, $rules, $messages = [], $customAttributes = []) {
                return new MedusaValidators($translator, $data, $rules, $messages, $customAttributes);
            }
        );
    }
}
