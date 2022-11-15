<?php

declare(strict_types=1);

namespace App;

use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Exception;
use PragmaRX\Google2FA\Google2FA;

/**
 * Class to handle the user MFA
 */
class UserMfa extends User
{
    /**
     * @return string
     */
    public function getQrCodeMfa(): string
    {
        $jwt_user = Utils::jwtDecode();
        if ($jwt_user->id) {
            $db_user = $this->selectById($jwt_user->id);
            if ($db_user) {
                $google2fa = new Google2FA();
                $google2fa_url = $google2fa->getQRCodeUrl(QRCODE_COMPANY, $db_user->email, $db_user->mfa_secret);
                $writer = new Writer(
                    new ImageRenderer(
                        new RendererStyle(400),
                        new ImagickImageBackEnd()
                    )
                );
                $mfa_image = base64_encode($writer->writeString($google2fa_url));
                $response['status'] = 'success';
                $response['mfa_secret'] = $db_user->mfa_secret;
                $response['mfa_image'] = $mfa_image;
                return Utils::apiResponse($response);
            }
        }

        $response['status'] = 'invalid_login';
        $response['message'] = 'Your credentials are invalid.';
        return Utils::apiResponse($response);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getUserMfaSecret($id)
    {
        $instance = Database::getInstance();
        $connection = $instance->getConnection();

        $query = 'SELECT mfa_secret FROM users WHERE id = :id';
        $statement = $connection->prepare($query);
        $statement->bindValue(':id', $id);

        $statement->execute();
        return $statement->fetchObject()->mfa_secret;
    }

    /**
     * @param object $request
     * @return string
     */
    public function verifyTokenMfa(object $request): string
    {
        $token = $request->token ?? null;
        if (!Utils::validateString($token, 6)){
            $response['status'] = 'invalid_token';
            $response['message'] = 'The token is invalid.';
            return Utils::apiResponse($response);
        }
        try {
            $jwt_user = Utils::jwtDecode();
            $mfa_secret = $this->getUserMfaSecret($jwt_user->id);
            $google2fa = new Google2FA();
            if ($google2fa->verifyKey($mfa_secret, $token, 2)) {
                $login_jwt = $this->loginCredentials($jwt_user, 'true');
                $response['status'] = 'success';
                $response['jwt'] = $login_jwt;
                return Utils::apiResponse($response);
            }
        } catch (Exception $exception) {
            $response['status'] = 'invalid_token';
            $response['message'] = $exception->getMessage();
            return Utils::apiResponse($response);
        }
        $response['status'] = 'invalid_token';
        $response['message'] = 'The informed token is invalid';
        return Utils::apiResponse($response);
    }
}