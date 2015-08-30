<?php

class UserController extends \BaseController
{

    /**
     * Display a listing of users
     *
     * @return Response
     */
    public function index()
    {
        $branches = ['RMN', 'RMMC', 'RMA', 'GSN', 'RHN', 'IAN', 'SFS', 'CIVIL', 'INTEL'];

        foreach($branches as $branch) {
            $users = User::where('active', '=', "1")
                ->where('registration_status', '=', 'Active')
                ->where('branch', '=', $branch)
                ->remember(30)
                ->get();

            $usersByBranch[$branch] = $users;
        }

        return View::make('user.index', ['users' => $usersByBranch, 'title' => 'Membership List']);
    }

    public function reviewApplications()
    {
        $users = User::where('active', '!=', "1")->where('registration_status', '=', 'Pending')->get();

        return View::make('user.review', ['users' => $users, 'title' => 'Approve Membership Applications']);
    }

    public function approveApplication(User $user)
    {
        $user->registration_status = 'Active';
        $user->registration_date = date('Y-m-d');

        switch ($user->branch) {
            case 'RMN':
            case 'GSN':
            case 'RHN':
            case 'IAN':
                $billet = 'Crewman';

                $dob = new DateTime($user->dob);
                $ageCutOff = new DateTime('now');
                $ageCutOff->modify('-18 year');

                if ($ageCutOff < $dob) {
                    $billet = 'Midshipman';
                }

                $assignment = $user->assignment;
                $assignment[0]['billet'] = $billet;
                $user->assignment = $assignment;

                break;
            case 'RMMC':
                $assignment = $user->assignment;
                $assignment[0]['billet'] = 'Marine';
                $user->assignment = $assignment;

                break;
            case 'RMA':
                $assignment = $user->assignment;
                $assignment[0]['billet'] = 'Soldier';
                $user->assignment = $assignment;

                break;
            default:
                $assignment = $user->assignment;
                $assignment[0]['billet'] = 'Civilian';
                $user->assignment = $assignment;
        }

        $rank = $user['rank'];
        $rank['date_of_rank'] = date('Y-m-d');
        $user->rank = $rank;
        $user->member_id = 'RMN' . User::getFirstAvailableMemberId();

        $user->save();

        // Get Chapter CO's email
        $user->co_email = Chapter::find($user->getPrimaryAssignmentId())->getCO()[0]->email_address;

        // Send welcome email
        Mail::send('emails.welcome', ['user' => $user], function ($message) use ($user) {
                $message->from('membership@trmn.org', 'TRMN Membership');

                $message->to($user->email_address)->bcc($user->co_email);

                $message->subject('TRMN Membership');
            }
        );


        return Redirect::route('user.review');
    }

    public function denyApplication(User $user)
    {
        $user->registration_status = 'Denied';
        $user->registration_date = date('Y-m-d');
        $user->save();

        return Redirect::route('user.review');
    }

    /**
     * Show the form for creating a new user
     *
     * @return Response
     */
    public function create()
    {
        return View::make(
            'user.create',
            [
                'user'      => new User,
                'countries' => $this->_getCountries(),
                'branches'  => Branch::getBranchList(),
                'grades'    => Grade::getGradesForBranch('RMN'),
                'ratings'   => Rating::getRatingsForBranch('RMN'),
                'chapters'  => ['0' => 'Select a Chapter'],
                'billets'   => ['0' => 'Select a Billet'] + Billet::getBillets(),
                'locations' => ['0' => 'Select a Location'] + Chapter::getChapterLocations()
            ]
        );
    }

