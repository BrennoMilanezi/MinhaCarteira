<?php

namespace App\Controllers;

use App\Models\User;

class UsersController {

    public function get($id = null) {

        if(AuthController::checkAuth()){

            if ($id) {

                return User::select($id);

            } else {

                return User::selectAll();
                
            }

        }else{

            throw new \Exception("Não autenticado");
        
        }
    }

    public function post() {

        $data = json_decode(file_get_contents("php://input"));

        return User::insert($data);
    }

}