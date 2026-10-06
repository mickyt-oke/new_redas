@extends('user.directorates._layout')

@section('directorate-tabs', '1')

@section('directorate-sections')
@include('user.states.sections.finance', ['financeIncludePreviewTab' => true])
@endsection
