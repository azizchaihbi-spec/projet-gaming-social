// views/backoffice/admin/js/add-stream.js - Client-side validation
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("streamForm");
    if (!form) return;

    // Helper function to show/clear field-level error messages
    function showFieldError(fieldName, errorMessage) {
        const field = form[fieldName];
        if (!field) return;

        // Remove existing error message
        const existingError = field.parentElement.querySelector(".field-error");
        if (existingError) existingError.remove();

        // Add invalid class
        field.classList.add("is-invalid");

        // Create and append error message
        if (errorMessage) {
            const errorDiv = document.createElement("div");
            errorDiv.className = "field-error";
            errorDiv.textContent = errorMessage;
            field.parentElement.appendChild(errorDiv);
        }
    }

    function clearFieldError(fieldName) {
        const field = form[fieldName];
        if (!field) return;
        
        field.classList.remove("is-invalid");
        const existingError = field.parentElement.querySelector(".field-error");
        if (existingError) existingError.remove();
    }

    form.addEventListener("submit", function (e) {
        let hasErrors = false;

        // Reset all field errors
        form.querySelectorAll(".is-invalid").forEach(el => el.classList.remove("is-invalid"));
        form.querySelectorAll(".field-error").forEach(el => el.remove());

        // Streamer
        const id_streamer = form.id_streamer?.value || "";
        if (!id_streamer) {
            showFieldError("id_streamer", "Vous devez sélectionner un streamer.");
            hasErrors = true;
        }

        // Platform checkboxes
        const platformCheckboxes = document.querySelectorAll("input[name='streamer_platform[]']");
        const anyPlatformChecked = Array.from(platformCheckboxes).some(cb => cb.checked);
        if (!anyPlatformChecked && platformCheckboxes.length > 0) {
            const platformContainer = platformCheckboxes[0].closest(".mb-6") || platformCheckboxes[0].parentElement.parentElement;
            const existingError = platformContainer.querySelector(".field-error");
            if (existingError) existingError.remove();
            const errorDiv = document.createElement("div");
            errorDiv.className = "field-error";
            errorDiv.textContent = "Sélectionnez au moins une plateforme.";
            platformContainer.appendChild(errorDiv);
            hasErrors = true;
        }

        // Titre
        const titre = (form.titre?.value || "").trim();
        if (!titre) {
            showFieldError("titre", "Le titre du stream est obligatoire.");
            hasErrors = true;
        } else if (titre.length < 3 || titre.length > 100) {
            showFieldError("titre", "Le titre doit faire entre 3 et 100 caractères.");
            hasErrors = true;
        }

        // URL (required and must be valid)
        const url = (form.url?.value || "").trim();
        if (!url) {
            showFieldError("url", "L'URL du stream est obligatoire.");
            hasErrors = true;
        } else {
            try {
                const u = new URL(url);
                if (!u.protocol.startsWith("http")) {
                    throw new Error("Invalid protocol");
                }
            } catch (_) {
                showFieldError("url", "L'URL du stream doit être valide (http/https).");
                hasErrors = true;
            }
        }

        // Dates
        const debut = form.date_debut?.value || "";
        const fin = form.date_fin?.value || "";
        if (!debut) {
            showFieldError("date_debut", "La date de début est obligatoire.");
            hasErrors = true;
        }
        if (!fin) {
            showFieldError("date_fin", "La date de fin est obligatoire.");
            hasErrors = true;
        }
        if (debut && fin && fin < debut) {
            showFieldError("date_fin", "La date de fin doit être après la date de début.");
            hasErrors = true;
        }

        // Statut
        const statut = form.statut?.value || "";
        const allowed = ["planifie", "en_cours", "termine", "annule"];
        if (!allowed.includes(statut)) {
            showFieldError("statut", "Le statut sélectionné est invalide.");
            hasErrors = true;
        }

        // Don total
        const donStr = (form.don_total?.value ?? "").toString().trim();
        if (donStr !== "") {
            const donNum = parseFloat(donStr);
            if (isNaN(donNum)) {
                showFieldError("don_total", "Le total des dons doit être un nombre valide.");
                hasErrors = true;
            } else if (donNum < 0) {
                showFieldError("don_total", "Le total des dons ne peut pas être négatif.");
                hasErrors = true;
            } else if (donNum > 100000000) {
                showFieldError("don_total", "Le total des dons ne peut pas dépasser 100,000,000 DT.");
                hasErrors = true;
            }
        }

        if (hasErrors) {
            e.preventDefault();
            console.log('Form validation failed - preventing submission');
            const firstError = form.querySelector(".is-invalid");
            if (firstError) {
                firstError.scrollIntoView({ behavior: "smooth", block: "center" });
                console.log('First error field:', firstError.name);
            }
        } else {
            console.log('Form validation passed - submitting');
        }
    });

    // Remove error when typing/changing
    form.querySelectorAll("input, select").forEach(input => {
        input.addEventListener("input", function() {
            clearFieldError(input.name);
        });
        input.addEventListener("change", function() {
            clearFieldError(input.name);
        });
    });

    // Handle platform checkboxes
    const platformCheckboxes = document.querySelectorAll("input[name='streamer_platform[]']");
    platformCheckboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function() {
            const platformContainer = this.closest(".mb-6") || this.parentElement.parentElement;
            const existingError = platformContainer.querySelector(".field-error");
            if (existingError) existingError.remove();
        });
    });
});
