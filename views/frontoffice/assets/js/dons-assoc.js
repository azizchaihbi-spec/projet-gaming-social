$(document).ready(function() {
    // Menu toggle functionality
    $('.menu-trigger').click(function(e) {
        e.preventDefault();
        $('body').toggleClass('menu-is-open');
    });

    // Close menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.main-nav, .menu-trigger').length) {
            $('body').removeClass('menu-is-open');
        }
    });

    // Close menu on scroll
    $(window).on('scroll', function() {
        $('body').removeClass('menu-is-open');
    });

    // Header show/hide on scroll
    var lastScrollTop = 0;
    var $header = $('#mainHeader');
    $(window).on('scroll', function () {
        var st = $(this).scrollTop();
        if (st > lastScrollTop && st > 100) {
            // Scroll down, hide header
            $header.css({
                'transform': 'translateY(-100%)',
                'transition': 'transform 0.4s ease'
            });
        } else {
            // Scroll up, show header
            $header.css({
                'transform': 'translateY(0)',
                'transition': 'transform 0.4s ease'
            });
        }
        lastScrollTop = st;
    });

    // Initialisation Owl Carousel pour associations
    $('#assoc-slider').owlCarousel({
        loop: true,
        margin: 20,
        nav: true,
        navText: ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>'],
        responsive: {
            0: { items: 1 },
            768: { items: 2 },
            992: { items: 3 }
        }
    });

    // Animation remplissage barre de progression dans challenges
    $('.progression-fill').each(function () {
        const width = $(this).attr('style');
        $(this).css('width', '0');
        $(this).animate(
            { width: width.replace('width: ', '') },
            1500
        );
    });

    // Animation cards au chargement
    $('.challenge-card').each(function(i) {
        $(this).delay(200 * i).queue(function(next){
            $(this).addClass('animated fadeInUp');
            next();
        });
    });

    // Validation formulaire Don (jQuery version)
    $('#formDon').submit(function(e) {
        e.preventDefault();

        const montant = $('#montant').val().trim();
        const association = $('#association').val();

        if (!montant) {
            alert('Veuillez entrer un montant pour votre don.');
            $('#montant').focus();
            return;
        }
        if (isNaN(montant) || montant <= 0) {
            alert('Veuillez entrer un montant valide supérieur à zéro.');
            $('#montant').focus();
            return;
        }
        if (!association) {
            alert('Veuillez sélectionner une association.');
            $('#association').focus();
            return;
        }

        alert(`Super ! Votre don de ${montant}€ va transformer des vies. Merci, héros du gaming ! ❤️🎮`);
        $('#modalDon').modal('hide');
        this.reset();
    });

    // Validation formulaire Challenge
    $('#formChallenge').submit(function(e) {
        e.preventDefault();

        const challengeAssoc = $('#challenge-assoc').val();
        const defi = $('#defi').val().trim();
        const objectif = $('#objectif').val().trim();
        const recompense = $('#recompense').val().trim();

        if (!challengeAssoc) {
            alert('Veuillez sélectionner une association.');
            $('#challenge-assoc').focus();
            return;
        }
        if (!defi) {
            alert('Veuillez saisir la description du défi.');
            $('#defi').focus();
            return;
        }
        if (!objectif || isNaN(objectif) || objectif < 10) {
            alert('Veuillez saisir un objectif de dons valide (minimum 10€).');
            $('#objectif').focus();
            return;
        }
        if (!recompense) {
            alert('Veuillez indiquer la récompense.');
            $('#recompense').focus();
            return;
        }

        alert(`Défi "${defi}" lancé ! Préparez-vous pour l'aventure solidaire – que le stream commence ! 🚀`);
        $('#modalChallenge').modal('hide');
        this.reset();
    });
});

// Advanced form validation for donations (vanilla JS version for compatibility)
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

    // Advanced form validation with email support
    form.addEventListener("submit", async function (e) {
        e.preventDefault();
        clearErrors();

        const email = form.email ? form.email.value.trim() : "";
        const montant = form.montant.value.trim();
        const association = form.id_association ? form.id_association.value : form.association.value;

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
            showError(form.id_association ? "id_association" : "association");
            hasError = true;
            return;
        }

        // S'il y a des erreurs → on arrête
        if (hasError) {
            return;
        }

        // Si pas d'erreurs, continuer avec le traitement du formulaire
        // (cette partie peut être étendue selon les besoins)
    });
});

// Association Card Click - Open Modal with Details
document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll('.solidaire-card.clickable-card');
    const modal = document.getElementById('modalAssociation');
    
    if (!modal || cards.length === 0) return;
    
    // Bootstrap modal instance
    let bsModal = null;
    if (typeof bootstrap !== 'undefined') {
        bsModal = new bootstrap.Modal(modal);
    }
    
    cards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Don't open modal if clicking on the donate button
            if (e.target.closest('.neon-btn')) return;
            
            // Get data from card attributes
            const id = this.dataset.id;
            const name = this.dataset.name;
            const description = this.dataset.description;
            const total = this.dataset.total;
            const donateurs = this.dataset.donateurs;
            const email = this.dataset.email;
            const website = this.dataset.website;
            
            // Populate modal
            document.getElementById('modal-assoc-name').textContent = name;
            document.getElementById('modal-assoc-description').textContent = description;
            document.getElementById('modal-assoc-total').textContent = total + '€';
            document.getElementById('modal-assoc-donateurs').textContent = donateurs;
            document.getElementById('modal-assoc-email').textContent = email;
            
            // Website section
            const websiteSection = document.getElementById('website-section');
            const websiteLink = document.getElementById('modal-assoc-website');
            if (website && website.trim() !== '') {
                websiteSection.style.display = 'block';
                websiteLink.href = website;
                websiteLink.textContent = website;
            } else {
                websiteSection.style.display = 'none';
            }
            
            // Update donate button link
            document.getElementById('modal-donate-btn').href = 'don.php?association=' + id;
            
            // Open modal
            if (bsModal) {
                bsModal.show();
            } else if (typeof $ !== 'undefined') {
                $('#modalAssociation').modal('show');
            }
        });
    });
});
