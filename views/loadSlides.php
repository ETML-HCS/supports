<?php

function loadSlides($module, $chapterName) {
    $slidesFile = './supports2cours/' . $module . '/' . $chapterName . '/slides.html';

    $realPath = realpath($slidesFile);
    if ($realPath === false || !is_file($realPath)) {
        throw new Exception("Erreur : Le fichier '$slidesFile' n'a pas été trouvé.");
    }

    include $realPath;
}

