<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

$protocol = (Request::secure()) ? "https:" : "http:";

$host = Request::server('HTTP_HOST');

$hostFull = $protocol . "//" . $host;

if (Auth::check()) {
    $authUser = Auth::user();
} else {
    $authUser = null;
}

View::share('serverUrl', $hostFull);
View::share('authUser', $authUser);

Route::get('/osa', ['as' => 'osa', 'uses' => 'HomeController@osa']);

// OAuth2 routes
Route::get(
    'oauth/authorize',
    function () {
        return app('oauth2')->authorize();
    }
);

Route::post(
    'oauth/authorize',
    function () {
        return app('oauth2')->authorizePost();
    }
);

Route::post(
    'oauth/token',
    function () {
        return app('oauth2')->token();
    }
);

Route::post(
    'oauth/updateuser',
    function () {
        return app('oauth2')->updateUser();
    }
);

Route::get(
    'oauth/profile',
    function () {
        return app('oauth2')->profile();
    }
);

Route::get(
    'oauth/user',
    function () {
        return app('oauth2')->user();
    }
);

Route::get(
    'oauth/lastupdate',
    function () {
        return app('oauth2')->lastUpdated();
    }
);

Route::get(
    'oauth/tistig',
    function () {
        return app('oauth2')->getTisTig();
    }
);

Route::get(
    'oauth/idcard',
    function () {
        return app('oauth2')->getIdCard();
    }
);

Route::get(
    'oauth/events',
    function () {
        return app('oauth2')->getScheduledEvents();
    }
);

Route::get(
    'oauth/checkin',
    function () {
        return app('oauth2')->checkMemberIn();
    }
);

Route::model('oauthclient', 'OAuthClient');
Route::resource('oauthclient', 'OAuthController', ['middleware' => 'auth']);

// Authentication
Route::get('/signout', ['as' => 'signout', 'uses' => 'AuthController@signout']);
Route::post('/signin', ['as' => 'signin', 'uses' => 'AuthController@signin']);
Route::get(
    '/register',
    ['as' => 'register', 'uses' => 'UserController@register']
);
Route::post('/apply', ['as' => 'user.apply', 'uses' => 'UserController@apply']);

// Users
Route::model('user', 'App\User');
Route::get(
    '/user/finddups/{billet2check}',
    ['as'     => 'user.dups',
    'uses'   => 'UserController@findDuplicateAssignment',
    'middleware' => 'auth'
    ]
);
Route::get(
    '/user/find/{user?}',
    ['as' => 'user.find', 'uses' => 'UserController@find', 'middleware' => 'auth']
);
Route::get('/user/review', [
  'as'     => 'user.review',
  'uses'   => 'UserController@reviewApplications',
  'middleware' => 'auth'
]);
Route::get(
    '/user/{user}/confirmdelete',
    ['as'     => 'user.confirmdelete',
    'uses'   => 'UserController@confirmDelete',
    'middleware' => 'auth'
    ]
);
Route::post(
    '/user/tos',
    ['as' => 'tos', 'uses' => 'UserController@tos', 'middleware' => 'auth']
);
Route::post(
    '/user/osa',
    ['as' => 'osa', 'uses' => 'UserController@osa', 'middleware' => 'auth']
);
Route::post(
    '/user/{user}/peerage',
    ['as'     => 'addOrEditPeerage',
    'uses'   => 'UserController@addOrEditPeerage',
    'middleware' => 'auth'
    ]
);
Route::get(
    '/user/{user}/peerage/{peerageId}',
    ['as'     => 'delete_peerage',
    'uses'   => 'UserController@deletePeerage',
    'middleware' => 'auth'
    ]
);
Route::post(
    '/user/{user}/note',
    ['as'     => 'addOrEditNote',
    'uses'   => 'UserController@addOrEditNote',
    'middleware' => 'auth'
    ]
);
Route::get(
    '/user/{user}/perm/{perm}/add',
    ['as'     => 'user.perm.add',
    'uses'   => 'UserController@addPerm',
    'middleware' => 'auth'
    ]
);
Route::get(
    '/user/{user}/perm/{perm}/delete',
    ['as'     => 'user.perm.del',
    'uses'   => 'UserController@deletePerm',
    'middleware' => 'auth'
    ]
);
Route::get('/users/{branch}', [
  'as'     => 'showBranch',
  'uses'   => 'UserController@showBranch',
  'middleware' => 'auth'
]);
Route::get('/user/rack', [
  'as'     => 'ribbonRack',
  'uses'   => 'UserController@buildRibbonRack',
  'middleware' => 'auth'
]);
Route::post('/user/rack/save', [
  'as'     => 'saverack',
  'uses'   => 'UserController@saveRibbonRack',
  'middleware' => 'auth'
]);

