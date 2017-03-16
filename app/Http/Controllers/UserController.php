<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers as Voy;
use App\User as User;

class UserController extends Voy\VoyagerBreadController
{
    //

    public function usersbycompany(Request $request, $id)
    {
        $userData 	= json_encode(User::where('company_id',$id)->get());

        echo json_encode($userData);
    }
}