    /**
     * Store a newly created user in storage.
     *
     * @return Response
     */
    public function store()
    {

        $rules = User::$rules;
        $errMsg = User::$error_message;

        $rules['password'] = 'required|min:8';
        $errMsg['password.required'] = 'You must set a password for the user';
        $errMsg['password.min'] = 'The password must be at least 8 characters long';
        $validator = Validator::make($data = Input::all(), $rules, $errMsg);

        if ($validator->fails()) {
            return Redirect::route('user.create')->withErrors($validator)->withInput();
        }

        // Massage the data a little bit.  First, build up the rank array

        $data['rank'] = [
            'grade'        => $data['display_rank'],
            'date_of_rank' => date(
                'Y-m-d',
                strtotime($data['dor'])
            )
        ];
        unset( $data['display_rank'], $data['dor'] );

        // Build up the member assignments

        $chapterName = Chapter::find($data['primary_assignment'])->chapter_name;

        $assignment[] = [
            'chapter_id'    => $data['primary_assignment'],
            'chapter_name'  => $chapterName,
            'date_assigned' => date('Y-m-d', strtotime($data['primary_date_assigned'])),
            'billet'        => $data['primary_billet'],
            'primary'       => true
        ];

        unset( $data['primary_assignment'], $data['primary_date_assigned'], $data['primary_billet'] );

        if (isset( $data['secondary_assignment'] ) === true && empty( $data['secondary_assignment'] ) === false) {
            $chapterName = Chapter::find($data['secondary_assignment'])->chapter_name;

            $assignment[] = [
                'chapter_id'    => $data['secondary_assignment'],
                'chapter_name'  => $chapterName,
                'date_assigned' => date('Y-m-d', strtotime($data['secondary_date_assigned'])),
                'billet'        => $data['secondary_billet'],
                'primary'       => false
            ];
        }

        unset( $data['secondary_assignment'], $data['secondary_date_assigned'], $data['secondary_billet'] );

        $data['assignment'] = $assignment;

        // Hash the password

        $data['password'] = Hash::make($data['password']);

        // Assign a member id

        $data['member_id'] = 'RMN' . User::getFirstAvailableMemberId($data['honorary']);

        if (isset( $data['honorary'] ) === true && $data['honorary'] === "1") {
            $data['member_id'] .= '-H';
            unset( $data['honorary'] );
        }

        // Set the active flag, application date and registration date

        $data['active'] = '1';

        $data['application_date'] = date('Y-m-d');
        $data['registration_date'] = date('Y-m-d');

        // Normalize State and Province
        $data['state_province'] = User::normalizeStateProvince($data['state_province']);

        // For future use

        $data['peerage_record'] = [];

        $data['awards'] = [];

        unset( $data['_token'], $data['password_confirmation'] );

        $user = User::create($data);

        // Until I figure out why mongo drops fields, I'm doing this hack!

        $u = User::find($user['_id']);

        foreach ($data as $key => $value) {
            $u->$key = $value;
        }

        $u->save();

        Event::fire('user.created', $user);

        return Redirect::route('user.index');
    }

