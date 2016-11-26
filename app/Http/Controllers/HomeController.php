<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Room;
use Auth;

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

    public function play() {

        $user = Auth::user();

        // check for empty Rooms
        $room = Room::where( 'user1_id', $user->id )->first();

        if( $room ){
            if( $room->user2_id )
                return "Inceper joc";
            else
                return "Asteapta";
        }else{
            $room = Room::where( 'user2_id', $user->id )->first();

            if( $room ){
                return "Incepe joc";
            }else{
                $room = Room::whereNull( 'user2_id')->first();
                if( $room ){
                    $room->user2_id = $user->id;
                    $room->save();
                    return "Incepe joc";
                }else{
                    $room             = new Room();
                    $room->user1_id   = $user->id;
                    $room->user2_id   = NULL;
                    $room->turn       = 1;
                    $room->table_data = "";
                    $room->save();

                    return "Asteapta";
                }
            }
        }

        return "error";
        if ( $room ) {
            if ( $room->user1_id == $user->id ) {
                if ( !is_null( $room->user2_id ) ) {
                    return "Reincepe joc";
                } else {
                    return "Asteapta jucator. camera deja creata";
                }
            }

            if ( $room->user2_id == $user->id ) {
                return "Reincepe joc";
            }

        } else {
            // create new room
            $room             = new Room();
            $room->user1_id   = $user->id;
            $room->user2_id   = NULL;
            $room->turn       = 1;
            $room->table_data = "";

            $room->save();

            return "Asteapta jucator. camera tocmai a fost creata";
        }

        return ( $room );

        return view( 'play' );
    }
}
