// Menu toggle
    $('.menu-trigger').click(function(e) {
        e.preventDefault();
        $('body').toggleClass('menu-is-open');
    });
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.main-nav, .menu-trigger').length) {
            $('body').removeClass('menu-is-open');
        }
    });
    $(window).on('scroll', function() {
        $('body').removeClass('menu-is-open');
    });

    // Header show/hide on scroll
    $(document).ready(function () {
        var lastScrollTop = 0;
        var $header = $('#mainHeader');
        $(window).on('scroll', function () {
            var st = $(this).scrollTop();
            if (st > lastScrollTop && st > 100) {
                $header.css('transform', 'translateY(-100%)');
            } else {
                $header.css('transform', 'translateY(0)');
            }
            lastScrollTop = st;
        });
    });

    // Validation formulaire Don
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

    // Animation cards au chargement
    $('.challenge-card').each(function(i) {
        $(this).delay(200 * i).queue(function(next){
            $(this).addClass('animated fadeInUp');
            next();
        });
    });
// Toggle menu open class
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

    // Show/hide header on scroll: hide scroll down, show scroll up
    $(document).ready(function() {
        var lastScrollTop = 0;
        var $header = $('#mainHeader');

        $(window).on('scroll', function() {
            var st = $(this).scrollTop();

            if (st > lastScrollTop && st > 100) {
                // Scroll vers bas, cacher header
                $header.css({
                    'transform': 'translateY(-100%)',
                    'transition': 'transform 0.4s ease'
                });
            } else {
                // Scroll vers haut, montrer header
                $header.css({
                    'transform': 'translateY(0)',
                    'transition': 'transform 0.4s ease'
                });
            }
            lastScrollTop = st;
        });
    });

    // Initialisation Owl Carousel pour associations
    $(document).ready(function () {
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