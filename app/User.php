<?php

declare(strict_types=1);

namespace App;

use PDO;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;
use stdClass;

/**
 * Class for PHP task - User API
 */
class User
{
    use Utils;

    /**
     * Check user credentials from JWT cookie
     */
    public function getUser(): array
    {
        $jwt_user = Utils::jwtDecode();

        if ($jwt_user->global_mfa_enabled === 'true' && $jwt_user->mfa_verified === 'false') {
            $response['status'] = 'invalid_login';
            $response['message'] = 'Your credentials are invalid.';
            return $response;
        }

        if ($jwt_user->id) {
            $db_user = $this->selectById($jwt_user->id);
            if ($db_user) {
                $response['status'] = 'success';
                $response['id'] = $db_user->id;
                $response['email'] = $db_user->email;
                $response['full_name'] = $db_user->full_name;
                $user_roles = $this->rolesById($db_user->id);
                $roles_output = [];
                foreach ($user_roles as $roles) {
                    $roles_output[] = Utils::getRoleNameById($roles['role_id']);
                }
                $response['roles'] = $roles_output;

                return $response;
            }
        }

        $response['status'] = 'invalid_login';
        $response['message'] = 'Your credentials are invalid.';
        return $response;
    }

    /**
     * Get a single user reading the JWT cookie in the request
     *
     * @return string
     */
    public function getUserResponse(): string
    {
        return Utils::apiResponse($this->getUser());
    }

    /**
     * Get a list of all users
     *
     * @return string
     */
    public function getUsers(): string
    {
        if ($this->checkUserIsAdmin()) {
            $instance = Database::getInstance();
            $connection = $instance->getConnection();
            $query = "SELECT id, email, full_name, to_char(created_at, 'MM/DD/YYYY HH:MI:SS') As created_at 
                    FROM users WHERE status = 1 ORDER BY id ASC";
            $statement = $connection->prepare($query);
            $statement->execute();
            return json_encode($statement->fetchAll(PDO::FETCH_ASSOC));
        }

        $response['status'] = 'forbidden';
        $response['message'] = 'Unauthorized request.';
        return Utils::apiResponse($response);
    }

    /**
     * Get a list of all roles
     *
     * @return string
     */
    public function getRoles(): string
    {
        if ($this->checkUserIsAdmin()) {
            $instance = Database::getInstance();
            $connection = $instance->getConnection();
            $query = 'SELECT id, name FROM roles ORDER BY name ASC';
            $statement = $connection->prepare($query);
            $statement->execute();
            return json_encode($statement->fetchAll(PDO::FETCH_ASSOC));
        }

        $response['status'] = 'forbidden';
        $response['message'] = 'Unauthorized request.';
        return Utils::apiResponse($response);
    }

    /**
     * @param object $request
     * @return string
     */
    public function deactivateUser(object $request): string
    {
        $deactivate_id = $request->id;
        // if the user trying to perform the action is administrator
        if ($this->checkUserIsAdmin()) {
            // if there's only one administrator
            if ($this->countAdmins() === 1) {
                // if the only administrator are not the same user that will be deactivated
                if ($this->checkUserIsAdmin($deactivate_id)) {
                    $response['status'] = 'invalid_operation';
                    $response['message'] = "You can't deactivate all the administrator accounts.";
                    return Utils::apiResponse($response);
                }
            }

            $instance = Database::getInstance();
            $connection = $instance->getConnection();

            $updated_at = date('Y-m-d H:i:s');

            $query = 'UPDATE users SET status = 0, updated_at = :updated_at WHERE id = :id';
            $statement = $connection->prepare($query);

            $statement->bindValue(':id', $deactivate_id);
            $statement->bindValue(':updated_at', $updated_at);

            $statement->execute();

            $response['status'] = 'success';
            $response['message'] = 'The user was deactivated.';
            $response['updated_at'] = $updated_at;
            return Utils::apiResponse($response);
        }

        $response['status'] = 'forbidden';
        $response['message'] = 'Unauthorized request.';
        return Utils::apiResponse($response);
    }

