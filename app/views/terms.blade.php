@extends('layout')

@section('pageTitle')
    Terms of Service
@stop

@section('content')
    @include('partials.tos')
    {{ Form::open(['route' => 'tos', 'method' => 'post']) }}
    {{ Form::hidden('id', $user->id) }}
    <div>
    {{ Form::checkbox('tos',1, false, ['class' => 'switcher']) }} I have read and agree to the Terms of Service
    </div>
    <div>
    <a class="button"
       href="{{ route('signout') }}">Cancel</a> {{ Form::submit('Save', [ 'class' => 'button' ] ) }}
    </div>
    {{ Form::close() }}
@stop
@section ('scriptFooter')
<script type="application/javascript">
    $('.switcher').rcSwitcher({

    // reverse: true,
    inputs: false,
    // width: 70,
    // height: 24,
    // blobOffset: 2,
    onText: 'Yes',
    offText: 'No',
    theme: 'stoplight',
    // autoFontSize: true,

    });
</script>
@stop