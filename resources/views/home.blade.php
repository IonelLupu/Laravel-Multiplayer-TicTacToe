@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    You won {{ $user->won_games }} games
                </div>


            </div>

            <div class="text-center">
                <a class="btn btn-large btn-info" href="/play">START NEW GAME</a>
            </div>

            <hr>
        </div>
    </div>
</div>
@endsection