Route::resource('user', 'UserController', ['middleware' => 'auth']);
Route::get(
    '/user/{user}/approve',
    ['as'     => 'user.approve',
    'uses'   => 'UserController@approveApplication',
    'middleware' => 'auth'
    ]
);
Route::get(
    '/user/{user}/deny',
    ['as'     => 'user.deny',
    'uses'   => 'UserController@denyApplication',
    'middleware' => 'auth'
    ]
);
Route::get('/user/{user}/reset', [
  'as'     => 'user.getReset',
  'uses'   => 'UserController@getReset',
  'middleware' => 'auth'
]);
Route::post('/user/{user}/reset', [
  'as'     => 'user.postReset',
  'uses'   => 'UserController@postReset',
  'middleware' => 'auth'
]);

// Assignment Change Requests
Route::model('request', 'App\ChangeRequest');
Route::get(
    '/user_request/{user}/create',
    ['as'     => 'user.change.request',
    'uses'   => 'UserChangeRequestController@create',
    'middleware' => 'auth'
    ]
);
Route::post(
    '/user_request',
    ['as'     => 'user.change.store',
    'uses'   => 'UserChangeRequestController@store',
    'middleware' => 'auth'
    ]
);
Route::get(
    '/user_request/review',
    ['as'     => 'user.change.review',
    'uses'   => 'UserChangeRequestController@review',
    'middleware' => 'auth'
    ]
);
Route::get(
    '/user_request/approve/{request}',
    ['as'     => 'user.change.approve',
    'uses'   => 'UserChangeRequestController@approve',
    'middleware' => 'auth'
    ]
);
Route::get(
    '/user_request/deny/{request}',
    ['as'     => 'user.change.deny',
    'uses'   => 'UserChangeRequestController@deny',
    'middleware' => 'auth'
    ]
);

// Other Routes
Route::model('chapter', 'App\Chapter');
Route::model('echelon', 'App\Chapter');
Route::model('mardet', 'App\Chapter');
Route::model('unit', 'App\Chapter');
Route::model('anyunit', 'App\Chapter');

Route::get(
    '/home/{message?}',
    ['as' => 'home', 'uses' => 'HomeController@index']
);
Route::get('/', ['as' => 'root', 'uses' => 'HomeController@index']);
Route::get('/login', ['as' => 'login', 'uses' => 'HomeController@index']);
Route::get(
    '/chapter/{chapter}/decommission',
    ['as'     => 'chapter.decommission',
    'uses'   => 'ChapterController@decommission',
    'middleware' => 'auth'
    ]
);
Route::resource('chapter', 'ChapterController', ['middleware' => 'auth']);
Route::get(
    '/triadreport',
    ['as'     => 'chapter.triadreport',
    'uses'   => 'ChapterController@commandTriadReport',
    'middleware' => 'auth'
    ]
);
Route::get(
    '/export/{chapter}',
    ['as'     => 'roster.export',
    'uses'   => 'ChapterController@exportRoster',
    'middleware' => 'auth'
    ]
);
//Route::resource('announcement', 'AnnouncementController', ['middleware' => 'auth']);
Route::resource('report', 'ReportController', ['middleware' => 'auth']);

Route::get(
    '/report/getexams/{id}',
    ['as'     => 'report.getexams',
    'uses'   => 'ReportController@getCompletedExamsForCrew',
    'middleware' => 'auth'
    ]
);
Route::get('/report/send/{id}', [
  'as'     => 'report.send',
  'uses'   => 'ReportController@sendReport',
  'middleware' => 'auth'
]);

Route::get(
    '/echelon/{echelon}/deactivate',
    ['as'     => 'echelon.deactivate',
    'uses'   => 'EchelonController@deactivate',
    'middleware' => 'auth'
    ]
);
Route::resource('echelon', 'EchelonController', ['middleware' => 'auth']);

Route::model('unit', 'App\Chapter');
Route::get(
    '/unit/{unit}/deactivate',
    ['as'     => 'unit.deactivate',
    'uses'   => 'UnitController@deactivate',
    'middleware' => 'auth'
    ]
);

Route::resource('unit', 'UnitController', ['middleware' => 'auth']);
Route::resource('mardet', 'MardetController', ['middleware' => 'auth']);
Route::get(
    '/mardet/{unit}/deactivate',
    ['as'     => 'mardet.deactivate',
    'uses'   => 'MardetController@deactivate',
    'middleware' => 'auth'
    ]
);
Route::resource('anyunit', 'AnyUnitController', ['middleware' => 'auth']);
Route::get(
    '/anyunit/{unit}/deactivate',
    ['as'     => 'anyunit.deactivate',
    'uses'   => 'AnyUnitController@deactivate',
    'middleware' => 'auth'
    ]
);

Route::model('type', 'App\Type');
Route::resource('type', 'TypeController', ['middleware' => 'auth']);

// RemindersController
Route::get('password/remind', 'RemindersController@getRemind');
Route::post('password/remind', 'RemindersController@postRemind');
Route::get('password/reset/{token?}', 'RemindersController@getReset');
Route::post('password/reset', 'RemindersController@postReset');

