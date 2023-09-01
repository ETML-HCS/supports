<?php
require_once 'controllers/utils.php';
require_once 'controllers/connectedController.php';

$encoded_token = $_COOKIE[COOKIE_NAME] ?? null;

// Vérifier si $encoded_token n'est pas null
if (!is_null($encoded_token)) {
    // Décoder le token
    $decoded_token = rawurldecode($encoded_token);
} else {
    // Gérer l'erreur ici, par exemple rediriger vers la page de login ????
    header("Location: /");
    exit();
}

if ($decoded_token !== null) {
    $is_valid = validateJWT( $decoded_token);

    if ($is_valid) {
        // Le token est valide, continuez le traitement
        $username = json_decode(base64_decode(explode('.',  $decoded_token)[1]), true)['username'];
        $_SESSION[MSG_ERROR]="Connexion valide pour l'utilisateur : $username";

        $connectedController = new ConnectedController();
        $supportFolders = $connectedController->getAllSupportFolders();

        if (!empty($supportFolders)) {
            generateModuleAndChapterLinks($supportFolders);
          } else {
            echo"<h3>Aucun dossier trouvé dans le répertoire support2cours.</h3>";
        }
    } else {
        // Le token est invalide
        $_SESSION[MSG_ERROR]="Token invalide";
    }
} else {
    // Le token n'est pas présent dans le cookie ou est invalide
    $_SESSION[MSG_ERROR]="Token manquant ou invalide";
}
/**
 *  Functions 
 */

function generateModuleAndChapterLinks($supportFolders){
    echo '<ul id="moduleList">';

    foreach ($supportFolders as $module) {
        // Utiliser le nom du module comme ID unique
        $moduleId = 'module_' . htmlspecialchars($module);
        
        echo '<li id="' . $moduleId 
        . '" onclick="handleModuleClick(\'' 
        . $moduleId . '\')">' 
        . htmlspecialchars($module) 
        . '</li>';
        
        echo '<ul id="' 
        . $moduleId 
        . '_chapters" style="display:none;">';

        $modulePath = './supports2cours/' . $module;
        $moduleChapters = scandir($modulePath);

        foreach ($moduleChapters as $chapter) {
            if ($chapter !== '.' && $chapter !== '..' && is_dir($modulePath . '/' . $chapter)) {
                // Utiliser le nom du chapitre comme ID unique
                $chapterId = 'chapter_' . htmlspecialchars($chapter);
                echo '<li id="' 
                .$chapterId
                .'" onclick="loadSlides(\'' . htmlspecialchars($module) . '\', \'' . htmlspecialchars($chapter) . '\')">' 
                . htmlspecialchars($chapter)
                .'</li>';
            }
        }
        echo '</ul>';
    }
    echo '</ul>';
}

