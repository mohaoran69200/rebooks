document.querySelectorAll('.toggle-password').forEach(function (toggleButton) {
    toggleButton.addEventListener('click', function () {
        // Trouve le champ de mot de passe et l'icône d'œil correspondants
        const passwordField = toggleButton.closest('.relative').querySelector('input');
        const eyeIcon = toggleButton.querySelector('svg');

        // Vérifie si le champ est masqué ou visible et bascule son affichage
        if (passwordField.type === "password") {
            passwordField.type = "text"; // Affiche le mot de passe en clair
            eyeIcon.innerHTML = '<path d="M12 4.5c-7.333 0-11 7.5-11 7.5s3.667 7.5 11 7.5 11-7.5 11-7.5-3.667-7.5-11-7.5z"/>' +
                '<circle cx="12" cy="12" r="3"/><line x1="3" y1="3" x2="21" y2="21" stroke="currentColor" stroke-width="2" />'; // Change l'icône pour montrer un œil barré
        } else {
            passwordField.type = "password"; // Cache le mot de passe
            eyeIcon.innerHTML = '<path d="M12 4.5c-7.333 0-11 7.5-11 7.5s3.667 7.5 11 7.5 11-7.5 11-7.5-3.667-7.5-11-7.5z"/>' +
                '<circle cx="12" cy="12" r="3"/>'; // Change l'icône pour montrer un œil normal
        }
    });
});
