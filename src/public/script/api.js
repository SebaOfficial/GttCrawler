async function getStopInfo(query) {
    try {
        const response = await axios.get(`/api.php?stop=${query}`);

        const results = response.data;

        // Clear previous results
        resultsContainer.innerHTML = '';

        // Display the results
        if (results.length === 0) {
            resultsContainer.innerText = "{{stopNoResults}}";
        } else {
            results.forEach(result => {
                const item = document.createElement('div');
                const title = document.createElement('p');

                item.classList.add('search-result');

                title.innerText = `{{stopResultTitle}} ${result.line}: ${result.hour} ${(result.realtime ? " *" : "")}`;
                item.appendChild(title);

                resultsContainer.appendChild(item);
            });
        }
    } catch (error) {
        let statusCode = error.response.status;

        resultsContainer.innerHTML = (() => {
            switch (error.response.status) {
                case 404:
                    return "{{stopNotFound}}";

                default:
                    return "{{stopGeneralError}}";
            }
        })();
    }
}