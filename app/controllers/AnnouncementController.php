<?php

class AnnouncementController extends BaseController {

    public function index() {
        $announcements = Announcement::orderBy( 'publish_date' )->get();

        $viewData = [
            'announcements' => $announcements,
        ];

        return Response::view( 'announcement.index', $viewData );
    }

    public function show( $id ) {

        $announcement = Announcement::with( 'author' )->find( $id );

        $viewData = [
            'announcement' => $announcement,
        ];

        return Response::view( 'announcement.show', $viewData );
    }

    public function create() {

        $announcement = new Announcement;

        $viewData = [
            'announcement' => $announcement,
        ];

        return Response::view( 'announcement.create', $viewData );
    }

    public function edit( $id ) {

        $announcement = Announcement::find( $id );

        $viewData = [
            'announcement' => $announcement,
        ];

        return Response::view( 'announcement.edit', $viewData );
    }

    public function update( $id ) {

        $data = Input::all();

        $announcement = Announcement::find( $id );

        $announcement->update( $data );

        return Redirect::route( 'announcement.show', $id );
    }

    public function store() {

        $data = Input::all();

        $announcement = Announcement::create( $data );

        $announcement->is_published = false;

        $currentUser = Auth::getUser();

        $currentUser->announcements()->save( $announcement );

        return Redirect::route( 'announcement.show', $announcement->id );
    }

    public function destroy( $id ) {

        Announcement::destroy( $id );

        return Redirect::route( 'announcement.index' );
    }

}
