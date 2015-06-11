<?php

use Jenssegers\Mongodb\Model as Eloquent;

class Chapter extends Eloquent
{

    protected $fillable = [ 'chapter_name', 'chapter_type', 'hull_number', 'assigned_to', 'ship_class', 'commission_date', 'decommission_date' ];

    public static $rules = [
        'chapter_name' => 'required|min:6|unique:chapters',
        'chapter_type' => 'required',
    ];

    public static $updateRules = [
        'chapter_name' => 'required|min:6',
        'chapter_type' => 'required'
    ];


    static function getChapters($branch='')
    {
        $holdingChapters = [ 'SS-001', 'SS-002', 'LP', 'HC' ];

        if (empty($branch) === false) {
            $results = Chapter::where('branch', '=', strtoupper($branch))->get();
        } else {
            $results = Chapter::all();
        }

        if (count($results) === 0) {
            $results = Chapter::where('hull_number','=','SS-001')->get();
        }
        $chapters = [ ];

        foreach ( $results as $chapter ) {
            $chapters[ $chapter->_id ] = $chapter->chapter_name;


            if ( isset( $chapter->hull_number ) === true && empty( $chapter->hull_number ) === false ) {
                if ( in_array( $chapter->hull_number, $holdingChapters ) === true ) {
                    $chapters[$chapter->_id] .= ' (Holding Chapter)';
                } else {
                    $co = Chapter::find($chapter->_id)->getCO();
                    //die("<pre>" . print_r($co, true));
                    $append = '';
                    if (empty($co[0]) === false && empty($co[0]['city']) === false && empty($co[0]['state_province']) == false) {
                        $append = ' (' . $co[0]['city'] . ', ' . $co[0]['state_province'] . ')';
                    }
                    $chapters[ $chapter->_id ] .= $append;
                }
            }
        }

        asort( $chapters, SORT_NATURAL );

        $chapters = [ '' => "Select a Chapter" ] + $chapters;

        return $chapters;
    }

    /**
     * Get all users/members assigned to a specific chapter excluding the command crew
     *
     * @param $chapterId
     * @return mixed
     */
    public function getCrew() {
        $users = User::where( 'assignment.chapter_id', '=', (string)$this->_id )->whereNotIn( 'assignment.billet', [ 'Commanding Officer', 'Executive Officer', 'Bosun' ])->get();

        return $users;
    }

    /**
     * Get all users/members assigned to a specific chapter, including the command crew
     *
     * @param $chapterId
     * @return mixed
     */
    public function getAllCrew( $chapterId )
    {
        return User::where( 'assignment.chapter_id', '=', $chapterId )->get();
    }

    public function getCO() {
        $users = User::where( 'assignment.chapter_id', '=', (string)$this->_id )->where( 'assignment.billet', '=', 'Commanding Officer' )->get();
        return $users;
    }

    public function getXO() {
        $users = User::where( 'assignment.chapter_id', '=', (string)$this->_id )->where( 'assignment.billet', '=', 'Executive Officer' )->get();

        return $users;
    }

    public function getBosun() {
        $users = User::where( 'assignment.chapter_id', '=', (string)$this->_id )->where( 'assignment.billet', '=', 'Bosun' )->get();

        return $users;
    }

    /**
     * Get the command crew for a chapter
     *
     * @param $chapterId
     * @return mixed
     */
    public function getCommandCrew() {
        $users['CO'] = $this->getCO();
        $users['XO'] = $this->getXO();
        $users['BOSUN'] = $this->getBosun();

        return $users;
    }



}