Route::get(
    '/exam',
    ['as' => 'exam.index', 'uses' => 'ExamController@index', 'middleware' => 'auth']
);
Route::post('/exam/upload', [
  'as'     => 'exam.upload',
  'uses'   => 'ExamController@upload',
  'middleware' => 'auth'
]);
Route::post('/exam/update', [
  'as'     => 'exam.update',
  'uses'   => 'ExamController@update',
  'middleware' => 'auth'
]);
Route::get(
    '/exam/find/{user?}/{message?}',
    ['as' => 'exam.find', 'uses' => 'ExamController@find', 'middleware' => 'auth']
);
#Route::get('/exam/user/{user}', ['as' => 'exam.show', 'uses' => 'ExamController@showUser', 'middleware' => 'auth']);
Route::post(
    '/exam/store',
    ['as' => 'exam.store', 'uses' => 'ExamController@store', 'middleware' => 'auth']
);
Route::get('/exam/list', [
  'as'     => 'exam.list',
  'uses'   => 'ExamController@examList',
  'middleware' => 'auth'
]);
Route::get('/exam/create', [
  'as'     => 'exam.create',
  'uses'   => 'ExamController@create',
  'middleware' => 'auth'
]);

Route::model('exam', 'App\ExamList');
Route::get(
    '/exam/edit/{exam}',
    ['as' => 'exam.edit', 'uses' => 'ExamController@edit', 'middleware' => 'auth']
);
Route::post('/exam/updateExam', [
  'as'     => 'exam.updateExam',
  'uses'   => 'ExamController@updateExam',
  'middleware' => 'auth'
]);
Route::post(
    '/exam/user/delete',
    ['as' => 'exam.deleteUserExam', 'uses' => 'ExamController@delete']
);

Route::model('billet', 'App\Billet');
Route::resource('billet', 'BilletController', ['middleware' => 'auth']);

// IdController
Route::get('id/qrcode/{id}', 'IdController@getQrcode');
Route::get('id/card/{id}', 'IdController@getCard');
Route::get('id/bulk/{id}', 'IdController@getBulk');
Route::get('id/markbulk/{id}', 'IdController@getMarkbulk');
Route::get('id/mark/{id}', 'IdController@getMark');

Route::model('events', 'App\Events');
Route::resource('events', 'EventController', ['middleware' => 'auth']);
Route::get('/events/export/{events}', [
  'as'     => 'event.export',
  'uses'   => 'EventController@export',
  'middleware' => 'auth'
]);

Route::model('config', 'App\MedusaConfig');
Route::resource('config', 'ConfigController', ['middleware' => 'auth']);

// API calls

Route::get(
    '/api/branch',
    'ApiController@getBranchList'
); // Get a list of all the tRMN branches
Route::get(
    '/api/country',
    'ApiController@getCountries'
); // Get a list of Countries and Country Codes
Route::get(
    '/api/branch/{branchID}/grade',
    'ApiController@getGradesForBranch'
); // Get a list of pay grades for that branch
Route::get(
    '/api/chapter',
    'ApiController@getChapters'
); // Get a list of all the chapters
Route::get(
    '/api/chapter/{branchID}/{location}',
    'ApiController@getChaptersByBranch'
);
Route::get('/api/locations', 'ApiController@getChapterLocations');
Route::get('/api/holding', 'ApiController@getHoldingChapters');
Route::get('/api/fleet', 'ApiController@getFleets');
Route::get('/api/hq', 'ApiController@getHeadquarters');
Route::get('/api/bureau', 'ApiController@getBureaus');
Route::get('/api/su', 'ApiController@getSeparationUnits');
Route::get('/api/tf', 'ApiController@getTaskForces');
Route::get('/api/tg', 'ApiController@getTaskGroups');
Route::get('/api/squadron', 'ApiController@getSquadrons');
Route::get('/api/division', 'ApiController@getDivisions');
Route::get('/api/office', 'ApiController@getOffices');
Route::get('/api/academy', 'ApiController@getAcademies');
Route::get('/api/college', 'ApiController@getColleges');
Route::get('/api/center', 'ApiController@getCenters');
Route::get('/api/institute', 'ApiController@getInstitutes');
Route::get('/api/university', 'ApiController@getUniversities');
Route::get(
    '/api/branch/{branchID}/rate',
    'ApiController@getRatingsForBranch'
); // Get a list of all the ratings
Route::get(
    '/api/korder/{orderid}',
    'ApiController@getKnightClasses'
); // Get the classes for a Knightly Order
Route::post(
    '/api/photo',
    'ApiController@savePhoto',
    ['middleware' => 'auth']
); // File Photo upload
Route::get('/api/find/{query?}', [
  'as'     => 'user.find.api',
  'uses'   => 'ApiController@findMember',
  'middleware' => 'auth'
]); // search for a member
Route::get(
    '/api/exam',
    'ApiController@findExam',
    ['middleware' => 'auth']
); // search for an exam
Route::get(
    '/api/checkemail/{email}',
    'ApiController@checkAddress'
); // Check that an email address is available
Route::get(
    '/api/findchapter/{query?}',
    ['as' => 'chapter.find.api', 'uses' => 'ApiController@findChapter']
);
Route::get('/api/ribbonrack/{memberid}', ['as' => 'ribbonrack', 'uses' => 'ApiController@getRibbonRack']);
