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

        $user = \Auth::user();//retureaza userul curent

        return view( 'home' )->with( 'user', $user );
    }

    /**
     * Returns the user's current room
     *
     * @return mixed
     */
    public function getRoom() {

        $user = Auth::user();

        return Room::where( 'user1_id', $user->id )->orWhere( 'user2_id', $user->id )->first();
    }

    /**
     * Here the method that is called every time a user enters the page
     * If the user is not in a room we will create one for him.
     *
     * @return int
     */
    public function play() {

//              return view( 'play' );
        $user = Auth::user();

        // check for empty Rooms
        $room = Room::where( 'user1_id', $user->id )->first();

        if ( $room ) {
            if ( $room->user2_id )  //table_data=matricea x si 0
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

    /**
     * Show main page
     *
     */
    public function game() {
        return view( 'play' );
    }

    /**
     * Method to call every time the game needs to be updated.
     *
     * @return mixed|void
     */
    public function update() {

        $room = $this->getRoom();
        if( is_null($room) )
            return;

        // check who wins
        if ( $room->isFinished() ) {
            $room['winner'] = $this->winner();
        }

        return $room;
    }

    /**
     * Method to call every time a user adds a sign on the grid
     * Also here we check if the user wins the gameor not.
     *
     * @param Request $request
     *
     * @return array|int|void
     */
    public function addSign( Request $request ) {

        $row    = $request->get( 'row' );
        $column = $request->get( 'column' );
        $user   = Auth::user();

        // check for empty Rooms
        $room = $this->getRoom();
        if ( $room->turn == -1 ) //-1 = end of the game
            return 0;

        $tableData = $room->table_data;

        if ( !is_null( $tableData[ $row ][ $column ] ) )
            return;

        // stabilire semn jucator
        $sign = NULL;

        if ( $room->user1_id == $user->id && $room->turn == 1 ) {
            $sign       = 'X';
            $room->turn = 2;
        } elseif ( $room->user2_id == $user->id && $room->turn == 2 ) {
            $sign       = 'O';
            $room->turn = 1;
        }

        $tableData[ $row ][ $column ] = $sign;

        $room->table_data = $tableData;
        $room->save();

        $winner = $this->winner();
        if ( $winner == 1 ) {
            $user->won_games++;
            $user->save();
        }

        if ( $winner == 1 || $winner == -1 ) {
            $room->turn = -1;
            $room->save();
        }

        // RETURN NEW DATA TO THE BROWSER
        return [
            "sign"   => $sign,
            "winner" => $winner,
        ];
    }

    /**
     * Get the game's winner
     *
     * @return int
     */
    public function winner() {
        $room = $this->getRoom();

        $user = Auth::user();
        $data = $room->table_data;

        $sign = 'O';
        if ( $room->user1_id == $user->id )
            $sign = 'X';

        return $this->checkGame( $data, $sign );
    }

    private function checkGame( $data, $sign ) {

        $it    = new RecursiveIteratorIterator( new RecursiveArrayIterator( $data ) );
        $board = [ ];

        $fullBoard = 0;
        foreach ( $it as $v ) {
            if ( !is_null( $v ) ) {
                $fullBoard++;
            }
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

        // [ x, nul, o, nul, 0, 0, x, x, nul ];

        foreach ( $winRules as $rule ) {
            $win = 0;
            foreach ( $rule as $index ) {
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

        if ( $fullBoard == 9 )
            return -1;

        return 0;

    }
}
