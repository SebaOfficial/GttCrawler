const localLang = (navigator.language || navigator.userLanguage).substr(0, 2);
const queryString = window.location.search;

if(localLang != "{{defaultLang}}"){

    if (queryString.includes("?lang=")) {

        if(queryString.split("?lang=")[1] === ""){
            window.location.href = `?lang=${localLang}`;
        }

    } else {
        window.location.href = `?lang=${localLang}`;
    }

} else{
    if (queryString.includes("?lang=")) {

        if(queryString.split("?lang=")[1] === ""){
            window.history.replaceState(null, "", `?lang=${localLang}`);
        }

    } else {
        window.history.replaceState(null, "", `?lang=${localLang}`);
    }
}