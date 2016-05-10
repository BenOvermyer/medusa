<?php

class ExamController extends \BaseController
{

    /**
     * Display a listing of the resource.
     * GET /exam
     *
     * @return Response
     */
    public function index()
    {
        if (($redirect = $this->checkPermissions('UPLOAD_EXAMS')) !== true) {
            return $redirect;
        }

        return View::make('exams.index', ['messages' => Message::where('source', '=', 'import_grades')->orderBy('created_at', 'asc')->get()]);
    }

    public function find($user = null)
    {
        if (($redirect = $this->checkPermissions(['ADD_GRADE', 'EDIT_GRADE'])) !== true) {
            return $redirect;
        }

        return View::make('exams.find', ['user' => $user]);
    }
    
    publi function edit()
    {
        
        if (($redirect = $this->checkPermissions(['EDIT_GRADE'])) !== true) {
            return $redirect;
        }
        
        
        
    }

    public function update()
    {
        if (($redirect = $this->checkPermissions(['ADD_GRADE', 'EDIT_GRADE'])) !== true) {
            return $redirect;
        }

        unset($message);

        // Validation rules

        $rules = [
            'member_id' => 'required|size:11',
            'exam' => 'required|is_grader',
            'date' => 'required|date|date_format:Y-m-d'
        ];

        $errorMessages = [
            'member_id.required' => "The member's RMN number is required",
            'exam.required' => 'The Exam ID is required',
            'date.required' => 'You must provide the date the exam was graded',
            'date_format' => 'Dates must be formated Y-M-D',
            'score.in' => 'Score must be PASS, BETA or CREATE',
            'score.min' => 'Score can not be less than 70',
            'score.max' => 'Score can not be more than 100',
        ];

        $data = Input::all();

        // Do we have a numeric score?

        if (preg_match('/^\d{2,3}%?/', $data['score']) === 0) {
            // Not a numeric score, add rule for valid alpha grades and slam the score to upper case just in case
            $rules['score'] = 'required|in:PASS,BETA,CREATE';
            $data['score']  = strtoupper($data['score']);
        } else {
            $rules['score'] = 'required|integer|min:70|max:100';
        }

        $validator = Validator::make($data, $rules, $errorMessages);

        if ($validator->fails()) {
            return Redirect::to(URL::previous())->withErrors($validator)->withInput();
        }

        if (preg_match('/^\d*$/', trim($data['score'])) === 1) {
            $data['score'] = trim($data['score']) . '%';
        } else {
            $data['score'] = strtoupper(trim($data['score']));
        }

        // Get the user's exam record

        $record = Exam::where('member_id', '=', $data['member_id'])->first();

        // Get the user record as well

        $member = User::where('member_id', '=', $data['member_id'])->first();

        // This might be an update, so check and see if the exam exists in the exams array

        if (array_key_exists($data['exam'], $record->exams) === true) {
            // This is an edit, update it

            $record[$data['exam']] = [
                'score' => $data['score'],
                'date' => $data['date'],
                'entered_by' => Auth::user()->id,
                'date_entered' => date('Y-m-d'),
            ];

            $message = $data['exam'] . ' updated in academy coursework for ' . $member->first_name . ' ' .
                       (!empty($member->middle_name) ? $member->middle_name . ' ' : '') . $member->last_name .
                       (!empty($member->suffix) ? ' ' . $member->suffix : '') .
                       ' (' . $member->member_id . ')';

        } else {

            $exams = $record->exams;

            // Massage the score, make sure that it's reasonably formated

            $exams[$data['exam']] = [
                'score' => $data['score'],
                'date' => $data['date'],
                'entered_by' => Auth::user()->id,
                'date_entered' => date('Y-m-d'),
            ];

            $record->exams = $exams;

            $message = $data['exam'] . ' added to academy coursework for ' . $member->first_name . ' ' .
                       (!empty($member->middle_name) ? $member->middle_name . ' ' : '') . $member->last_name .
                       (!empty($member->suffix) ? ' ' . $member->suffix : '') .
                       ' (' . $member->member_id . ')';

        }

        $record->save();

        return Redirect::route('exam.find')->with('message', $message);

    }

    public function upload()
    {
        if (($redirect = $this->checkPermissions('UPLOAD_EXAMS')) !== true) {
            return $redirect;
        }

        if (Input::file('file')->isValid() === true) {

            // Delete any records in the messages collection, this is a fresh run
            Message::where('source', '=', 'import_grades')->delete();

            $ext = Input::file('file')->getClientOriginalExtension();

            if ($ext != 'xlsx' && $ext != 'ods') {
                return Redirect::route('exam.index')->with('message', 'Only .xlsx files will be accepted');
            }

            Input::file('file')->move(app_path() . '/database', 'TRMN Exam grading spreadsheet.xlsx');

            $max_execution_time = ini_get('max_execution_time');
            set_time_limit(0);

            Artisan::call('import:grades');

            Cache::flush();

            if (is_null($max_execution_time) === false) {
                set_time_limit($max_execution_time);
            } else {
                set_time_limit(30);
            }

            return Redirect::route('exam.index')->with('message', 'Exam grades uploaded');
        }
    }

    public function store () {
        if (($redirect = $this->checkPermissions('EDIT_GRADE')) !== true) {
            return $redirect;
        }
        // updated to use the correct model.  Don't forget to actually add the rules
        $validator = Validator::make($data = Input::all(), ExamList::$rules);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        // updated with the correct collection name.  make sure the field names in the form match up with the names in
        // the model
        $this->writeAuditTrail(
             Auth::user()->id,
            'create',
            'exam_list',
            null,
            json_encode($data),
            'ExamController@store'
        );

        // updated to use the correct model
        ExamList::create($data);

        // This should probably change, exam/index.blade.php is for the soon to be deprecated file upload.  Once the final
        // excel upoad is done, we could probably re-purpose it.  I also updated the directory name from exam to exams.
        return Redirect::route('exams.index');
	}

}