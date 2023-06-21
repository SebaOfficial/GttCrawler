<?php
const LOCALES_DIR = __DIR__ . "/../locales/";

$lang = $_GET['lang'] ?? "en";

if(!file_exists(LOCALES_DIR . "$lang.json")) $lang = "en";


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

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>


    <title><?= $translation->title ?></title>
</head>
<body>
    <div class="splash-container">
        <div class="splash">
            <h1 class="splash-head"><img src="logo.png"/></h1>
            <p class="splash-subhead">
                <h1><?= $translation->main->h1 ?></h1>
            </p>
            
            <center>
                <div class="arrow-container" onclick="scrollToElement(event, 'content')" onkeydown="scrollToElement(event, 'content')" tabindex=0>
                    <div class="arrow"></div>
                    <p id="click-me"><?= $translation->main->click_me ?></p>
                </div>
            </center>
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
                        <input id="searchInput" type="number" placeholder="<?= $translation->main->search_stop->input->placeholder ?>">
                        <div id="stopResults"></div>
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

    <script>
        let scrollTimeoutId;
        
        function scrollToElement(event, elementId) {
            event.preventDefault(); // Prevents the default behavior of the anchor tag

            clearTimeout(scrollTimeoutId); // Clear any previously set timeout

            const targetElement = document.getElementById(elementId); // Get the target element
            const targetPosition = targetElement.offsetTop; // Get the top position of the target element

            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth' // Scroll smoothly
            });
        }

        scrollTimeoutId = setTimeout(function(){
            let clickMe = document.getElementById('click-me');
            clickMe.style.opacity = "1";
        }, 2000);

        const localLang = (navigator.language || navigator.userLanguage).substr(0, 2);
        const queryString = window.location.search;

        if (queryString.includes("?lang=")) {
            if(queryString.split("?lang=")[1] === "")
                window.history.replaceState(null, "", `?lang=${localLang}`);
        } else {
            window.history.replaceState(null, "", `?lang=${localLang}`);
        }


        const searchInput = document.getElementById('searchInput');
        const resultsContainer = document.getElementById('stopResults');

        let timeoutId;

        searchInput.addEventListener('input', async () => {
            const query = searchInput.value.trim();
            clearTimeout(timeoutId);

            if(/^\d+$/.test(query)){ // Check if it's a number

                timeoutId = setTimeout(async () => {
                    try {
                        const response = await axios.get(`/api.php?stop=${query}`);
                        
                        const results = response.data;
                        console.log(results);

                        // Clear previous results
                        resultsContainer.innerHTML = '';

                        // Display the results
                        if (results.length === 0) {
                            resultsContainer.innerText = "<?= $translation->main->search_stop->results->no_results ?>";

                        } else {
                            results.forEach(result => {
                                const item = document.createElement('div');
                                const title = document.createElement('p');

                                item.classList.add('search-result');

                                title.innerText = "<?= $translation->main->search_stop->results->title ?> " + result.line + ": " + result.hour + (result.realtime ? " *" : "");
                                item.appendChild(title);
                                
                                resultsContainer.appendChild(item);
                            });
                        }
                    } catch (error) {
                        let statusCode = error.response.status;

                        resultsContainer.innerHTML = (() => {
                            switch (error.response.status) {
                                case 404:
                                    return "<?= $translation->main->search_stop->results->not_found ?>";
                                
                                default:
                                    return "<?= $translation->main->search_stop->results->general_error ?>";
                            }
                        })();
                    }
                }, 300);

            }else{
                resultsContainer.innerHTML = '';
            }
        });



    </script>

    

</body>
</html>