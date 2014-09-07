<?php

class HomeController extends BaseController {

    public function showDashboard() {
        if ( Auth::check() ) {
            return View::make( 'dashboard', [ 
                'pageTitle' => 'Dashboard',
                'user' => Auth::user(),
            ] );    
        } else {
            return Redirect::intended( '/' );
        }
    }

    public function showWelcome()
    {
        return View::make( 'welcome', [ 'pageTitle' => 'Home' ] );
    }

}
