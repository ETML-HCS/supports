<?php
require_once './config/db.php';

class UserModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance();
    }

    public function getAllUsers()
    {
        $sql = "SELECT * FROM t_user";
        $result =  $this->conn->query($sql);

        $users = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }

        return $users;
    }

    public function getUserById($id)
    {
        $sql = "SELECT * FROM t_user WHERE id = ?";
        $stmt =  $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    public function addUser($username, $password, $email, $modules)
    {
        $hashedPassword = password_hash($password,PASSWORD_DEFAULT);  // Ajout du hachage du mot de passe

        // Requête SQL pour ajouter un nouvel utilisateur
        $sql = "INSERT INTO t_user (username, password, email, modules) VALUES (?, ?, ?, ?)";
        $stmt =  $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $hashedPassword, $email, $modules);  // Utilisez le mot de passe hashé

        if ($stmt->execute()) {
            return true; // L'utilisateur a été ajouté avec succès
        } else {
            return false; // Erreur lors de l'ajout de l'utilisateur
        }
    }

    public function updateUser($id, $username, $password, $email, $modules)
    {
        // Requête SQL pour mettre à jour un utilisateur existant
        $sql = "UPDATE t_user SET username = ?, password = ?, email = ?, modules = ? WHERE id = ?";
        $stmt =  $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $username, $password, $email, $modules, $id);

        if ($stmt->execute()) {
            return true; // L'utilisateur a été mis à jour avec succès
        } else {
            return false; // Erreur lors de la mise à jour de l'utilisateur
        }
    }

    public function deleteUser($id)
    {
        // Requête SQL pour supprimer un utilisateur
        $sql = "DELETE FROM t_user WHERE id = ?";
        $stmt =  $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return true; // L'utilisateur a été supprimé avec succès
        } else {
            return false; // Erreur lors de la suppression de l'utilisateur
        }
    }

    /**
     * Récupère un utilisateur en fonction de son nom d'utilisateur et de son mot de passe.
     * 
     * @param string $username Nom d'utilisateur.
     * @param string $password Mot de passe.
     * 
     * @return array|null Les informations de l'utilisateur ou null si non trouvé.
     * @throws Exception Si une erreur de base de données se produit.
     */
    public function getUserByUsernameAndPassword($username, $password)
    {
        if ($this->conn === null) {
            throw new Exception("La connexion à la base de données est null.");
        }

        // Préparation de la requête SQL pour récupérer l'utilisateur en fonction du nom d'utilisateur
        $stmt = $this->conn->prepare("SELECT * FROM t_user WHERE username = ?");
        if ($stmt === false) {
            throw new Exception("Échec de la préparation de la requête : " . $this->conn->error);
        }

        // Liaison des paramètres
        $stmt->bind_param("s", $username);

        // Exécution de la requête
        $stmt->execute();

        // Récupération du résultat
        $result = $stmt->get_result();

        // On vérifie qu'on a bien un utilisateur
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {  // Utilisation de password_verify pour vérifier le mot de passe hashé
                return $user;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}
