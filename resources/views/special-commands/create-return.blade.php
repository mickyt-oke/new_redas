@extends('shared.single-return._layout')

@section('content')
    @php
        $isEditing = isset($editing) && $editing instanceof \App\Models\Application;
    @endphp

    @include('shared.single-return._form', [
        'title' => 'Special Command Return',
        'commandLabel' => 'Special Command',
        'commandName' => $commandName,
        'formAction' => $isEditing ? route('special-commands.returns.update', ['applicationHash' => \App\Services\HashidService::encode($editing->id)]) : route('special-commands.returns.store'),
        'backRoute' => 'special-commands.dashboard',
        'editing' => $isEditing ? $editing : null,
    ])
@endsection
