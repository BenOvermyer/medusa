<?php

class UnitController extends \BaseController
{

    private $chapterTypes = ['bivouac', 'barracks', 'outpost', 'fort', 'planetary', 'theater'];
    private $permissions = ['ADD' => 'ADD_UNIT', 'EDIT' => 'EDIT_UNIT', 'DELETE' => 'DELETE_UNIT'];
    private $auditName = 'UnitController';
    private $select = 'Select a Command/Unit Type';
    private $title = 'Command or Unit';

    use Medusa\Echelons\MedusaEchelons;

    private function getCommands()
    {
        $chapters = [];

        foreach ($this->chapterTypes as $type) {
            $chapters = array_merge($chapters, \Chapter::getChaptersByType($type));
        }

        asort($chapters);

        return $chapters;
    }

}