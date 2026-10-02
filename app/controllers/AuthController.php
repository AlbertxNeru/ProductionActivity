<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->database();
        $this->call->library('api');
        $this->call->model('User_model');
        header('Content-Type: application/json; charset=utf-8');
    }

    public function register()
    {
        $this->api->require_method('POST');
        $body = $this->api->body();

        $username = html_entity_decode(trim($body['username'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email = strtolower(trim($body['email'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        if (strlen($username) < 3 || strlen($username) > 100) {
            $this->api->respond_error('Username must be between 3 and 100 characters.', 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->api->respond_error('Please enter a valid email address.', 422);
        }

        if (strlen($password) < 6) {
            $this->api->respond_error('Password must be at least 6 characters.', 422);
        }

        if ($this->User_model->username_or_email_exists($username, $email)) {
            $this->api->respond_error('Username or email is already registered.', 409);
        }

        $user = $this->User_model->create_user(
            $username,
            $email,
            password_hash($password, PASSWORD_DEFAULT)
        );

        if (!$user) {
            $this->api->respond_error('Unable to create the account.', 500);
        }

        $tokens = $this->api->issue_tokens([
            'id' => $user['id'],
            'role' => $user['role'],
        ]);

        unset($user['password']);

        $this->api->respond([
            'message' => 'Account created successfully.',
            'user' => $user,
            'tokens' => $tokens,
        ], 201);
    }

    public function login()
    {
        $this->api->require_method('POST');
        $body = $this->api->body();

        $email = strtolower(trim($body['email'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->api->respond_error('Email and password are required.', 422);
        }

        $user = $this->User_model->find_by_email($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->api->respond_error('Invalid email or password.', 401);
        }

        if (!(int) $user['is_active']) {
            $this->api->respond_error('This account is inactive.', 403);
        }

        $tokens = $this->api->issue_tokens([
            'id' => $user['id'],
            'role' => $user['role'],
        ]);

        unset($user['password']);

        $this->api->respond([
            'message' => 'Login successful.',
            'user' => $user,
            'tokens' => $tokens,
        ]);
    }

    public function refresh()
    {
        $this->api->require_method('POST');
        $body = $this->api->body();
        $refresh_token = (string) ($body['refresh_token'] ?? '');

        if ($refresh_token === '') {
            $this->api->respond_error('Refresh token is required.', 422);
        }

        $this->api->refresh_access_token($refresh_token);
    }

    public function logout()
    {
        $this->api->require_method('POST');
        $body = $this->api->body();
        $refresh_token = (string) ($body['refresh_token'] ?? '');

        if ($refresh_token !== '') {
            $this->api->revoke_refresh_token($refresh_token);
        }

        $this->api->respond(['message' => 'Logout successful.']);
    }

    public function me()
    {
        $this->api->require_method('GET');
        $auth = $this->api->require_jwt();
        $user = $this->User_model->find_public_by_id($auth['sub']);

        if (!$user) {
            $this->api->respond_error('User not found.', 404);
        }

        $this->api->respond(['user' => $user]);
    }
}
