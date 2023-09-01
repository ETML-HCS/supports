<?php
require_once 'utils.php';
require_once './models/userModel.php';

class LoginController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();

        // Vérifier si la session est déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        } else {
            $_SESSION[MSG_ERROR] = "Session en cours";
        }
    }

    public function handleRequest()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = $_POST["username"];
            $password = $_POST["password"];

            // Remplacez cette vérification factice par votre logique d'authentification réelle
            if ($this->authenticateUser($username, $password)) {

                $this->redirectToDashboard($username);
            } else {
                // Identifiants incorrects
                $this->showLoginForm("Identifiants incorrects. Veuillez réessayer.");
                error_log("problème avec les Identifiants");
            }
        } else {
            if (isset($_GET['action'])) {
                $action = $_GET['action'];
                if ($action === 'logout') {
                    session_unset();
                    session_destroy();
                    $_SESSION[MSG_ERROR] = "Suppression de la session";
                    error_log("suppresion de la session");
                }
            }
            // Afficher le formulaire de connexion
            $this->showLoginForm();
        }
    }

    private function authenticateUser($username, $password)
    {
        error_log("Authentification en cours...");
        // Vérifier les informations d'authentification en utilisant le modèle UserModel
        $user = $this->userModel->getUserByUsernameAndPassword($username, $password);

        if ($user !== null) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['modules'] = $user['modules'];

            $_SESSION[MSG_ERROR] = "Authentification réussie !";
            return true;
        } else {
            $_SESSION[MSG_ERROR] = "identifiants incorrects";
            return false;
        }
    }

    private function showLoginForm($error = "")
    {
        $_SESSION[MSG_ERROR] = $error;
        include('views/login.php');
    }

    private function redirectToDashboard($username)
    {
        $payload = array(
            "username" => $username,
            "exp" => time() + (60 * 60) // Durée de validité du token (1 heure)
        );

        $token = generateJWT($payload);

        // Nom du cookie dans lequel nous allons stocker le token
        $cookie_name = COOKIE_NAME;

        // Encodage du token pour une utilisation dans le cookie
        // (utilisation de rawurlencode pour conserver les caractères spéciaux)
        $encoded_token = rawurlencode($token);

        // Durée de validité du cookie en secondes (1 heure)
        $cookie_duration = 3600;

        // Chemin du cookie (pour que le cookie soit accessible uniquement dans ce répertoire)
        $cookie_path = "/";

        // Domaine du cookie (vous pouvez le configurer en fonction de votre domaine)
        $cookie_domain = "esvr.tech";

        // Indique au navigateur de sécuriser le cookie en l'envoyant uniquement sur des connexions HTTPS
        $cookie_secure = true;

        // Indique au navigateur que le cookie ne doit pas être accessible via JavaScript (protection contre les attaques XSS)
        $cookie_httponly = true;

        // Crée le cookie avec le token et les paramètres définis
        setcookie(
            $cookie_name,
            $encoded_token,
            time() + $cookie_duration,
            $cookie_path,
            $cookie_domain,
            $cookie_secure,
            $cookie_httponly
        );
        error_log("cookie créer header action=connected");
        // Rediriger vers la page du tableau de bord
        header("Location: index.php?action=connected");
        exit;
    }
}
