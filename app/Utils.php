<?php

declare(strict_types=1);

namespace App;

use DomainException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use InvalidArgumentException;
use stdClass;
use UnexpectedValueException;

/**
 * Common methods for the API
 */
trait Utils
{
    /**
     * @param array $response
     * @param int $status_code
     * @return string
     */
    public static function apiResponse(array $response, int $status_code = 200): string
    {
        if ($status_code) {
            http_response_code($status_code);
        }
        header('Content-Type: application/json; charset=utf-8');
        return json_encode($response);
    }

    /**
     * @param $jwt
     * @return bool
     */
    public static function addCookieJwt($jwt): bool
    {
        $cookie_options = [
            'expires' => time() + JWT_EXPIRES,
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => Utils::isSecure(),
            'httponly' => true,
            'samesite' => 'Strict'
        ];
        return setcookie('jwt', $jwt, $cookie_options);
    }

    /**
     * @return bool
     */
    public static function isSecure(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    }

    /**
     * @return stdClass|string
     */
    public static function jwtDecode()
    {
        $jwt_user = empty($_COOKIE['jwt']) ? null : $_COOKIE['jwt'];
        if ($jwt_user) {
            try {
                $decoded = JWT::decode($jwt_user, new Key(JWT_KEY, 'HS256'));
            } catch (InvalidArgumentException|ExpiredException|BeforeValidException|DomainException|SignatureInvalidException|UnexpectedValueException $exception) {
                $response['status'] = 'invalid_login';
                $response['message'] = $exception->getMessage();
                return Utils::apiResponse($response);
            }
            return $decoded;
        }
        $response['status'] = 'invalid_login';
        $response['message'] = 'Your credentials are invalid.';
        return Utils::apiResponse($response);
    }

    /**
     * @param array $payload
     * @return string
     */
    public static function jwtEncode(array $payload): string
    {
        return JWT::encode($payload, JWT_KEY, 'HS256');
    }

    /**
     * @param $email
     * @return bool
     */
    public static function validateEmail($email): bool
    {
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param $field
     * @param $size
     * @return bool
     */
    public static function validateString($field, $size): bool
    {
        return strlen($field) >= $size;
    }

    /**
     * @param $name
     * @return int
     */
    public static function getRoleIdByName($name): int
    {
        // this list should keep updated with "roles" table in database
        $roles['Administrator'] = 1;
        $roles['User'] = 2;
        return $roles[$name];
    }

    /**
     * @param $role_id
     * @return string
     */
    public static function getRoleNameById($role_id): string
    {
        // this list should keep updated with "roles" table in database
        $roles[1] = 'Administrator';
        $roles[2] = 'User';
        return $roles[$role_id];
    }
}