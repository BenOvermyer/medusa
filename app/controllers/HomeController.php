<?php

class HomeController extends BaseController
{

    public function dashboard()
    {
        if ( Auth::check() ) {
            $user = Auth::user();

            $viewData = [
                'greeting' => $user->getGreetingArray(),
                'user' => $user,
                'chapter' => Chapter::find( $user->getPrimaryAssignmentId() ),
            ];
//die("<pre>" . print_r($viewData, true));
            return View::make( 'dashboard', $viewData );
        }

        return Redirect::intended( '/' );
    }

    public function home()
    {
        return View::make( 'home' );
    }

}