    /**
     * Count active administrators
     * @return int
     */
    public function countAdmins(): int
    {
        $instance = Database::getInstance();
        $connection = $instance->getConnection();
        $query = 'SELECT COUNT(*) AS total FROM users u 
                INNER JOIN users_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON r.id = ur.role_id
                WHERE r.name = :name AND u.status = :status';
        $statement = $connection->prepare($query);
        $statement->bindValue(':name', 'Administrator');
        $statement->bindValue(':status', 1);
        $statement->execute();
        $users = $statement->fetchObject();
        return $users->total;
    }

    /**
     * @param object $request
     * @return string
     */
    public function updateUser(object $request): string
    {
        $jwt_user = Utils::jwtDecode();
        if ($jwt_user->id) {
            $full_name = $request->full_name;
            $updated_at = date('Y-m-d H:i:s');
            if (!Utils::validateString($full_name, 2)){
                $response['status'] = 'invalid_name';
                $response['message'] = 'Please type your name.';
                return Utils::apiResponse($response);
            }

            $instance = Database::getInstance();
            $connection = $instance->getConnection();

            $query = 'UPDATE users SET full_name = :full_name, updated_at = :updated_at WHERE id = :id';
            $statement = $connection->prepare($query);

            $statement->bindValue(':full_name', $full_name);
            $statement->bindValue(':updated_at', $updated_at);
            $statement->bindValue(':id', $jwt_user->id);

            $statement->execute();

            $response['status'] = 'success';
            $response['message'] = 'Your account was updated.';
            $response['full_name'] = $full_name;
            $response['updated_at'] = $updated_at;
            return Utils::apiResponse($response);
        }

        $response['status'] = 'invalid_login';
        $response['message'] = 'Your credentials are invalid.';
        return Utils::apiResponse($response);
    }

    /**
     * @param $id
     * @return bool|object
     */
    public function selectById($id)
    {
        $instance = Database::getInstance();
        $connection = $instance->getConnection();

        $query = 'SELECT id, email, full_name, mfa_secret FROM users WHERE id = :id';
        $statement = $connection->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->fetchObject();
    }

    /**
     * @param $email
     * @return bool|object
     */
    public function selectByEmail($email)
    {
        $instance = Database::getInstance();
        $connection = $instance->getConnection();

        $query = 'SELECT id FROM users WHERE email = :email';
        $statement = $connection->prepare($query);
        $statement->bindValue(':email', $email);
        $statement->execute();
        return $statement->fetchObject();
    }

    /**
     * @param object $request
     * @return string
     */
    public function login(object $request): string
    {
        $email = $request->email;
        $password = $request->password;

        if (!Utils::validateEmail($email)){
            $response['status'] = 'invalid_email';
            $response['message'] = 'The email address is invalid.';
            return Utils::apiResponse($response);
        }

        if (!Utils::validateString($password, 8)) {
            $response['status'] = 'invalid_password';
            $response['message'] = 'Password must be 8 characters or more.';
            return Utils::apiResponse($response);
        }

        $instance = Database::getInstance();
        $connection = $instance->getConnection();

        $query = 'SELECT id, email, full_name FROM users WHERE email = :email AND password = :password AND status = 1';
        $statement = $connection->prepare($query);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':password', hash('sha512', $password));

        $statement->execute();
        $login_user = $statement->fetchObject();

        if ($login_user) {
            $login_jwt = $this->loginCredentials($login_user, 'false');
            $response['status'] = 'success';
            $response['jwt'] = $login_jwt;
        } else {
            $response['status'] = 'wrong_credentials';
            $response['message'] = 'Your email or password is incorrect.';
        }

