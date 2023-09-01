//#region : Fonctions d'affichage

function updateProgressBar() {
  var progressBar = document.getElementById('progressBar');
  var progress = (slideShow.currentSlide + 1) / slideShow.slides.length * 100;
  progressBar.style.width = progress + '%';
}

/**
 * Affiche le slide spécifié dans le diaporama
 * @param {number} slideIndex - L'index du slide à afficher
 */
function goToSlide(slideIndex) {
  if (slideIndex < 0 || slideIndex >= slideShow.slides.length) {
    console.error("Index de diapositive invalide !");
    return;
  }

  slideShow.showSlide(slideIndex);
}

function goToSlideByInput() {
  var slideIndex = parseInt(document.getElementById('slideInput').value);

  if (!isNaN(slideIndex)) { 
    goToSlide(slideIndex);
  } 
}

/**
 * Affiche les textes cachés
 * @param {Array} hiddenTexts - Les textes cachés à afficher
 */
function afficherTextes(hiddenTexts) {
  if (hiddenTexts.length === 0) {
    console.log("Aucun élément hiddenText trouvé.");
    return;
  }

  for (let i = 0; i < hiddenTexts.length; i++) {
    hiddenTexts[i].style.display = "block";
  }
}
//#endregion : Fonctions d'affichage

//#region : SlideShow
var slideShow = {
  slides: [],
  currentSlide: 0,

  initialize: function () {
    this.slides = document.getElementsByClassName("slide");

    // Vérifie si des diapositives existent
    if (this.slides.length === 0) {
      console.error("Aucune diapositive trouvée !");
      return;
    }

    // Afficher la première diapositive
    this.showSlide(0);

    // Ajoute l'écouteur d'événement pour les touches du clavier
    document.addEventListener("keydown", this.keyDownHandler.bind(this));
  },

  showSlide: function (n) {
    var pageOfPage = document.getElementById('page');
    pageOfPage.innerHTML = "Slide " + n + "/" + this.slides.length;

    // Masquer la diapositive actuelle
    this.slides[this.currentSlide].style.display = "none";

    // Calculer l'index de la nouvelle diapositive
    this.currentSlide = (n + this.slides.length) % this.slides.length;

    // Afficher la nouvelle diapositive
    this.slides[this.currentSlide].style.display = "block";

    // Mettre à jour la position de la ligne de progression
    updateProgressBar();

    // Récupérer les éléments hiddenText de la diapositive actuelle
    this.hiddenTexts = this.slides[this.currentSlide].getElementsByClassName("hiddenText");

    if (this.hiddenTexts.length !== 0) {
      // Afficher les textes cachés avec un intervalle de 1660ms
      setInterval(function () {
        afficherTextes(slideShow.hiddenTexts);
      }, 1660);
    } else {
      console.log("Pas de texte caché.");
    }
  },

  keyDownHandler: function (e) {
    switch (e.keyCode) {
      case 37: // Flèche gauche
        this.showSlide(this.currentSlide - 1);
        break;
      case 39: // Flèche droite
        this.showSlide(this.currentSlide + 1);
        break;
    }
  },
};

slideShow.initialize();

//#endregion : SlideShow

//#region : Zoom

/**
 * Zoom sur une image
 * @param {Object} img - L'image à zoomer
 */
function zoomImage(img) {
  // Crée l'élément de zoom
  let zoomedElement = document.createElement('div');
  zoomedElement.classList.add('zoomed');

  // Crée l'élément d'image dans le zoom
  let zoomedImage = document.createElement('img');
  zoomedImage.src = img.src;

  // Ajoutez l'image au zoom
  zoomedElement.appendChild(zoomedImage);

  // Ajoutez le zoom à la page
  document.body.appendChild(zoomedElement);

  // Supprimez le zoom lorsque vous cliquez dessus
  zoomedElement.onclick = function () {
    document.body.removeChild(zoomedElement);
  };
}

/**
 * Zoom sur un texte
 * @param {String} texts - Le texte à zoomer
 */
function zoomText(texts) {
  // Crée l'élément de zoom
  let zoomedElement = document.createElement('div');
  zoomedElement.classList.add('zoomed');

  // Crée l'élément texte dans le zoom
  let zoomedText = document.createElement('p');
  zoomedText.textContent = texts;

  // Ajoute le texte zoomé à l'élément zoomé
  zoomedElement.appendChild(zoomedText);

  // Ajoute l'élément zoomé au corps de la page
  document.body.appendChild(zoomedElement);

  // Supprimez le zoom lorsque vous cliquez dessus
  zoomedElement.onclick = function () {
    document.body.removeChild(zoomedElement);
  };
}

//#endregion : Zoom

//#region : Horloge

/**
 * Affiche l'heure actuelle
 */
function writeTime() {

  const baliseTime = document.getElementById('horloge');

  let time = new Date();

  let hours = time.getHours().toLocaleString();
  let minutes = time.getMinutes().toLocaleString().padStart(2, '0');

  baliseTime.textContent = hours + "h " + minutes;
  console.log('refresh time');
}

// Appelle la fonction writeTime toutes les secondes
setInterval(writeTime, 30000);

//#endregion : Horloge

//#region : BuildSummer

function buildSommaire() {
  // Référence à l'élément cible où la table des matières sera insérée
  var ici = document.getElementById('sommaire');

  // Création d'un élément de liste non ordonnée pour contenir la table des matières
  var listeTdm = document.createElement('ul');

  // Recherche de tous les titres h2 sur la page
  var allTitles = document.getElementsByClassName('title');
  var titresH2 = Array.from(allTitles).filter(title => /^\d+\.0$/.test(title.id));

  // // Création des deux colonnes pour les titres
  // var colFirst = document.createElement('div');
  // colFirst.classList.add('col-l');
  // var colSecond = document.createElement('div');
  // colSecond.classList.add('col-r');

  // Parcours des titres h2 et création des éléments de liste correspondants
  for (var i = 0; i < titresH2.length; i++) {
    var titreH2 = titresH2[i];

    // Création d'un élément de liste
    var elementListe = document.createElement('li');

    // Définition du contenu interne de l'élément de liste avec le texte du titre h2
    elementListe.innerHTML = titreH2.textContent;
    
    // Ajout des colonnes à l'élément de liste (colFirst +colSecond)
    // cette instruction doit etre placé hors de la boucle si nous souhaitons obtenir 2 colonnes -.-) 
    listeTdm.appendChild(elementListe);

    // if (i <= 7) {
    //   colFirst.appendChild(elementListe);
    // } else {
    //   colSecond.appendChild(elementListe);
    // }
  }
  // Ajout de la liste de la table des matières à l'élément cible
  ici.appendChild(listeTdm);
}

//#endregion : BuildSummer
