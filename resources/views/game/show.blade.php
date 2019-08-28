@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @if ($eloquentUser->id === $eloquentGame->owner_id || $eloquentUser->id === $eloquentGame->competitor_id)
                <game-component id="{{ $eloquentGame->id }}"></game-component>
            @else
                <join-game-component id="{{ $eloquentGame->id }}"></join-game-component>
            @endif
        </div>
    </div>
@endsection
