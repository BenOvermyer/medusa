@extends('layout')

@section('pageTitle')
{{{  $user->getGreeting() }}} {{{ $user->first_name }}}{{{ isset($user->middle_name) ? ' ' . $user->middle_name : '' }}} {{{ $user->last_name }}}{{{ isset($user->suffix) ? ' ' . $user->suffix : '' }}}
@stop

@section('content')
<h2>{{{  $user->getGreeting() }}} {{{ $user->first_name }}}{{{ isset($user->middle_name) ? ' ' . $user->middle_name : '' }}} {{{ $user->last_name }}}{{{ isset($user->suffix) ? ' ' . $user->suffix : '' }}}</h2>
<table class="table table-striped">
<tbody>
    <tr>
        <td>Member ID:</td><td>{{{$user->member_id}}}</td>
    </tr>
    <tr>
        <td>Name:</td><td>{{{ $user->first_name }}}{{{ isset($user->middle_name) ? ' ' . $user->middle_name : '' }}} {{{ $user->last_name }}}{{{ isset($user->suffix) ? ' ' . $user->suffix : '' }}}</td>
    </tr>
    <tr>
        <td>Address:</td><td>{{{$user->address_1}}}@if($user->address_2)<br />{{{$user->address_2}}} @endif<br />{{{$user->city}}}, {{{$user->state_province}}} {{{$user->postal_code}}}<br />{{{$countries[$user->country]}}}</td>
    </tr>
    <tr>
        <td>Phone Number:</td><td>{{{ isset($user->phone_number) ? $user->phone_number : '' }}}</td>
    </tr>
    <tr>
        <td>Email Address:</td><td><a href="mailto:{{{$user->email_address}}}">{{{$user->email_address}}}</a></td>
    </tr>
    <tr>
        <td>Branch of Service:</td><td>{{{$branches[$user->branch]}}}</td>
    </tr>
    <tr>
        <td>Rank:</td><td>{{{ $user->rank['grade'] }}} / {{{$user->getGreeting()}}} as of {{{ $user->rank['date_of_rank'] }}}</td>
    </tr>
    @if(isset($user->rating['rate']))
    <tr>
        <td>Rating:</td><td>{{{$user->rating['rate']}}} / {{{$user->rating['description']}}}</td>
    </tr>
    @endif
    <tr>
        <td>Current Assignment:</td><td>@foreach($user->assignment as $assignment)<a href="{{ route('chapter.show' , [$assignment['chapter_id']]) }}">{{{$user->getPrimaryAssignmentName()}}}</a> on {{{$user->getPrimaryDateAssigned()}}}
        @if($assignment['billet']) as {{{$assignment['billet']}}}@endif @if($assignment['primary']) (Primary)@endif<br />@endforeach</td>
    </tr>
    <tr>
        <td colspan="2"><a href="{{route('user.index')}}">User Home</a> | <a href="{{route('user.edit', [$user->_id])}}">Edit User</a></td>
    </tr>
</tbody>
</table>
@stop
