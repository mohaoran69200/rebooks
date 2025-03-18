document.addEventListener('DOMContentLoaded', () => {
    const addressInput = document.querySelector('[data-action=address-input]');
    const postalCodeInput = document.querySelector('[data-action=postal-code-input]');
    const cityInput = document.querySelector('[data-action=city-input]');
    const dropdownContainer = document.querySelector('#dropdown-menu');
    const dropdownResults = document.querySelector('#dropdown-results');

    if (!addressInput || !postalCodeInput || !cityInput || !dropdownContainer || !dropdownResults) {
        console.warn("Un ou plusieurs éléments du formulaire d'adresse sont introuvables.");
        return;
    }

    const noResult = document.createElement('li');
    noResult.className = 'no-result';
    noResult.innerText = 'Aucun résultat';

    addressInput.addEventListener('keyup', debounce(function (e) {
        const value = e.target.value.trim();

        if (value.length < 3 || value.length > 200) {
            dropdownContainer.classList.add('hidden');
            dropdownResults.innerHTML = '';
            return;
        }

        fetch(`https://api-adresse.data.gouv.fr/search/?q=${value}&limit=5`)
            .then(response => response.json())
            .then(data => {
                const features = data.features;

                dropdownContainer.classList.remove('hidden');
                dropdownResults.innerHTML = '';

                if (features && features.length > 0) {
                    features.forEach(feature => {
                        const li = document.createElement('li');
                        li.className = 'dropdown-item py-2 px-4 hover:bg-gray-100 cursor-pointer';
                        li.innerText = feature.properties.label;

                        li.addEventListener('click', () => {
                            // Mappage des données de l'API vers les champs du formulaire
                            const label = feature.properties.label.split(',');
                            const street = label[0].trim();
                            const city = feature.properties.city || feature.properties.context.split(', ')[0];
                            const postalCode = feature.properties.postcode;

                            addressInput.value = street;
                            postalCodeInput.value = postalCode;
                            cityInput.value = city;

                            dropdownContainer.classList.add('hidden');
                            dropdownResults.innerHTML = '';
                        });

                        dropdownResults.appendChild(li);
                    });
                } else {
                    dropdownResults.appendChild(noResult);
                }
            })
            .catch(() => {
                alert('Erreur API, veuillez réessayer !');
            });

        // Fermer la liste si l'utilisateur clique en dehors
        document.addEventListener('click', function (event) {
            if (!addressInput.contains(event.target) && !dropdownContainer.contains(event.target)) {
                dropdownContainer.classList.add('hidden');
                dropdownResults.innerHTML = '';
            }
        });
    }, 500));
});

function debounce(callback, delay) {
    let timer;
    return function () {
        const args = arguments;
        clearTimeout(timer);
        timer = setTimeout(() => {
            callback.apply(this, args);
        }, delay);
    };
}
