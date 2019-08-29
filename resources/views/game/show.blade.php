@extends('layouts.game')

@section('game-content')
    @if ($eloquentUser->id === $eloquentGame->owner_id || $eloquentUser->id === $eloquentGame->competitor_id)
        <game-component id="{{ $eloquentGame->id }}"></game-component>
    @else
        <join-game-component id="{{ $eloquentGame->id }}"></join-game-component>
    @endif
@endsection