    /**
     * Store a new applicant
     *
     * @return Response
     */
    public function apply()
    {
        $rules = [
            'email_address'      => 'required|email|unique:users',
            'first_name'         => 'required|min:2',
            'last_name'          => 'required|min:2',
            'address1'           => 'required|min:4',
            'city'               => 'required|min:2',
            'state_province'     => 'required|min:2',
            'postal_code'        => 'required|min:2',
            'country'            => 'required|min:2',
            'password'           => 'required|confirmed',
            'dob'                => 'required|date|date_format:Y-m-d',
            'branch'             => 'required',
            'primary_assignment' => 'required',
        ];

        $error_message = [
            'first_name.required' => 'Please enter your first name',
            'first_name.min' => 'Your first name must be at least 2 characters long',
            'last_name.required' => 'Please enter your last name',
            'last_name.min' => 'Your last name must be at least 2 characters long',
            'address1.required'              => 'Please enter your street address',
            'address1.min'                   => 'The street address must be at least 4 characters long',
            'city.required' => 'Please enter your city',
            'city.min' => 'Your city must be at least 2 characters long',
            'state_province.required'        => 'Please enter your state or province',
            'state_province.min'             => 'Your state or province must be at least 2 characters long',
            'postal_code.required' => 'Please enter your zip or postal code',
            'postal_code.min' => 'Your zip or postal code must be at least 2 characters long',
            'country.required' => 'Please enter your country',
            'country.min' => 'Your country must be at least 2 characters long',
            'dob.required' => 'Please enter your date of birth',
            'dob.date_format' => 'Please enter your date of birth in the YYYY-MM-DD format',
            'dob.date' => 'Please enter a valid date of birth',
            'primary_assignment.required'    => "Please select a chapter",
            'branch.required'                => "Please select the members branch",
            'email_address.unique'           => 'That email address is already in use',
            'email_address.required' => 'Please enter your email address',
        ];

        $validator = Validator::make($data = Input::all(), $rules, $error_message);

        if ($validator->fails()) {
            return Redirect::to('register')->withErrors($validator)->withInput();
        }

        // Check Captcha
        if (\Iorme\SimpleCaptcha\SimpleCaptcha::check(Input::get('captcha')) === false) {
            return Redirect::to('register')->withErrors(['message' => 'The code you entered was not correct'])->withInput();
        }

        $data['rank'] = ['grade' => 'E-1', 'date_of_rank' => date('Y-m-d')];

        switch ($data['rank']) {
            case "CIVIL":
            case "INTEL":
            case "SFS":
                $data['rank']['grade'] = 'C-1';
                $billet = "Civilian One";
            case "RMMC":
                $billet = "Marine";
            case "RMA":
                $billet = "Soldier";
            default:
                $billet = "Crewman";
        }

        if (isset( $data['primary_assignment'] ) === true && empty( $data['primary_assignment'] ) === false) {

            $chapterName = Chapter::find($data['primary_assignment'])->chapter_name;

            $data['assignment'][] = [
                'chapter_id'    => $data['primary_assignment'],
                'chapter_name'  => $chapterName,
                'date_assigned' => date('Y-m-d'),
                'billet'        => $billet,
                'primary'       => true
            ];
        } else {
            $data['assignment'][] = [];
        }

        unset( $data['primary_assignment'], $data['primary_date_assigned'], $data['primary_billet'], $data['captcha'] );

        // Hash the password

        $data['password'] = Hash::make($data['password']);

        // Normalize State and Province
        $data['state_province'] = User::normalizeStateProvince($data['state_province']);

        // For future use

        $data['peerage_record'] = [];
        $data['awards'] = [];
        $data['active'] = '0';
        $data['application_date'] = date('Y-m-d');
        $data['registration_status'] = 'Pending';

        unset( $data['_token'], $data['password_confirmation'] );

        $user = User::create($data);

        // Until I figure out why mongo drops fields, I'm doing this hack!

        $u = User::find($user['_id']);

        foreach ($data as $key => $value) {
            $u->$key = $value;
        }

        $u->save();

        Event::fire('user.registered', $user);

        return Redirect::route('login')->with(
            'message',
            'Thank you for joining The Royal Manticoran Navy: The Official Honor Harrington Fan Association.  Your application will be reviewed and you should receive an email in 48 to 72 hours once your account has been activated.'
        );
    }

