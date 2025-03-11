document.addEventListener("turbo:load", function () {
    // Récupère le bouton du menu utilisateur et le menu lui-même
    const menuButton = document.getElementById("user-menu-button");
    const menu = document.getElementById("user-menu");

    // Vérifie si les éléments existent avant d'aller plus loin
    if (!menuButton || !menu) return;

    // Fonction pour afficher/masquer le menu
    function toggleMenu() {
        menu.classList.toggle("opacity-0"); // Rend le menu visible/invisible
        menu.classList.toggle("invisible"); // Permet d'éviter les clics involontaires
    }

    // Ajoute un événement sur le bouton pour ouvrir/fermer le menu
    menuButton.addEventListener("click", function (event) {
        event.stopPropagation(); // Empêche la fermeture immédiate lors du clic
        toggleMenu();
    });

    // Ferme le menu si on clique en dehors
    document.addEventListener("click", function (event) {
        if (!menu.contains(event.target) && !menuButton.contains(event.target)) {
            menu.classList.add("opacity-0", "invisible"); // Cache le menu
        }
    });

    // Empêche la propagation du clic à l'intérieur du menu (évite qu'il se ferme)
    menu.addEventListener("click", function (event) {
        event.stopPropagation();
    });
});