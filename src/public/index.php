<?php
const LOCALES_DIR = __DIR__ . "/../locales/";

$defaultLang = "en";

$lang = $_GET['lang'] ?? $defaultLang;

if(!file_exists(LOCALES_DIR . "$lang.json")) $lang = $defaultLang;


$translation = json_decode(file_get_contents(LOCALES_DIR . "$lang.json"))->home;


?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>

    <link rel="alternate" hreflang="en" href="https://<?= $translation->url ?>?lang=en">
    <link rel="alternate" hreflang="it" href="https://<?= $translation->url ?>?lang=it">

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="<?= $translation->description?>">
    <meta property="og:description" content="<?= $translation->description?>">
    <meta property="og:site_name" content="<?= $translation->title ?>">
    <meta property="og:title" content="<?= $translation->title ?>">
    <meta property="og:url" content="<?= $translation->url ?>">
    <meta property="og:type" content="website">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1280">
    <meta property="og:image:height" content="800">
    <meta property="og:image" content="<?= $translation->url ?>logo.png">
    <meta property="twitter:card" content="summary_large_image">

    <link rel="canonical" href="<?= $translation->url ?>">

    <link rel="stylesheet" href="css/pure-min.css">
    <link rel="stylesheet" href="css/grids-responsive-min.css">
    <link rel="stylesheet" href="css/font-awesome.css">
    <link rel="stylesheet" href="css/index.css">

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <script src="script/instascan.min.js"></script>


    <title><?= $translation->title ?></title>
</head>
<body>

    <div id="qr-preview-container">
        <div class="exit-preview" onclick="scanQR(false)"></div>
        <video id="qr-scanner-preview" autoplay></video>
        <p>Click anywhere on the screen to exit</p>
    </div>

    <div itemscope itemtype="http://schema.org/WebPage" class="splash-container">
        <div class="splash">
            <div class="splash-head">
                <img itemprop="logo" src="/img/logo.png" alt="Website Logo"/>
            </div>
            <div class="splash-subhead">
                <h1 itemprop="name"><?= $translation->main->h1 ?></h1>
            </div>
            
            <div class="center">
                <div class="arrow-container" onclick="scrollToElement(event, 'content')" onkeydown="scrollToElement(event, 'content')" tabindex=0>
                    <div class="arrow"></div>
                    <p id="click-me"><?= $translation->main->click_me ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <main class="content-wrapper" id="content">

        <div class="content">
            <h2 class="content-head is-center"><?= $translation->main->h2 ?></h2>
        </div>

        <div class="ribbon l-box-lrg pure-g">
            <div class="pure-u-lg-1-5">&nbsp;</div>
            <div class="pure-u-1 pure-u-md-1-1 pure-u-lg-3-5">

                <div class="search-stop">
                    <h3><?= $translation->main->search_stop->title ?></h3>
                    <div class="search-bar">
                        <p><?= $translation->main->search_stop->description ?></p>
                        
                        <div class="searcher">
                            <input id="searchInput" type="number" placeholder="<?= $translation->main->search_stop->input->placeholder ?>">
                            <div class="scan-qr" onclick="scanQR()">
                                <img src="/img/scan-qr.png" alt="Scan QR Code">
                            </div>
                        </div>

                        <div id="searchResults"></div>


                        <i><?= $translation->main->search_stop->real_time ?></i>
                    </div>
                    <br>
                </div>

                <?php
                    foreach($translation->main->description as $section => $content){
                        echo "<h3>$content->title</h3>";
                    
                        foreach($content as $tag => $data)
                            if(is_array($data))
                                foreach($data as $value)
                                    echo "<$tag>$value</$tag>";
                    
                        echo "<br>";
                    }
                ?>
            </div>
        </div>
        
        <footer class="footer l-box is-center">
            <?= $translation->footer->content; ?>
        </footer>
    </main>

    <?php

        foreach(glob(__DIR__ . "/script/*.js") as $script){
            if(substr($script, -7) === ".min.js")
                continue;

            echo "<script>\n" .
                strtr(file_get_contents($script), [
                    "{{defaultLang}}" => $defaultLang,
                    "{{noCamerasError}}" => $translation->main->search_stop->qr_code->no_camera_error,
                    "{{cameraPermissionDenied}}" => $translation->main->search_stop->qr_code->permission_denied,
                    "{{stopNoResults}}" => $translation->main->search_stop->results->no_results,
                    "{{stopResultTitle}}" => $translation->main->search_stop->results->title,
                    "{{stopNotFound}}" => $translation->main->search_stop->results->not_found,
                    "{{stopGeneralError}}" => $translation->main->search_stop->results->general_error,

                ]) .
            "\n</script>";
        }

    ?>

    <script>
        const searchInput = document.getElementById('searchInput');
        const resultsContainer = document.getElementById('searchResults');

        let timeoutId;

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim();
            clearTimeout(timeoutId);

            if(/^\d+$/.test(query)){

                timeoutId = setTimeout(() => {
                    getStopInfo(query);
                }, 300);
                
            }else{
                resultsContainer.innerHTML = '';
            }
        });
    </script>

    

</body>
</html>