@extends('user.directorates._layout')

{{-- This view renders its own Declaration & Consent card (section 11) via the
     shared section partial, so the shared form body skips its declaration card
     to avoid a duplicate consent field. --}}
@section('directorate-declaration', '1')

@section('directorate-sections')
@include('user.states.sections.investigation')
@endsection
