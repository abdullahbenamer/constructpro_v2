<?php
require_once '../app/Core/Model.php';
class ProjectDocumentModel extends Model
{
    public function getByProject($project_id)
    {
        return $this->db->query(
            "SELECT d.*,
                    u.full_name AS uploaded_by_name
             FROM project_documents d
             LEFT JOIN users u
                ON u.id = d.uploaded_by
             WHERE d.project_id = ?
             ORDER BY d.uploaded_at DESC",
            [$project_id]
        )->fetchAll();
    }

    public function getById($id)
    {
        return $this->db->query(
            "SELECT *
             FROM project_documents
             WHERE id = ?",
            [$id]
        )->fetch();
    }

    public function create($data)
    {
       $this->db->query(
    "INSERT INTO project_documents
    (
        project_id,
        category,
        title,
        description,
        document_date,
        original_name,
        stored_name,
        file_type,
        file_size,
        uploaded_by
    )
    VALUES
    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
    [
        $data['project_id'],
        $data['category'],
        $data['title'],
        $data['description'],
        $data['document_date'],
        $data['original_name'],
        $data['stored_name'],
        $data['file_type'],
        $data['file_size'],
        $data['uploaded_by']
    ]
);

return $this->db->lastInsertId();
    }

    public function delete($id)
{
    return $this->db->query(
        "DELETE FROM project_documents WHERE id=?",
        [$id]
    )->rowCount() > 0;
}
}