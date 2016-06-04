@if(!empty($user->assignment))
    <div class="NordItalic ninety padding-5">
        <a href="{{route('chapter.show',$user->getPrimaryAssignmentId())}}">
            {{$user->getPrimaryAssignmentName()}}
            <?php
            $chapterType = Chapter::getChapterType($user->getPrimaryAssignmentId());
            ?>
            @if($chapterType == "ship" || $chapterType == "station")
                {{$user->getPrimaryAssignmentDesignation()}}
            @endif
        </a>
        @if(!empty($showPrimary))
            <br />{{$user->getPrimaryBillet()}}
            <br /><span class="Incised901Bold">{{$user->member_id}}</span>
        @endif
        @if(in_array($user->getPrimaryAssignmentId(),explode(',', $user->duty_roster)) && $user->id == Auth::user()->id)
            @if(Chapter::find(Auth::user()->getPrimaryAssignmentId())->crewHasNewExams() === true)
                <br /><span class="fi-alert alert Incised901Light">One or more crew members have had new exams posted since your last login.<br />View your <a href="{{route('chapter.show',$user->getPrimaryAssignmentId())}}">roster</a> for more information</span>
            @endif
        @endif
    </div>
    <br />
    <div class="Incised901Black ninety">
        Additional Assignments:
    </div>

    <div class="Incised901Light whitesmoke">
        <?php
        $count = 0;
        foreach (['secondary', 'additional', 'extra'] as $position) {
            if (empty( $user->getAssignmentName($position) ) === false) {
                echo '<a href="' . route('chapter.show', $user->getAssignmentId($position)) . '">' .
                        $user->getAssignmentName($position) . '</a>';
                $count++;
            }

            if (empty( $user->getBillet($position) ) === false) {
                echo ', ' . $user->getBillet($position) . '<br>';
            }
        }

        if ($count === 0) {
            echo "None<br>";
        }

        ?>
    </div>
    <br />
@endif
@if(!empty($showPrimary))
    <div class="Incised901Light whitesmoke">{{$user->email_address}}</div>
@endif