        return Utils::apiResponse($response);
    }

    /**
     * @param $user
     * @param $mfa_verified
     * @return string
     */
    public function loginCredentials($user, $mfa_verified): string
    {
        $payload = [
            'id' => $user->id,
            'test' => '7',
            'global_mfa_enabled' => GLOBAL_MFA_ENABLED,
            'mfa_verified' => $mfa_verified,
            'iss' => APP_URL,
            'aud' => APP_URL,
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + JWT_EXPIRES
        ];
        $login_jwt = Utils::jwtEncode($payload);
        Utils::addCookieJwt($login_jwt);
        return $login_jwt;
    }

    /**
     * @param object $request
     * @return string
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function register(object $request): string
    {
        $administrator = $this->checkUserIsAdmin();

        if (isset($request->role)) {
            if ($administrator) {
                $role_id = $request->role;
            } else {
                $response['status'] = 'invalid_permissions';
                $response['message'] = 'The operation you are trying to perform is not allowed.';
                return Utils::apiResponse($response);
            }
        } else {
            $role_id = Utils::getRoleIdByName('User');
        }

        $user_object = new stdClass();
        $user_object->full_name = $request->full_name;
        $user_object->email = $request->email;
        $user_object->password = $request->password;
        $user_object->created_at = date('Y-m-d H:i:s');
        $user_object->updated_at = $user_object->created_at;
        $user_object->role = $request->role;

        if (!Utils::validateString($user_object->full_name, 2)){
            $response['status'] = 'invalid_name';
            $response['message'] = 'Please type your name.';
            return Utils::apiResponse($response);
        }

        if (!Utils::validateEmail($user_object->email)){
            $response['status'] = 'invalid_email';
            $response['message'] = 'The email address is invalid.';
            return Utils::apiResponse($response);
        }

        if (!Utils::validateString($user_object->password, 8)) {
            $response['status'] = 'invalid_password';
            $response['message'] = 'Password must be 8 characters or more.';
            return Utils::apiResponse($response);
        }

        $instance = Database::getInstance();
        $connection = $instance->getConnection();

        $email_in_use = $this->selectByEmail($user_object->email);
        if ($email_in_use) {
            $response['status'] = 'email_already_used';
            $response['message'] = 'The email address is already being used.';
            return Utils::apiResponse($response);
        }

        $google2fa = new Google2FA();
        $user_object->mfa_secret = $google2fa->generateSecretKey();

        $query = 'INSERT INTO users (full_name, email, mfa_secret, password, status, created_at, updated_at) 
            VALUES (:full_name, :email, :mfa_secret, :password, 1, :created_at, :updated_at)';
        $statement = $connection->prepare($query);
        $statement->bindValue(':full_name', $user_object->full_name);
        $statement->bindValue(':email', $user_object->email);
        $statement->bindValue(':mfa_secret', $user_object->mfa_secret);
        $statement->bindValue(':password', hash('sha512', $user_object->password));
        $statement->bindValue(':created_at', $user_object->created_at);
        $statement->bindValue(':updated_at', $user_object->updated_at);

        $statement->execute();

        $user_object->id = $connection->lastInsertId('users_id_seq');

        $query = 'INSERT INTO users_roles (user_id, role_id)
            VALUES (:user_id, :role_id)';
        $statement = $connection->prepare($query);
        $statement->bindValue(':user_id', $user_object->id);
        $statement->bindValue(':role_id', $role_id);

        $statement->execute();

        if ($administrator) {
            $response['status'] = 'user_registered';
            $response['message'] = 'The user was successfully registered';
        } else {
            $login_jwt = $this->loginCredentials($user_object, 'false');
            $response['status'] = 'success';
            $response['jwt'] = $login_jwt;
        }

        return Utils::apiResponse($response);
    }

    /**
     * Check the current logged user from JWT cookie
     *
     * @return array
     */
    public function checkJwtRoles(): array
    {
        $jwt_user = Utils::jwtDecode();

        $instance = Database::getInstance();
        $connection = $instance->getConnection();

        $query = 'SELECT role_id FROM users_roles ur
            INNER JOIN users u ON u.id = ur.user_id
            WHERE u.id = :user_id';

        $statement = $connection->prepare($query);
        $statement->bindValue(':user_id', $jwt_user->id);

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gets the user roles by id
     *
     * @param $user_id
     * @return array
     */
    public function rolesById($user_id): array
    {
        $instance = Database::getInstance();
        $connection = $instance->getConnection();

        $query = 'SELECT role_id FROM users_roles ur
            INNER JOIN users u ON u.id = ur.user_id
            WHERE u.id = :user_id';

        $statement = $connection->prepare($query);
        $statement->bindValue(':user_id', $user_id);

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function checkUserIsAdmin(string $user_id = ''): bool
    {
        if ($user_id === '') {
            $user_roles = $this->checkJwtRoles();
        } else {
            $user_roles = $this->rolesById($user_id);
        }

        foreach ($user_roles as $user_role) {
            if ($user_role['role_id'] === Utils::getRoleIdByName('Administrator')) {
                return true;
            }
        }

        return false;
    }
}
