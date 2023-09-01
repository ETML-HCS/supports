<?php
require_once './controllers/utils.php';

// Conformément aux meilleures pratiques, l'utilisation d'un fichier d'environnement serait recommandée.
// Toutefois, cette approche peut nécessiter l'intégration de plusieurs bibliothèques externes.

/**
 * Classe Database pour la gestion de la connexion à la base de données.
 */
class Database
{
    /**
     * Instance unique de la connexion à la base de données.
     * @var mysqli
     */
    private static $instance = null;

    /**
     * Paramètres de connexion à la base de données.
     * @var array
     */
    private static $settings = [
        'servername' => DEBUG ? "srv1060.hstgr.io" : "localhost", // serveur, potentiellement différent en mode DEBUG
        'username' => "u871035213_admin", // nom d'utilisateur pour la BDD
        'encrypted_password' => "ci3X9almPKln2FEqrXk8rlp71zvtrNQSqzTg6Q53J4s=", // mot de passe crypté
        'dbname' => "u871035213_supports" // nom de la BDD
    ];

    /**
     * Obtient l'instance unique de la connexion à la base de données.
     *
     * @return mysqli L'instance de la connexion à la base de données.
     * @throws Exception Si une erreur se produit lors de la connexion.
     */
    public static function getInstance()
    {
        // Si l'instance n'existe pas, on essaie de la créer
        if (self::$instance === null) {
            try {
                // Déchiffrement du mot de passe
                $decrypted_password = decryptPassword(self::$settings['encrypted_password']);

                // Instanciation de la classe mysqli pour la connexion à la BDD
                self::$instance = new mysqli(
                    self::$settings['servername'],
                    self::$settings['username'],
                    $decrypted_password,
                    self::$settings['dbname']
                );

                // Vérification de la connexion
                if (self::$instance->connect_error) {
                    throw new Exception("Erreur de connexion : " . self::$instance->connect_error);
                }
            } catch (Exception $e) {
                // Enregistrement de l'erreur dans un fichier log
                error_log($e->getMessage());

                // Lancement d'une nouvelle exception pour indiquer une erreur générale
                throw new Exception("Une erreur est survenue. Veuillez réessayer plus tard.");
            }
        }

        // Retourne l'instance de la connexion
        return self::$instance;
    }
}
