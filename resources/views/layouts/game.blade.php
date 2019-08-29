@extends('layouts.app')

@section('content')
    <div class="col-md-10">
        <div class="card">
            <div class="card-body">
                @yield('game-content')
            </div>
        </div>
    </div>
@endsection
