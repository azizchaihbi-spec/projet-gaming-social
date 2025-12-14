document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("eventForm");

    function clearErrors() {
        form.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
        form.querySelectorAll(".field-error").forEach(el => el.remove());
    }

    function showError(field, message) {
        field.classList.add("is-invalid");
        const errorDiv = document.createElement("div");
        errorDiv.className = "field-error";
        errorDiv.textContent = message;
        field.parentElement.appendChild(errorDiv);
    }

    form.addEventListener("submit", function (e) {
        let hasErrors = false;
        clearErrors();

        const titre = form.titre.value.trim();
        if (!titre) {
            showError(form.titre, "Le titre de l'événement est obligatoire.");
            hasErrors = true;
        } else if (titre.length < 3 || titre.length > 100) {
            showError(form.titre, "Le titre doit faire entre 3 et 100 caractères.");
            hasErrors = true;
        }

        const theme = form.theme.value.trim();
        if (!theme) {
            showError(form.theme, "Le thème de l'événement est obligatoire.");
            hasErrors = true;
        } else if (theme.length < 2 || theme.length > 50) {
            showError(form.theme, "Le thème doit faire entre 2 et 50 caractères.");
            hasErrors = true;
        }

        if (!form.date_debut.value) {
            showError(form.date_debut, "La date de début est obligatoire.");
            hasErrors = true;
        }

        if (!form.date_fin.value) {
            showError(form.date_fin, "La date de fin est obligatoire.");
            hasErrors = true;
        } else if (form.date_debut.value && form.date_fin.value < form.date_debut.value) {
            showError(form.date_fin, "La date de fin doit être après la date de début.");
            hasErrors = true;
        }

        const description = form.description.value.trim();
        if (!description) {
            showError(form.description, "La description est obligatoire (min 10 caractères).");
            hasErrors = true;
        } else if (description.length < 10) {
            showError(form.description, "La description doit faire au moins 10 caractères.");
            hasErrors = true;
        } else if (description.length > 2000) {
            showError(form.description, "La description ne peut pas dépasser 2000 caractères.");
            hasErrors = true;
        }

        const obj = form.objectif.value.trim();
        if (obj === "") {
            showError(form.objectif, "L'objectif est obligatoire.");
            hasErrors = true;
        } else {
            const objNum = parseFloat(obj);
            if (isNaN(objNum)) {
                showError(form.objectif, "L'objectif doit être un nombre valide.");
                hasErrors = true;
            } else if (objNum <= 0) {
                showError(form.objectif, "L'objectif doit être supérieur à 0.");
                hasErrors = true;
            } else if (objNum > 1000000) {
                showError(form.objectif, "L'objectif ne peut pas dépasser 1,000,000 DT.");
                hasErrors = true;
            }
        }

        if (hasErrors) {
            e.preventDefault();
            const firstError = form.querySelector(".is-invalid");
            if (firstError) {
                firstError.scrollIntoView({ behavior: "smooth", block: "center" });
            }
        }
    });

    form.querySelectorAll("input, textarea, select").forEach(input => {
        input.addEventListener("input", function() {
            this.classList.remove("is-invalid");
            const error = this.parentElement.querySelector(".field-error");
            if (error) error.remove();
        });
    });
});