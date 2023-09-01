<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ESVR - ETML</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="./css/index.css">
</head>

<body>
    <header>
        <h1>Bienvenue sur ESVR.tech</h1>
        <p>Votre portail de ressources pour ETML Sébeillon-Venne</p>
    </header>
    
    <?php
        require_once './controllers/utils.php';
        
        if (!isLoggedIn()) {
            $action = isset($_GET['action']) ? $_GET['action'] : 'login';
        }
        else{
            $action = isset($_GET['action']) ? $_GET['action'] : 'connected';
        }

        error_log($action);
        
        switch ($action) {
            case 'login':
            case 'logout':
                require_once './controllers/loginController.php';
                $controller = new LoginController();
            break;
            case 'connected':
                // Utiliser le contrôleur ConnectedController si l'utilisateur est connecté
                require_once './controllers/connectedController.php';
                $controller = new ConnectedController();
            break;
            default:
            break;
        }
    ?>
    <nav>
        <a href="?action=<?php echo isLoggedIn() ? 'logout' : 'login'; ?>">
            <i class="fas <?php echo isLoggedIn()  ? 'fa-lock-open' : 'fa-lock'; ?>"></i>
            <?php echo isLoggedIn() ? 'Déconnexion' : 'Connexion'; ?>
        </a>
    </nav>

    <button style="display: none;" id="simulateKeyPrev">&#8592;</button>
    <button style="display: none;"  id="simulateKeyNext">&#8594;</button>

    <main>
        <?php
            if (isset($controller)) {
                
                $controller->handleRequest();
                
                if (DEBUG) {
                    $controllerClassName = get_class($controller);
                    echo "Nom de la classe de l'instance : " . $controllerClassName;
                    error_log("Nom de la classe de l'instance : " . $controllerClassName);
                }
            }
        ?>
    </main>

    <script src="./js/gestErrors.js"></script>
    <script src="./js/gestFolders.js"></script>

    <footer> <p>&copy; 2023 ESVR.tech | Tous droits réservés</p> </footer>
</body>
</html>