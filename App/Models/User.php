<?php

namespace App\Models;

class User {
    
    public static function select(int $id) {

        $connPdo = new \PDO(DBDRIVE.': host='.DBHOST.'; dbname='.DBNAME, DBUSER, DBPASS);

        $sql = 'SELECT u.id, u.nome, u.num_inscricao, u.email, 
                IF(u.tipo = 1, "Comum", "Lojista") as tipo,
                c.valor as valor_carteira
                FROM usuarios u 
                JOIN carteiras c ON c.id_usuario = u.id
                WHERE u.id = :id';

        $stmt = $connPdo->prepare($sql);

        $stmt->bindValue(':id', $id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            return $stmt->fetch(\PDO::FETCH_ASSOC);

        } else {

            throw new \Exception("Nenhum usuário encontrado");
        }
    }

    public static function selectAll() {

        $connPdo = new \PDO(DBDRIVE.': host='.DBHOST.'; dbname='.DBNAME, DBUSER, DBPASS);

        $sql = 'SELECT u.id, u.nome, u.num_inscricao, u.email, 
                IF(u.tipo = 1, "Comum", "Lojista") as tipo,
                c.valor as valor_carteira
                FROM usuarios u 
                JOIN carteiras c ON c.id_usuario = u.id';

        $stmt = $connPdo->prepare($sql);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } else {

            throw new \Exception("Nenhum usuário encontrado");

        }
    }

    public static function insert($data) {

        $connPdo = new \PDO(DBDRIVE.': host='.DBHOST.'; dbname='.DBNAME, DBUSER, DBPASS);

        $sql_verif = 'SELECT id 
        FROM usuarios 
        WHERE num_inscricao = :num_inscricao OR email = :email';

        $stmt_verif = $connPdo->prepare($sql_verif);

        $stmt_verif->bindValue(':num_inscricao', $data->num_inscricao);
        $stmt_verif->bindValue(':email', $data->email);

        $stmt_verif->execute();

        if ($stmt_verif->rowCount() > 0) {

            throw new \Exception("E-mail ou CPF/CNPJ do usuário(a) já cadastrado");

        }else{

            $sql = 'INSERT INTO usuarios (nome, num_inscricao, email, senha, tipo) 
            VALUES (:nome, :num_inscricao, :email, :senha, :tipo)';

            $stmt = $connPdo->prepare($sql);

            $stmt->bindValue(':nome', $data->nome);
            $stmt->bindValue(':num_inscricao', $data->num_inscricao);
            $stmt->bindValue(':email', $data->email);
            $stmt->bindValue(':senha', $data->senha);
            $stmt->bindValue(':tipo', $data->tipo);

            $stmt->execute();

            $id_usuario_insert = $connPdo->lastInsertId();

            if ($stmt->rowCount() > 0) {
                
                $sql_carteira = 'INSERT INTO carteiras (id_usuario, valor) 
                VALUES (:id_usuario, 0.00)';

                $stmt_carteira = $connPdo->prepare($sql_carteira);

                $stmt_carteira->bindValue(':id_usuario', $id_usuario_insert);

                $stmt_carteira->execute();

                return 'Usuário(a) inserido com sucesso';

            } else {

                throw new \Exception("Falha ao inserir usuário(a)");

            }
        }
    }
}