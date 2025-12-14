document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("clipForm");

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
            showError(form.titre, "Le titre du clip est obligatoire.");
            hasErrors = true;
        } else if (titre.length < 3 || titre.length > 100) {
            showError(form.titre, "Le titre doit faire entre 3 et 100 caractères.");
            hasErrors = true;
        }

        const url = form.url_video.value.trim();
        if (!url) {
            showError(form.url_video, "L'URL de la vidéo est obligatoire.");
            hasErrors = true;
        } else if (!isValidUrl(url)) {
            showError(form.url_video, "L'URL de la vidéo n'est pas valide.");
            hasErrors = true;
        }

        if (hasErrors) {
            e.preventDefault();
            const firstError = form.querySelector(".is-invalid");
            if (firstError) {
                firstError.scrollIntoView({ behavior: "smooth", block: "center" });
            }
        }
    });

    form.querySelectorAll("input, textarea").forEach(input => {
        input.addEventListener("input", function() {
            this.classList.remove("is-invalid");
            const error = this.parentElement.querySelector(".field-error");
            if (error) error.remove();
        });
    });

    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }
});
