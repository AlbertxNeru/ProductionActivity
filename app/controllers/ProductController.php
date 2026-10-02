<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ProductController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->library('api');
        $this->call->model('Product_model');
        header('Content-Type: application/json; charset=utf-8');
    }

    public function index()
    {
        $this->api->require_method('GET');
        $this->api->require_jwt();

        $this->api->respond([
            'products' => $this->Product_model->get_all_products(),
        ]);
    }

    public function show($id)
    {
        $this->api->require_method('GET');
        $this->api->require_jwt();

        $product = $this->Product_model->get_product($id);
        if (!$product) {
            $this->api->respond_error('Product not found.', 404);
        }

        $this->api->respond(['product' => $product]);
    }

    public function store()
    {
        $this->api->require_method('POST');
        $this->api->require_jwt();

        $data = $this->normalize_product_input($this->api->body(), true);
        $id = $this->Product_model->create_product($data);
        $product = $this->Product_model->get_product($id);

        $this->api->respond([
            'message' => 'Product added successfully.',
            'product' => $product,
        ], 201);
    }

    public function update($id)
    {
        $this->api->require_jwt();

        $product = $this->Product_model->get_product($id);
        if (!$product) {
            $this->api->respond_error('Product not found.', 404);
        }

        $data = $this->normalize_product_input($this->api->body(), false);
        if (empty($data)) {
            $this->api->respond_error('No valid product fields were provided.', 422);
        }

        $this->Product_model->update_product($id, $data);

        $this->api->respond([
            'message' => 'Product updated successfully.',
            'product' => $this->Product_model->get_product($id),
        ]);
    }

    public function destroy($id)
    {
        $this->api->require_method('DELETE');
        $this->api->require_jwt();

        $product = $this->Product_model->get_product($id);
        if (!$product) {
            $this->api->respond_error('Product not found.', 404);
        }

        $this->Product_model->delete_product($id);

        $this->api->respond([
            'message' => 'Product deleted successfully.',
        ]);
    }

    private function normalize_product_input(array $body, $require_all)
    {
        $data = [];

        if ($require_all || array_key_exists('product_name', $body)) {
            $name = html_entity_decode(trim($body['product_name'] ?? ''), ENT_QUOTES, 'UTF-8');
            if ($name === '' || strlen($name) > 100) {
                $this->api->respond_error('Product name is required and must not exceed 100 characters.', 422);
            }
            $data['product_name'] = $name;
        }

        if ($require_all || array_key_exists('description', $body)) {
            $data['description'] = html_entity_decode(trim($body['description'] ?? ''), ENT_QUOTES, 'UTF-8');
        }

        if ($require_all || array_key_exists('price', $body)) {
            $price = $body['price'] ?? null;
            if (!is_numeric($price) || (float) $price < 0) {
                $this->api->respond_error('Price must be a number greater than or equal to 0.', 422);
            }
            $data['price'] = number_format((float) $price, 2, '.', '');
        }

        if ($require_all || array_key_exists('quantity', $body)) {
            $quantity = $body['quantity'] ?? null;
            if (filter_var($quantity, FILTER_VALIDATE_INT) === false || (int) $quantity < 0) {
                $this->api->respond_error('Quantity must be a whole number greater than or equal to 0.', 422);
            }
            $data['quantity'] = (int) $quantity;
        }

        return $data;
    }
}
