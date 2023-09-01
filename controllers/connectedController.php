<?php
require_once 'utils.php';
require_once './models/userModel.php';

class ConnectedController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel(Database::getInstance());
    }

    public function handleRequest()
    {
        // Gérer les différentes actions en fonction des paramètres d'action
        $action =  $_GET['action'] ?? 'connected';

        if ($action === 'connected') {
            // L'action "connected" est déjà définie, afficher la page par défaut du tableau de bord
            $this->showDashboard();
            error_log("Dashboard lancé");
        } else {
            // Traiter les autres actions
            error_log("autres page que Dashboard");
        }
    }

    private function showDashboard()
    {
        return include ('views/dashbord.php');
    }

    public function getSupportFiles($folder = './supports2cours')
    {
        $filesList = array();
    
        if (is_dir($folder)) {
            $files = scandir($folder);
    
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $folder . '/' . $file;
                    if (is_dir($filePath)) {
                        // Si c'est un sous-dossier, récursivement, ajoutez tous les fichiers de ce sous-dossier
                        $subFilesList = $this->getSupportFiles($filePath);
                        foreach ($subFilesList as $subFile) {
                            $filesList[] = $file . '/' . $subFile;
                        }
                    } else {
                        // Si c'est un fichier, ajoutez-le à la liste
                        $filesList[] = $file;
                    }
                }
            }
        }
        return $filesList;
    }

    public function getAllSupportFolders()
    {
        $supportFolder = './supports2cours';
        $folders = array();
    
        if (is_dir($supportFolder)) {
            $subFolders = glob($supportFolder . '/*', GLOB_ONLYDIR);
    
            foreach ($subFolders as $subFolder) {
                $folders[] = basename($subFolder);
            }
        }    
        return $folders;
    }
}
