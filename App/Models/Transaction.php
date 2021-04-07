<?php

namespace App\Models;

class Transaction {
    
    public static function select(int $id) {

        $connPdo = new \PDO(DBDRIVE.': host='.DBHOST.'; dbname='.DBNAME, DBUSER, DBPASS);

        $sql = 'SELECT id, id_pagador, id_beneficiario, valor, data
                FROM transacoes
                WHERE id = :id';

        $stmt = $connPdo->prepare($sql);

        $stmt->bindValue(':id', $id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            return $stmt->fetch(\PDO::FETCH_ASSOC);

        } else {

            throw new \Exception("Nenhuma transação encontrada");
        }
    }

    public static function selectAll() {

        $connPdo = new \PDO(DBDRIVE.': host='.DBHOST.'; dbname='.DBNAME, DBUSER, DBPASS);

        $sql = 'SELECT id, id_pagador, id_beneficiario, valor, data
                FROM transacoes';

        $stmt = $connPdo->prepare($sql);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } else {

            throw new \Exception("Nenhuma transação encontrada");

        }
    }

    public static function insert($data) {

        //Verifica se o pagador e o beneficiario são os mesmos usuários
        if($data->id_pagador != $data->id_beneficiario){

            $connPdo = new \PDO(DBDRIVE.': host='.DBHOST.'; dbname='.DBNAME, DBUSER, DBPASS);

            //Verifica se existe beneficiario
            $sql_verif_benef = 'SELECT id
                FROM carteiras WHERE id_usuario = :id_usuario_benf';

            $stmt_verif_benef = $connPdo->prepare($sql_verif_benef);

            $stmt_verif_benef->bindValue(':id_usuario_benf', $data->id_beneficiario);

            $stmt_verif_benef->execute();

            if ($stmt_verif_benef->rowCount() > 0) {

                //Verifica se existe pagador, é usuário comum e se possui valor disponivel no saldo
                $sql = 'SELECT c.id
                    FROM carteiras c 
                    JOIN usuarios u ON u.id = c.id_usuario
                    WHERE u.tipo = 1 AND c.valor >= :valor AND c.id_usuario = :id_pagador';

                $stmt = $connPdo->prepare($sql);

                $stmt->bindValue(':valor', $data->valor);
                $stmt->bindValue(':id_pagador', $data->id_pagador);

                $stmt->execute();

                if ($stmt->rowCount() > 0) {

                    //Atualiza valor do pagador
                    $sql_update_pagador = 'UPDATE carteiras
                    SET valor = valor - :valor WHERE id_usuario = :id_pagador';

                    $stmt_update_pagador = $connPdo->prepare($sql_update_pagador);

                    $stmt_update_pagador->bindValue(':valor', $data->valor);
                    $stmt_update_pagador->bindValue(':id_pagador', $data->id_pagador);

                    $stmt_update_pagador->execute();

                    //Atualiza valor do beneficiario
                    $sql_update_benef = 'UPDATE carteiras
                    SET valor = valor + :valor WHERE id_usuario = :id_beneficiario';

                    $stmt_update_benef = $connPdo->prepare($sql_update_benef);

                    $stmt_update_benef->bindValue(':valor', $data->valor);
                    $stmt_update_benef->bindValue(':id_beneficiario', $data->id_beneficiario);

                    $stmt_update_benef->execute();

                    //Insere transacao
                    $sql_insert = 'INSERT INTO transacoes (id_pagador, id_beneficiario, valor) 
                    VALUES (:id_pagador, :id_beneficiario, :valor)';

                    $stmt_insert = $connPdo->prepare($sql_insert);

                    $stmt_insert->bindValue(':id_pagador', $data->id_pagador);
                    $stmt_insert->bindValue(':id_beneficiario', $data->id_beneficiario);
                    $stmt_insert->bindValue(':valor', $data->valor);

                    $stmt_insert->execute();

                    return 'Transação realizada';

                } else {

                    throw new \Exception("Transação não autorizada");

                }
            
            }else{

                throw new \Exception("Beneficiário não encontrado");
                
            }
        
        }else{

            throw new \Exception("Pagador e beneficiário iguais");

        }
    }
}