<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Product_model extends Model
{
    protected $table = 'products';

    public function get_all_products()
    {
        return $this->db->raw(
            'SELECT id, product_name, description, price, quantity, created_at FROM products ORDER BY id DESC'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_product($id)
    {
        $stmt = $this->db->raw(
            'SELECT id, product_name, description, price, quantity, created_at FROM products WHERE id = ? LIMIT 1',
            [(int) $id]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create_product(array $data)
    {
        $this->db->raw(
            'INSERT INTO products (product_name, description, price, quantity) VALUES (?, ?, ?, ?)',
            [
                $data['product_name'],
                $data['description'],
                $data['price'],
                $data['quantity'],
            ]
        );

        return $this->db->raw('SELECT LAST_INSERT_ID()')->fetchColumn();
    }

    public function update_product($id, array $data)
    {
        $allowed = ['product_name', 'description', 'price', 'quantity'];
        $sets = [];
        $values = [];

        foreach ($allowed as $column) {
            if (array_key_exists($column, $data)) {
                $sets[] = "{$column} = ?";
                $values[] = $data[$column];
            }
        }

        if (empty($sets)) {
            return 0;
        }

        $values[] = (int) $id;
        $stmt = $this->db->raw(
            'UPDATE products SET ' . implode(', ', $sets) . ' WHERE id = ?',
            $values
        );

        return $stmt->rowCount();
    }

    public function delete_product($id)
    {
        return $this->db->raw(
            'DELETE FROM products WHERE id = ?',
            [(int) $id]
        )->rowCount();
    }
}
