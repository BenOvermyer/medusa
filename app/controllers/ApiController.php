<?php

class ApiController extends BaseController
{

    public function getBranchList()
    {
        return Response::json(Branch::getBranchList());
    }

    public function getCountries()
    {
        $results = Countries::getList();
        $countries = array();

        foreach ($results as $country) {
            $countries[$country['iso_3166_3']] = $country['name'];
        }

        asort($countries);

        $countries = array('' => 'Select a Country') + $countries;

        return Response::json($countries);
    }

    public function getGradesForBranch($branchID)
    {
        return Response::json(Grade::getGradesForBranch($branchID));
    }

    public function getChapters()
    {
        return Response::json(Chapter::getChapters());
    }

    public function getChaptersByBranch($branchID, $location)
    {
        return Response::json(Chapter::getChapters($branchID, $location));
    }

    public function getRatingsForBranch($branchID)
    {
        return Response::json(Rating::getRatingsForBranch($branchID));
    }

    public function getChapterLocations()
    {
        return Response::json(Chapter::getChapterLocations());
    }

    public function getHoldingChapters()
    {
        return Response::json(Chapter::getHoldingChapters());
    }

    public function getFleets()
    {
        return Response::json(Chapter::getChaptersByType('fleet'));
    }

    public function getHeadquarters()
    {
        return Response::json(Chapter::getChaptersByType('headquarters'));
    }

    public function getBureaus()
    {
        return Response::json(Chapter::getChaptersByType('bureau'));
    }

    public function getSeparationUnits()
    {
        return Response::json(Chapter::getChaptersByType('SU'));
    }

    public function getTaskForces()
    {
        return Response::json(Chapter::getChaptersByType('task_force'));
    }

    public function getTaskGroups()
    {
        return Response::json(Chapter::getChaptersByType('task_group'));
    }

    public function getSquadrons()
    {
        return Response::json(Chapter::getChaptersByType('squadron'));
    }

    public function getDivisions()
    {
        return Response::json(Chapter::getChaptersByType('division'));
    }

    public function getOffices()
    {
        return Response::json(Chapter::getChaptersByType('office'));
    }

    public function getAcademies()
    {
        return Response::json(Chapter::getChaptersByType('academy'));
    }

    public function getSchools()
    {
        return Response::json(Chapter::getChaptersByType('school'));
    }

    public function savePhoto()
    {
        if (Input::file('file')->isValid() === true) {
            $res = User::where('member_id', '=', Input::get('member_id'))->get();

            if (count($res) === 1 && $res[0]->member_id == Input::get('member_id')) {

                $user = $res[0];
                $ext = Input::file('file')->getClientOriginalExtension();
                $fileName = $user->member_id . '.' . $ext;

                Input::file('file')->move(public_path() . '/images', $fileName);

                // File uploaded, add filename to user record
                $user->filePhoto = '/images/' . $fileName;

                $this->writeAuditTrail(
                    (string)Auth::user()->_id,
                    'update',
                    'users',
                    (string)$user->_id,
                    $user->toJson(),
                    'ApiController@savePhoto'
                );

                $user->save();
            }
        }
    }

    public function getKnightClasses($orderId)
    {
        $classes = [];

        if(empty($order = Korders::find($orderId)) === false) {
            foreach ($order->classes as $class) {
                $classes[$class['postnominal']] = $class['class'];
            }
        }

        return Response::json($classes);
    }
}
