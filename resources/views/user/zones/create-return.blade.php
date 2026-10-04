@extends('shared.single-return._layout')

@section('content')
    @php
        $isEditing = isset($editing) && $editing instanceof \App\Models\Application;
    @endphp

    @include('shared.single-return._form', [
        'title' => 'Zonal Return',
        'commandLabel' => 'Zone',
        'commandName' => $commandName,
        'formAction' => $isEditing ? route('user.zones.returns.update', ['applicationHash' => \App\Services\HashidService::encode($editing->id)]) : route('user.zones.returns.store'),
        'backRoute' => 'user.zones.dashboard',
        'editing' => $isEditing ? $editing : null,
    ])
@endsection
