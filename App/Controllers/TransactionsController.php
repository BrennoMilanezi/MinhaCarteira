<?php

namespace App\Controllers;

use App\Models\Transaction;

class TransactionsController {

    public function get($id = null) {

        if(AuthController::checkAuth()){

            if ($id) {

                return Transaction::select($id);

            } else {

                return Transaction::selectAll();
                
            }

        }else{

            throw new \Exception("Não autenticado");
        
        }
    }

    public function post() {

        if(AuthController::checkAuth()){

            $data = json_decode(file_get_contents("php://input"));

            return Transaction::insert($data);
        
        }else{

            throw new \Exception("Não autenticado");
        
        }

    }
    
}