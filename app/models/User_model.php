<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class User_model extends Model
{
    protected $table = 'users';

    public function find_by_email($email)
    {
        $stmt = $this->db->raw(
            'SELECT id, username, email, password, role, is_active, created_at FROM users WHERE email = ? LIMIT 1',
            [$email]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function find_public_by_id($id)
    {
        $stmt = $this->db->raw(
            'SELECT id, username, email, role, is_active, created_at FROM users WHERE id = ? LIMIT 1',
            [(int) $id]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function username_or_email_exists($username, $email)
    {
        $stmt = $this->db->raw(
            'SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1',
            [$username, $email]
        );
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create_user($username, $email, $password_hash)
    {
        $this->db->raw(
            "INSERT INTO users (username, email, password, role, is_active) VALUES (?, ?, ?, 'user', 1)",
            [$username, $email, $password_hash]
        );

        return $this->find_by_email($email);
    }
}
