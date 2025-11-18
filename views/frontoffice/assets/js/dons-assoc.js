document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formDon");
    if (!form) return;

    // Fonction pour enlever toutes les erreurs
    function clearErrors() {
        form.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
        form.querySelectorAll(".invalid-feedback").forEach(el => el.style.display = "none");
    }

    // Fonction pour afficher une erreur sous un champ
    function showError(fieldName, show = true) {
        const field = form.elements[fieldName];
        if (!field) return;
        field.classList.toggle("is-invalid", show);
        const feedback = field.parentNode.querySelector(".invalid-feedback");
        if (feedback) feedback.style.display = show ? "block" : "none";
        if (show) field.focus();
    }

    form.addEventListener("submit", async function (e) {
        e.preventDefault();
        clearErrors();

        const email = form.email.value.trim();
        const montant = form.montant.value.trim();
        const association = form.id_association.value;

        let hasError = false;

        // 1. Email : facultatif mais valide si rempli
        if (email !== "" && !/^\S+@\S+\.\S+$/.test(email)) {
            showError("email");
            hasError = true;
            return;
        }

        // 2. Montant obligatoire et > 0
        if (!montant || isNaN(montant) || parseFloat(montant) <= 0) {
            showError("montant");
            hasError = true;
            return;
        }

        // 3. Association obligatoire
        if (!association || association === "") {
            showError("id_association");
            hasError = true;
            return;
        }

        // S'il y a des erreurs → on arrête
        if (hasError) {
            return;
        }

        
    });
});