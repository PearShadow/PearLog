<?php

namespace Leantime\Domain\Tickets\Repositories;

use Leantime\Core\Db\Db;

class CustomTemplatesRepository
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function getAll($userId)
    {
        $sql = "SELECT * FROM zp_ticket_custom_templates 
                WHERE userId = :userId 
                ORDER BY title";

        $stmn = $this->db->database->prepare($sql);
        $stmn->bindValue(':userId', $userId, \PDO::PARAM_INT);
        $stmn->execute();

        return $stmn->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create($title, $content, $userId)
    {
        $sql = "INSERT INTO zp_ticket_custom_templates (title, content, userId) 
                VALUES (:title, :content, :userId)";

        $stmn = $this->db->database->prepare($sql);
        $stmn->bindValue(':title', $title, \PDO::PARAM_STR);
        $stmn->bindValue(':content', $content, \PDO::PARAM_STR);
        $stmn->bindValue(':userId', $userId, \PDO::PARAM_INT);

        return $stmn->execute();
    }

    public function delete($id, $userId)
    {
        $sql = "DELETE FROM zp_ticket_custom_templates 
                WHERE id = :id AND userId = :userId";

        $stmn = $this->db->database->prepare($sql);
        $stmn->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmn->bindValue(':userId', $userId, \PDO::PARAM_INT);

        return $stmn->execute();
    }

    public function getOne($id, $userId)
    {
        $sql = "SELECT * FROM zp_ticket_custom_templates 
                WHERE id = :id AND userId = :userId";

        $stmn = $this->db->database->prepare($sql);
        $stmn->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmn->bindValue(':userId', $userId, \PDO::PARAM_INT);
        $stmn->execute();

        return $stmn->fetch(\PDO::FETCH_ASSOC);
    }
}
