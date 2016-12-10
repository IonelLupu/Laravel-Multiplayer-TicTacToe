<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Room;
use Auth;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware( 'auth' );
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $user = \Auth::user();

        return view( 'home' )->with( 'user', $user );
    }

    public function getRoom() {

        $user = Auth::user();

        return Room::where( 'user1_id', $user->id )->orWhere( 'user2_id', $user->id )->first();
    }

    public function play() {

              return view( 'play' );
        $user = Auth::user();

        // check for empty Rooms
        $room = Room::where( 'user1_id', $user->id )->first();

        if ( $room ) {
            if ( $room->user2_id )
                return $room->table_data; //"Inceper joc"
            else
                return 0; // "Asteapta"
        } else {
            $room = Room::where( 'user2_id', $user->id )->first();

            if ( $room ) {
                return $room->table_data; //"Incepe joc"
            } else {
                $room = Room::whereNull( 'user2_id' )->first();
                if ( $room ) {
                    $room->user2_id = $user->id;
                    $room->save();

                    return $room->table_data; //"Inceper joc"
                } else {
                    $room             = new Room();
                    $room->user1_id   = $user->id;
                    $room->user2_id   = NULL;
                    $room->turn       = 1;
                    $room->table_data = [ [ NULL, NULL, NULL ], [ NULL, NULL, NULL ], [ NULL, NULL, NULL ] ];
                    $room->save();

                    return 0; // "Asteapta"
                }
            }
        }

    }

    public function game() {
        return view( 'play' );
    }

    public function addSign( Request $request ) {

        $i    = $request->get( 'i' );
        $j    = $request->get( 'j' );
        $user = Auth::user();

        // check for empty Rooms
        $room = $this->getRoom();
        if ( $room->turn == -1 )
            return 0;

        $data = $room->table_data;

        if( !is_null($data[ $i ][ $j ]) )
            return;

        $sign = NULL;

        if ( $room->user1_id == $user->id && $room->turn == 1 ) {
            $room->turn = 2;
            $sign       = 'X';
        } elseif ( $room->user2_id == $user->id && $room->turn == 2 ) {
            $room->turn = 1;
            $sign       = 'O';
        }


        if ( !is_null( $sign ) )
            $data[ $i ][ $j ] = $sign;

        $room->table_data = $data;

        $room->save();
        if ( $this->winner() ) {

            $user->won_games ++;
            $user->save();

            $room->turn = -1;
            $room->save();
        }

        return [
            "sign"   => $sign,
            "winner" => $this->winner(),
        ];
    }

    public function winner() {
        $room = $this->getRoom();

        $user = Auth::user();
        $data = $room->table_data;

        $sign = 'O';
        if ( $room->user1_id == $user->id )
            $sign = 'X';

        return $this->checkGame( $data, $sign );
    }

    public function update() {

        $room = $this->getRoom();

        $data = $room->toArray();

        // check who wins
        if ( $room->isFinished() ) {
            $data['winner'] = $this->winner();
        }

        return $data;
    }

    private function checkGame( $data, $sign ) {

        $it    = new RecursiveIteratorIterator( new RecursiveArrayIterator( $data ) );
        $board = [ ];

        foreach ( $it as $v ) {
            array_push( $board, $v );
        }

        $winRules = [
            [ 0, 1, 2 ],
            [ 3, 4, 5 ],
            [ 6, 7, 8 ],

            [ 0, 3, 6 ],
            [ 1, 4, 7 ],
            [ 2, 5, 8 ],

            [ 0, 4, 8 ],
            [ 2, 4, 6 ]
        ];

        foreach ( $winRules as $rule ) {
            $win = 0;
            foreach ( $rule as $k => $index ) {
                if ( $board[ $index ] == $sign )
                    $win++;
                if ( $win == count( $rule ) ) {
                    return 1;
                }
            }
        }

        $room = $this->getRoom();
        if ( $room->isFinished() )
            $room->delete();

        return 0;

    }
}