    /**
     * Display the specified user.
     *
     * @param  User $user
     *
     * @return Response
     */
    public function show(User $user)
    {
        return View::make(
            'user.show',
            [
                'user'      => $user,
                'countries' => $this->_getCountries(),
                'branches'  => Branch::getBranchList()
            ]
        );
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  User $user
     *
     * @return Response
     */
    public function edit(User $user)
    {
        $greeting = $user->getGreetingArray();

        if (isset( $user->rating ) === true && empty( $user->rating ) === false && is_array($user->rating) === false) {
            $user->rating =
                [
                    'rate'        => $user->rating,
                    'description' => Rating::where('rate_code', '=', $user->rating)->get()[0]->rate['description']
                ];
        }

        $user->display_rank = $user->rank['grade'];

        if (isset( $user->rank['date_of_rank'] )) {
            $user->dor = $user->rank['date_of_rank'];
        } else {
            $user->dor = '';
        }

        $user->primary_assignment = $user->getPrimaryAssignmentId();
        $user->primary_billet = $user->getPrimaryBillet();
        $user->primary_date_assigned = $user->getPrimaryDateAssigned();

        $user->secondary_assignment = $user->getSecondaryAssignmentId();
        $user->secondary_billet = $user->getSecondaryBillet();
        $user->secondary_date_assigned = $user->getSecondaryDateAssigned();

        return View::make(
            'user.edit',
            [
                'user'      => $user,
                'greeting'  => $greeting,
                'countries' => $this->_getCountries(),
                'branches'  => Branch::getBranchList(),
                'grades'    => Grade::getGradesForBranch($user->branch),
                'ratings'   => Rating::getRatingsForBranch($user->branch),
                'chapters'  => Chapter::getChapters(),
                'billets'   => Billet::getBillets(),
                'locations' => ['0' => 'Select a Location'] + Chapter::getChapterLocations()
            ]
        );
    }

    /**
     * Update the specified user in storage.
     *
     * @param  User $user
     *
     * @return Response
     */
    public function update(User $user)
    {
        $validator = Validator::make($data = Input::all(), User::$updateRules, User::$error_message);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        // Massage the data a little bit.  First, build up the rank array

        $rank = [];

        if (isset( $data['display_rank'] ) === true && empty( $data['display_rank'] ) === false) {
            $data['rank'] =
                ['grade' => $data['display_rank'], 'date_of_rank' => date('Y-m-d', strtotime($data['dor']))];
            unset( $data['display_rank'], $data['dor'] );
        }

        // Build up the member assignments

        $chapterName = Chapter::find($data['primary_assignment'])->chapter_name;

        $assignment[] = [
            'chapter_id'    => $data['primary_assignment'],
            'chapter_name'  => $chapterName,
            'date_assigned' => date('Y-m-d', strtotime($data['primary_date_assigned'])),
            'billet'        => $data['primary_billet'],
            'primary'       => true
        ];

        unset( $data['primary_assignment'], $data['primary_date_assigned'], $data['primary_billet'] );

        if (isset( $data['secondary_assignment'] ) === true && empty( $data['secondary_assignment'] ) === false) {
            $chapterName = Chapter::find($data['secondary_assignment'])->chapter_name;

            $assignment[] = [
                'chapter_id'    => $data['secondary_assignment'],
                'chapter_name'  => $chapterName,
                'date_assigned' => date('Y-m-d', strtotime($data['secondary_date_assigned'])),
                'billet'        => $data['secondary_billet'],
                'primary'       => false
            ];

            unset( $data['secondary_assignment'], $data['secondary_date_assigned'], $data['secondary_billet'] );
        }

        $data['assignment'] = $assignment;

        if (isset( $data['password'] ) === true && empty( $data['password'] ) === false) {
            // Hash the password

            $data['password'] = Hash::make($data['password']);
        } else {
            unset( $data['password'] );
        }

        // Normalize State and Province
        $data['state_province'] = User::normalizeStateProvince($data['state_province']);

        $data['awards'] = [];

        $redirect = 'user.index';

        if ($data['reload_form'] === "yes") {
            $redirect = 'user.edit';
        }

        if (Auth::user()->member_id === $data['member_id'] && $data['reload_form'] === "no") {
            $redirect = 'home';
        }

        unset( $data['_method'], $data['_token'], $data['password_confirmation'], $data['reload_form'] );

        $user->update($data);

        return Redirect::route($redirect);
    }

    /**
     * Confirm that the user should be deleted.
     *
     * @param  User $user
     *
     * @return Response
     */
    public function confirmDelete(User $user)
    {
        return View::make('user.confirm-delete', ['user' => $user]);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  User $user
     *
     * @return Response
     */
    public function destroy(User $user)
    {
        User::destroy($user->_id);

        return Redirect::route('user.index');
    }

    public function register()
    {
        $fullCountryList = Countries::getList();
        $countries = [];

        foreach ($fullCountryList as $country) {
            $countries[$country['iso_3166_3']] = $country['name'];
        }

        asort($countries);

        $fullBranchList = Branch::all();
        $branches = [];

        foreach ($fullBranchList as $branch) {
            $branches[$branch['branch']] = $branch['branch_name'];
        }

        asort($branches);

        $viewData = [
            'user'      => new User,
            'countries' => $countries,
            'branches'  => $branches,
            'chapters'  => ['0' => 'Select a Chapter'],
            'locations' => ['0' => 'Select a Location'] + Chapter::getChapterLocations()
        ];

        return View::make('user.register', $viewData);
    }

    private function _getCountries()
    {
        $results = Countries::getList();
        $countries = [];

        foreach ($results as $country) {
            $countries[$country['iso_3166_3']] = $country['name'];
        }

        return $countries;
    }

}
