// Appeler la fonction une fois que le DOM est chargé
document.addEventListener('DOMContentLoaded', adjustHeights);

// Appeler la fonction lors du redimensionnement de la fenêtre
window.addEventListener('resize', adjustHeights);

// Fonction pour gérer les clics sur les modules (niveau 1)
function handleModuleClick(moduleId) {
    var chaptersList = document.getElementById(moduleId + "_chapters");
    if (chaptersList.style.display === "none") {
        chaptersList.style.display = "block";
    } else {
        chaptersList.style.display = "none";
    }
}

const simulateKeyPrev = document.getElementById('simulateKeyPrev');
const simulateKeyNext =document.getElementById('simulateKeyNext');

simulateKeyPrev.addEventListener('click', function() {
    var iframeContent = document.getElementById('slidesContainer').contentWindow.document;
    simulateKeyPress(iframeContent, 37); 
});

simulateKeyNext.addEventListener('click', function() {
    var iframeContent = document.getElementById('slidesContainer').contentWindow.document;
    simulateKeyPress(iframeContent, 39); 
});

function loadSlides(module, chapterName) {

    // Appeler la fonction pour supprimer les enfants de la balise main
    removeChildrenFromMain();

    // Crée la balise iframe pour afficher le support de cours
    makeIframeFromMain();

    var slidesFile = './supports2cours/' + module + '/' + chapterName + '/slides.html';

    var iframe = document.getElementById('slidesContainer');
    iframe.src = slidesFile;

    // Assurer que les ressources (CSS, JavaScript) sont chargées correctement dans l'iframe
    iframe.onload = function () {
        var iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
        var baseElement = iframeDocument.createElement('base');
        baseElement.href = './supports2cours/' + module + '/' + chapterName + '/';
        iframeDocument.head.appendChild(baseElement);
    };
    simulateKeyNext.style.display="block";
    simulateKeyPrev.style.display="block";
}

function makeIframeFromMain() {
    // Créer l'élément iframe
    var iframe = document.createElement('iframe');

    // Définir les attributs de l'iframe
    iframe.id = 'slidesContainer';
    iframe.src = '';
    // iframe.frameBorder = 0;
    iframe.title = 'Support de cours';

    // Ajouter l'iframe à la balise main
    var mainElement = document.querySelector('main');
    mainElement.appendChild(iframe);  
}

function removeChildrenFromMain() {
    var mainElement = document.querySelector('main');
    while (mainElement.firstChild) {
        mainElement.removeChild(mainElement.firstChild);
    }
}

function adjustHeights() {
    var headerHeight = document.querySelector('header').offsetHeight;
    var mainElement = document.querySelector('main');
    mainElement.style.height = 'calc(100vh - ' + headerHeight + 'px)';
}

// Votre fonction simulateKeyPress, mise à jour pour prendre en compte le contexte du document
function simulateKeyPress(contextDocument, keyCode) {
    var event = new KeyboardEvent('keydown', { 'keyCode': keyCode, 'which': keyCode });
    contextDocument.dispatchEvent(event);
}

