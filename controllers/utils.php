<?php
define("DEBUG", true);
define('COOKIE_NAME', 'auth_token');
define('MSG_ERROR', 'msgErrors');
define('SECRET_KEY', 'secret_key');

// Activez cette ligne en mode développement pour voir les erreurs PHP
if (DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

function isLoggedIn()
{
    if (isset($_COOKIE[COOKIE_NAME])) {
        return true;
        error_log("cookie present");
    } else {
        return false;
        error_log("pas de cookie");
    }
}
function generateJWT($payload, $expiration_time = 3600)
{
    $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload['exp'] = time() + $expiration_time;
    $payload = base64_encode(json_encode($payload));
    $signature = hash_hmac('sha256', "$header.$payload", SECRET_KEY, true);
    $signature = base64_encode($signature);

    return "$header.$payload.$signature";
}

function validateJWT($token)
{
    // Valider la structure du token
    $tokenParts = explode('.', $token);
    if (count($tokenParts) !== 3) {
        throw new Exception('Invalid token structure');
    }

    list($header, $payload, $signature) = $tokenParts;

    // Calculer la signature attendue
    $expected_signature = base64_encode(hash_hmac('sha256', "$header.$payload", SECRET_KEY, true));

    // Comparaison sécurisée en temps constant des signatures
    if (hash_equals($signature, $expected_signature)) {
        $payload = json_decode(base64_decode($payload), true);

        // Vérifier l'heure d'expiration
        if (isset($payload['exp']) && $payload['exp'] >= time()) {
            return true;
        } else {
            throw new Exception('Token has expired');
        }
    } else {
        throw new Exception('Invalid signature');
    }

    return false;
}

function decryptPassword($encryptedPassword)
{

    $cipher = "AES-128-CBC";
    $hashAlgo = "sha256";
    $iterations = 100000;
    $keyLength = 16;

    // Décodage de la chaîne en base64
    $ciphertext_dec = base64_decode($encryptedPassword);

    // Longueur de l'IV
    $ivlen = openssl_cipher_iv_length($cipher);

    if ($ivlen !== false && strlen($ciphertext_dec) >= $ivlen) {

        // Extraction de l'IV et du Salt
        $iv = substr($ciphertext_dec, 0, $ivlen);
        $salt = $iv; // Si OpenSSL utilise le même pour salt et IV

        // Suppression de l'IV du texte chiffré
        $ciphertext_dec = substr($ciphertext_dec, $ivlen);

        // Utilisation de PBKDF2 pour dériver la clé
        $derivedKey = hash_pbkdf2($hashAlgo, SECRET_KEY, $salt, $iterations, $keyLength, true);

        // Déchiffrement
        $decryptedText = openssl_decrypt($ciphertext_dec, $cipher, $derivedKey, OPENSSL_RAW_DATA, $iv);

        if ($decryptedText === false) {
            error_log("OpenSSL decrypt error: " . openssl_error_string());
            return false;
        }

        return $decryptedText;
    } else {
        error_log("Either ivlen is false or ciphertext_dec length is less than ivlen.");
        return false;
    }
}

function encryptPassword($plainText)
{

    $cipher = "AES-128-CBC";
    $hashAlgo = "sha256";
    $iterations = 100000;
    $keyLength = 16;

    // Générer un IV aléatoire
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);

    // Utiliser le IV comme "salt" pour cette démo (à adapter selon votre cas d'utilisation)
    $salt = $iv;

    // Utilisation de PBKDF2 pour dériver la clé
    $derivedKey = hash_pbkdf2($hashAlgo, 'secret_key', $salt, $iterations, $keyLength, true);

    // Chiffrement
    $encryptedText = openssl_encrypt($plainText, $cipher, $derivedKey, OPENSSL_RAW_DATA, $iv);

    // Concaténation de l'IV et du texte chiffré
    $encryptedWithIv = $iv . $encryptedText;

    // Encodage en base64
    $encryptedBase64 = base64_encode($encryptedWithIv);

    return $encryptedBase64;
}


function displayError($error)
{
    echo "<script>addMessageFromPHP('error','$error');</script>";
}

function displaySuccess($success)
{
    echo "<script>addMessageFromPHP('success','$success');</script>";
}

function displayWarning($warning)
{
    echo "<script>addMessageFromPHP('warning','$warning');</script>";
}
