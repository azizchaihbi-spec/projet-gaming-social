<?php 
require_once '../../config/config.php'; 

// Récupérer les dons récents depuis la base de données
try {
    $stmt = $pdo->query("
        SELECT d.*, a.name as association_name 
        FROM don d 
        JOIN association a ON d.id_association = a.id_association 
        ORDER BY d.date_don DESC 
        LIMIT 10
    ");
    $dons_recents = $stmt->fetchAll();
} catch (Exception $e) {
    $dons_recents = [];
    error_log("Erreur lors de la récupération des dons récents: " . $e->getMessage());
}

// Récupérer les 3 derniers challenges
try {
    $stmt = $pdo->query("
        SELECT c.*, a.name as association_name 
        FROM challenge c 
        JOIN association a ON c.id_association = a.id_association 
        ORDER BY c.id_challenge DESC 
        LIMIT 3
    ");
    $challenges_epiques = $stmt->fetchAll();
} catch (Exception $e) {
    $challenges_epiques = [];
    error_log("Erreur lors de la récupération des challenges épiques: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Play to Help - Dons & Challenges</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/fontawesome.css" />
    <link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css" />
    <link rel="stylesheet" href="assets/css/owl.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <link rel="stylesheet" href="assets/css/dons-assoc.css" />
    
    <style>
        /* ===== VARIABLES & RESET ===== */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --success-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --dark-bg: rgba(20, 20, 35, 0.95);
            --card-bg: rgba(30, 30, 50, 0.8);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glow-purple: 0 0 40px rgba(102, 126, 234, 0.6);
            --glow-cyan: 0 0 40px rgba(0, 242, 254, 0.6);
            --glow-pink: 0 0 40px rgba(245, 87, 108, 0.6);
        }

        body {
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            background-attachment: fixed;
            position: relative;
            overflow-x: hidden;
        }

        /* ===== PARTICULES GAMING FLOTTANTES ===== */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(245, 87, 108, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(0, 242, 254, 0.15) 0%, transparent 50%);
            animation: particleFloat 20s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }

        /* Effet de grille cyberpunk */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(102, 126, 234, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(102, 126, 234, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes particleFloat {
            0%, 100% { 
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }
            33% { 
                transform: translate(30px, -30px) scale(1.2);
                opacity: 0.8;
            }
            66% { 
                transform: translate(-20px, 20px) scale(0.9);
                opacity: 0.6;
            }
        }

        @keyframes gridMove {
            0% {
                transform: translateY(0);
            }
            100% {
                transform: translateY(50px);
            }
        }

        /* ===== HERO BANNER CRÉATIF ===== */
        .hero-banner {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(102, 126, 234, 0.3);
            border-radius: 30px;
            padding: 60px 40px;
            margin: 40px auto;
            position: relative;
            overflow: hidden;
            box-shadow: var(--glow-purple), inset 0 0 60px rgba(102, 126, 234, 0.1);
            animation: heroGlow 3s ease-in-out infinite alternate;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 400px;
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(102, 126, 234, 0.2), transparent 30%);
            animation: rotate 8s linear infinite;
        }

        .hero-banner::after {
            content: '';
            position: absolute;
            inset: 2px;
            background: rgba(20, 20, 35, 0.7);
            border-radius: 28px;
            z-index: 1;
        }

        .hero-banner > * {
            position: relative;
            z-index: 2;
        }

        .hero-banner h2 {
            font-size: 3rem;
            font-weight: 900;
            color: #fff !important;
            margin-bottom: 20px;
            text-shadow: 
                0 0 10px #fff,
                0 0 20px #fff,
                0 0 30px #ff1744,
                0 0 40px #ff1744,
                0 0 50px #ff1744,
                0 0 60px #ff1744,
                0 0 70px #ff1744;
            animation: textShine 3s ease-in-out infinite;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        @keyframes textShine {
            0%, 100% { filter: brightness(1); }
            50% { filter: brightness(1.3); }
        }

        @keyframes heroGlow {
            0% { box-shadow: 0 0 30px rgba(102, 126, 234, 0.4), inset 0 0 60px rgba(102, 126, 234, 0.1); }
            100% { box-shadow: 0 0 60px rgba(102, 126, 234, 0.8), inset 0 0 80px rgba(102, 126, 234, 0.2); }
        }

        @keyframes rotate {
            100% { transform: rotate(360deg); }
        }

        .hero-banner p {
            font-size: 1.3rem;
            color: #fff !important;
            margin-bottom: 30px;
            text-align: center;
            text-shadow: 
                0 0 10px #fff,
                0 0 20px #fff,
                0 0 30px #ff1744,
                0 0 40px #ff1744;
            position: relative;
            z-index: 10;
        }

        .hero-banner .main-button {
            text-align: center;
        }

        /* ===== BOUTONS GAMING STYLE ===== */
        .btn-donate {
            background: var(--success-gradient);
            border: none;
            color: white;
            font-weight: 700;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
            box-shadow: 
                0 10px 30px rgba(67, 233, 123, 0.4),
                0 0 0 2px rgba(67, 233, 123, 0.2);
            cursor: pointer;
        }

        /* Effet de vague au clic */
        .btn-donate::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-donate:hover::before {
            width: 300px;
            height: 300px;
        }

        /* Effet de scan horizontal */
        .btn-donate::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.3), 
                transparent);
            animation: scanButton 3s linear infinite;
        }

        @keyframes scanButton {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }

        .btn-donate:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 
                0 15px 50px rgba(67, 233, 123, 0.6),
                0 0 0 3px rgba(67, 233, 123, 0.4),
                0 0 0 6px rgba(56, 249, 215, 0.2);
            animation: buttonPulse 0.5s ease-in-out;
        }

        @keyframes buttonPulse {
            0%, 100% {
                transform: translateY(-5px) scale(1.05);
            }
            50% {
                transform: translateY(-5px) scale(1.08);
            }
        }

        .btn-donate:active {
            transform: translateY(-2px) scale(1);
            box-shadow: 0 5px 20px rgba(67, 233, 123, 0.4);
        }

        .btn-challenge {
            background: var(--primary-gradient);
            border: none;
            color: white;
            font-weight: 700;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
            box-shadow: 
                0 10px 30px rgba(102, 126, 234, 0.4),
                0 0 0 2px rgba(102, 126, 234, 0.2);
            cursor: pointer;
        }

        /* Effet de vague au clic */
        .btn-challenge::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-challenge:hover::before {
            width: 300px;
            height: 300px;
        }

        /* Effet de scan diagonal */
        .btn-challenge::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, 
                transparent, 
                rgba(255, 255, 255, 0.2), 
                transparent);
            animation: scanDiagonal 3s linear infinite;
        }

        @keyframes scanDiagonal {
            0% {
                transform: translateX(-100%) translateY(-100%);
            }
            100% {
                transform: translateX(100%) translateY(100%);
            }
        }

        .btn-challenge:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 
                0 15px 50px rgba(102, 126, 234, 0.6),
                0 0 0 3px rgba(102, 126, 234, 0.4),
                0 0 0 6px rgba(118, 75, 162, 0.2);
            animation: buttonPulseChallenge 0.5s ease-in-out;
        }

        @keyframes buttonPulseChallenge {
            0%, 100% {
                transform: translateY(-5px) scale(1.05);
            }
            50% {
                transform: translateY(-5px) scale(1.08);
            }
        }

        .btn-challenge:active {
            transform: translateY(-2px) scale(1);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        /* ===== HEADER SECTION ===== */
        .header-section h4 {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #4facfe, #00f2fe, #667eea);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            display: inline-block;
            padding-bottom: 15px;
        }

        .header-section h4::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--accent-gradient);
            border-radius: 2px;
            box-shadow: 0 0 20px rgba(79, 172, 254, 0.6);
        }

        /* ===== SECTIONS CRÉATIVES ===== */
        .section-header-creative {
            text-align: center;
            margin: 60px 0 40px;
            position: relative;
        }

        .section-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            animation: iconFloat 3s ease-in-out infinite;
            display: inline-block;
            filter: drop-shadow(0 0 20px rgba(67, 233, 123, 0.6));
        }

        @keyframes iconFloat {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(10deg);
            }
        }

        .section-title {
            font-size: 3rem;
            font-weight: 900;
            background: linear-gradient(135deg, #43e97b, #38f9d7, #00f2fe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            letter-spacing: 3px;
            text-shadow: 0 0 30px rgba(67, 233, 123, 0.5);
        }

        .section-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.2rem;
            font-style: italic;
            margin-bottom: 20px;
        }

        .section-line {
            width: 200px;
            height: 4px;
            background: linear-gradient(90deg, transparent, #43e97b, #38f9d7, transparent);
            margin: 0 auto;
            border-radius: 2px;
            box-shadow: 0 0 20px rgba(67, 233, 123, 0.6);
            animation: lineGlow 2s ease-in-out infinite;
        }

        @keyframes lineGlow {
            0%, 100% {
                box-shadow: 0 0 20px rgba(67, 233, 123, 0.6);
            }
            50% {
                box-shadow: 0 0 40px rgba(67, 233, 123, 1);
            }
        }

        /* ===== GRIDS CRÉATIVES ===== */
        .dons-carousel-wrapper {
            position: relative;
            padding: 20px 0;
        }

        .dons-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 40px;
            perspective: 1000px;
        }

        .challenges-carousel-wrapper {
            position: relative;
            padding: 20px 0;
        }

        .challenges-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 40px;
            perspective: 1000px;
        }

        /* ===== CARTES DON GLASSMORPHISM GAMING 3D ===== */
        .don-item {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 2px solid rgba(67, 233, 123, 0.3);
            border-radius: 25px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            transform-style: preserve-3d;
            perspective: 1000px;
        }

        /* Effet de scan laser */
        .don-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(67, 233, 123, 0.3), 
                rgba(56, 249, 215, 0.3),
                transparent);
            transition: left 0.6s;
            z-index: 1;
        }

        .don-item:hover::before {
            left: 100%;
        }

        /* Effet de glitch sur hover */
        .don-item:hover {
            transform: translateY(-15px) scale(1.03);
            border-color: rgba(67, 233, 123, 0.8);
            box-shadow: 
                0 20px 60px rgba(67, 233, 123, 0.4), 
                var(--glow-cyan),
                0 0 0 1px rgba(67, 233, 123, 0.5),
                0 0 0 3px rgba(56, 249, 215, 0.3);
            animation: glitchCard 0.3s ease-in-out;
        }

        @keyframes glitchCard {
            0%, 100% {
                transform: translateY(-15px) scale(1.03);
            }
            25% {
                transform: translateY(-15px) scale(1.03) translateX(-2px);
            }
            75% {
                transform: translateY(-15px) scale(1.03) translateX(2px);
            }
        }

        /* Coins lumineux */
        .don-item::after {
            content: '';
            position: absolute;
            top: 10px;
            right: 10px;
            width: 8px;
            height: 8px;
            background: rgba(67, 233, 123, 0.8);
            border-radius: 50%;
            box-shadow: 
                0 0 10px rgba(67, 233, 123, 1),
                0 0 20px rgba(67, 233, 123, 0.8),
                0 0 30px rgba(67, 233, 123, 0.6);
            animation: cornerPulse 2s ease-in-out infinite;
            z-index: 2;
        }

        @keyframes cornerPulse {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.5;
                transform: scale(1.5);
            }
        }

        .don-item .thumb {
            position: relative;
            overflow: hidden;
            height: 200px;
        }

        .don-item .thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .don-item:hover .thumb img {
            transform: scale(1.2) rotate(3deg);
        }

        .don-item .hover-effect {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(67, 233, 123, 0.9), rgba(56, 249, 215, 0.9));
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .don-item:hover .hover-effect {
            opacity: 1;
        }

        .don-item .hover-effect ul li {
            color: white;
            font-size: 1.3rem;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            margin: 5px 0;
        }

        .don-item .down-content {
            padding: 20px;
            background: var(--card-bg);
        }

        .don-item .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid rgba(67, 233, 123, 0.6);
            margin-bottom: 15px;
            box-shadow: 0 0 20px rgba(67, 233, 123, 0.4);
        }

        .don-item .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .don-item .down-content span {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            display: block;
            margin-bottom: 8px;
        }

        .don-item .down-content h4 {
            color: white;
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
        }

        /* ===== CARTES CHALLENGE FUTURISTES GAMING 3D ===== */
        .challenge-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 2px solid rgba(102, 126, 234, 0.3);
            border-radius: 25px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            transform-style: preserve-3d;
            perspective: 1000px;
        }

        /* Effet de scan holographique */
        .challenge-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(102, 126, 234, 0.3),
                rgba(118, 75, 162, 0.3),
                transparent);
            transition: left 0.6s;
            z-index: 1;
        }

        .challenge-card:hover::before {
            left: 100%;
        }

        /* Effet de lévitation avec glitch */
        .challenge-card:hover {
            transform: translateY(-15px) scale(1.03);
            border-color: rgba(102, 126, 234, 0.8);
            box-shadow: 
                0 20px 60px rgba(102, 126, 234, 0.4), 
                var(--glow-purple),
                0 0 0 1px rgba(102, 126, 234, 0.5),
                0 0 0 3px rgba(118, 75, 162, 0.3);
            animation: glitchChallenge 0.3s ease-in-out;
        }

        @keyframes glitchChallenge {
            0%, 100% {
                transform: translateY(-15px) scale(1.03);
            }
            25% {
                transform: translateY(-15px) scale(1.03) translateX(2px);
            }
            75% {
                transform: translateY(-15px) scale(1.03) translateX(-2px);
            }
        }

        /* Indicateur de niveau (coins lumineux) */
        .challenge-card::after {
            content: '';
            position: absolute;
            top: 10px;
            left: 10px;
            width: 8px;
            height: 8px;
            background: rgba(102, 126, 234, 0.8);
            border-radius: 50%;
            box-shadow: 
                0 0 10px rgba(102, 126, 234, 1),
                0 0 20px rgba(102, 126, 234, 0.8),
                0 0 30px rgba(102, 126, 234, 0.6);
            animation: cornerPulseChallenge 2s ease-in-out infinite;
            z-index: 2;
        }

        @keyframes cornerPulseChallenge {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.5;
                transform: scale(1.5);
            }
        }

        .challenge-card .thumb {
            position: relative;
            overflow: hidden;
            height: 200px;
        }

        .challenge-card .thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .challenge-card:hover .thumb img {
            transform: scale(1.2) rotate(-3deg);
        }

        .challenge-card .hover-effect {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9));
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.4s ease;
            padding: 20px;
        }

        .challenge-card:hover .hover-effect {
            opacity: 1;
        }

        .challenge-card .hover-effect h6 {
            color: white;
            font-size: 1.2rem;
            font-weight: 700;
            text-align: center;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }

        .challenge-card .down-content {
            padding: 25px;
            background: var(--card-bg);
        }

        .challenge-card .down-content h4 {
            color: white;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .challenge-card .down-content p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 20px;
            font-size: 1rem;
        }

        /* ===== BARRE DE PROGRESSION NÉON ===== */
        .progress-container {
            margin: 20px 0 10px;
        }

        .progress-bar-bg {
            width: 100%;
            height: 12px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
            border-radius: 20px;
            position: relative;
            transition: width 0.6s ease;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.8);
            animation: progressGlow 2s ease-in-out infinite;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: progressShine 2s linear infinite;
        }

        @keyframes progressGlow {
            0%, 100% { box-shadow: 0 0 15px rgba(102, 126, 234, 0.6); }
            50% { box-shadow: 0 0 30px rgba(102, 126, 234, 1); }
        }

        @keyframes progressShine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .progress-text {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* ===== MODALS FUTURISTES ===== */
        .modal-content {
            background: linear-gradient(135deg, rgba(15, 12, 41, 0.98), rgba(48, 43, 99, 0.98));
            backdrop-filter: blur(30px);
            border: 3px solid transparent;
            border-radius: 30px;
            box-shadow: 
                0 30px 80px rgba(0, 0, 0, 0.7),
                0 0 0 1px rgba(102, 126, 234, 0.5),
                inset 0 0 60px rgba(102, 126, 234, 0.1);
            position: relative;
            overflow: hidden;
        }

        .modal-content::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, #667eea, #764ba2, #f093fb, #667eea);
            border-radius: 30px;
            z-index: -1;
            animation: borderRotate 4s linear infinite;
            background-size: 300% 300%;
        }

        @keyframes borderRotate {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Modal Don - Thème Cyan/Turquoise Immersif */
        #modalDon .modal-content {
            background: linear-gradient(135deg, rgba(6, 23, 41, 0.98), rgba(13, 71, 161, 0.98), rgba(0, 150, 199, 0.95));
        }

        #modalDon .modal-content::before {
            background: linear-gradient(135deg, #00d4ff, #0099ff, #00f2fe, #00d4ff);
            background-size: 300% 300%;
        }

        #modalDon .modal-header {
            border-bottom: 2px solid rgba(0, 212, 255, 0.5);
            padding: 30px;
            background: rgba(0, 212, 255, 0.08);
        }

        #modalDon .modal-title {
            font-size: 2rem;
            font-weight: 900;
            color: #fff;
            text-shadow: 
                0 0 10px rgba(0, 212, 255, 1),
                0 0 20px rgba(0, 212, 255, 0.8),
                0 0 30px rgba(0, 212, 255, 0.6),
                0 0 40px rgba(0, 153, 255, 0.4);
        }

        #modalDon .form-control,
        #modalDon .form-select {
            background: rgba(0, 212, 255, 0.1);
            border: 2px solid rgba(0, 212, 255, 0.4);
            border-radius: 15px;
            color: white;
            padding: 14px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        #modalDon .form-control:focus,
        #modalDon .form-select:focus {
            background: rgba(0, 212, 255, 0.18);
            border-color: rgba(0, 212, 255, 1);
            box-shadow: 0 0 30px rgba(0, 212, 255, 0.6), 0 0 15px rgba(0, 242, 254, 0.4);
            color: white;
            outline: none;
        }

        #modalDon .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        #modalDon .btn-success {
            background: linear-gradient(135deg, #00d4ff, #0099ff, #00f2fe);
            border: none;
            color: white;
            font-weight: 800;
            padding: 18px 40px;
            border-radius: 50px;
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            box-shadow: 
                0 10px 40px rgba(0, 212, 255, 0.6),
                0 0 20px rgba(0, 242, 254, 0.4);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        #modalDon .btn-success::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        #modalDon .btn-success:hover::before {
            width: 400px;
            height: 400px;
        }

        #modalDon .btn-success:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 
                0 15px 60px rgba(0, 212, 255, 0.9),
                0 0 40px rgba(0, 242, 254, 0.7);
        }

        /* Modal Challenge - Thème Violet/Rose */
        #modalChallenge .modal-content {
            background: linear-gradient(135deg, rgba(41, 12, 41, 0.98), rgba(99, 43, 99, 0.98));
        }

        #modalChallenge .modal-content::before {
            background: linear-gradient(135deg, #667eea, #764ba2, #f093fb, #667eea);
            background-size: 300% 300%;
        }

        .modal-header {
            border-bottom: 2px solid rgba(102, 126, 234, 0.4);
            padding: 30px;
            background: rgba(102, 126, 234, 0.05);
        }

        .modal-title {
            font-size: 2rem;
            font-weight: 900;
            color: #fff;
            text-shadow: 
                0 0 10px rgba(102, 126, 234, 1),
                0 0 20px rgba(102, 126, 234, 0.8),
                0 0 30px rgba(102, 126, 234, 0.6);
        }

        .modal-body {
            padding: 35px;
        }

        .form-control, .form-select {
            background: rgba(102, 126, 234, 0.08);
            border: 2px solid rgba(102, 126, 234, 0.4);
            border-radius: 15px;
            color: white;
            padding: 14px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(102, 126, 234, 0.15);
            border-color: rgba(102, 126, 234, 1);
            box-shadow: 0 0 25px rgba(102, 126, 234, 0.5);
            color: white;
            outline: none;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-select option {
            background: #1a1a2e;
            color: white;
        }

        .form-label {
            color: #fff;
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 1.05rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .text-danger {
            color: #ff1744 !important;
            text-shadow: 0 0 10px rgba(255, 23, 68, 0.6);
        }

        .text-muted {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        small.text-muted {
            font-size: 0.85rem;
            display: block;
            margin-top: 5px;
        }

        .alert {
            border-radius: 15px;
            border: 2px solid;
            padding: 15px 20px;
            font-weight: 600;
        }

        .alert-info {
            background: rgba(79, 172, 254, 0.15);
            border-color: rgba(79, 172, 254, 0.5);
            color: #4facfe;
        }

        .alert-success {
            background: rgba(67, 233, 123, 0.15);
            border-color: rgba(67, 233, 123, 0.5);
            color: #43e97b;
        }

        .alert-danger {
            background: rgba(255, 23, 68, 0.15);
            border-color: rgba(255, 23, 68, 0.5);
            color: #ff1744;
        }

        .invalid-feedback {
            color: #ff1744;
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .btn-close:hover {
            opacity: 1;
            transform: rotate(90deg);
        }

        .border-purple {
            border-color: #a78bfa !important;
        }

        .border-success {
            border-color: #43e97b !important;
        }

        /* ===== ANIMATIONS GAMING SPECTACULAIRES ===== */
        
        /* Animation d'entrée explosive */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9) rotateX(20deg);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1) rotateX(0deg);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-100px) rotate(-5deg);
            }
            to {
                opacity: 1;
                transform: translateX(0) rotate(0deg);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px) rotate(5deg);
            }
            to {
                opacity: 1;
                transform: translateX(0) rotate(0deg);
            }
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.5) rotate(180deg);
            }
            to {
                opacity: 1;
                transform: scale(1) rotate(0deg);
            }
        }

        /* Appliquer les animations aux cartes */
        .don-item {
            animation: slideInLeft 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) backwards;
        }

        .challenge-card {
            animation: slideInRight 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) backwards;
        }

        .don-item:nth-child(1) { animation-delay: 0.1s; }
        .don-item:nth-child(2) { animation-delay: 0.2s; }
        .challenge-card:nth-child(3) { animation-delay: 0.3s; }
        .challenge-card:nth-child(4) { animation-delay: 0.4s; }

        /* Animation de pulsation pour les boutons */
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 10px 30px rgba(67, 233, 123, 0.4);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 15px 50px rgba(67, 233, 123, 0.7);
            }
        }

        .btn-donate, .btn-challenge {
            animation: pulse 2s ease-in-out infinite;
        }

        /* Particules flottantes animées */
        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0) rotate(0deg);
            }
            25% {
                transform: translateY(-20px) translateX(10px) rotate(5deg);
            }
            50% {
                transform: translateY(-40px) translateX(-10px) rotate(-5deg);
            }
            75% {
                transform: translateY(-20px) translateX(10px) rotate(5deg);
            }
        }

        /* Effet de brillance sur les cartes */
        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }
            100% {
                background-position: 1000px 0;
            }
        }

        .don-item::after, .challenge-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.1),
                transparent
            );
            animation: shimmer 3s infinite;
            pointer-events: none;
        }

        /* Animation de rotation pour les avatars */
        @keyframes rotateAvatar {
            0% {
                transform: rotate(0deg) scale(1);
            }
            50% {
                transform: rotate(180deg) scale(1.1);
            }
            100% {
                transform: rotate(360deg) scale(1);
            }
        }

        .don-item:hover .avatar {
            animation: rotateAvatar 1s ease-in-out;
        }

        /* Effet de glow pulsant sur les bordures */
        @keyframes glowPulse {
            0%, 100% {
                box-shadow: 0 0 20px rgba(102, 126, 234, 0.4);
            }
            50% {
                box-shadow: 0 0 40px rgba(102, 126, 234, 0.8), 0 0 60px rgba(102, 126, 234, 0.6);
            }
        }

        .challenge-card {
            animation: slideInRight 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) backwards, glowPulse 3s ease-in-out infinite;
        }

        /* Animation de texte néon clignotant */
        @keyframes neonBlink {
            0%, 100% {
                text-shadow: 
                    0 0 10px #fff,
                    0 0 20px #fff,
                    0 0 30px #ff1744,
                    0 0 40px #ff1744;
            }
            50% {
                text-shadow: 
                    0 0 5px #fff,
                    0 0 10px #fff,
                    0 0 15px #ff1744,
                    0 0 20px #ff1744,
                    0 0 25px #ff1744,
                    0 0 30px #ff1744,
                    0 0 35px #ff1744;
            }
        }

        .hero-banner h2 {
            animation: textShine 3s ease-in-out infinite, neonBlink 2s ease-in-out infinite;
        }

        /* Animation de vague pour les modals */
        @keyframes wave {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .modal.show .modal-dialog {
            animation: zoomIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Effet de rebond sur les inputs focus */
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
        }

        .form-control:focus, .form-select:focus {
            animation: bounce 0.5s ease;
        }

        /* Animation de chargement pour les boutons */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .spinner-border {
            animation: spin 0.75s linear infinite;
        }

        /* Effet de particules sur hover des cartes */
        .don-item:hover, .challenge-card:hover {
            animation: float 2s ease-in-out infinite;
        }

        /* Animation de progression de la barre */
        @keyframes fillProgress {
            from {
                width: 0%;
            }
        }

        .progress-fill {
            animation: fillProgress 2s ease-out, progressGlow 2s ease-in-out infinite;
        }

        /* Effet de typing pour le texte du hero */
        @keyframes typing {
            from {
                width: 0;
            }
            to {
                width: 100%;
            }
        }

        /* Animation de confetti sur succès */
        @keyframes confetti {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(1000px) rotate(720deg);
                opacity: 0;
            }
        }

        /* Effet de shake sur erreur */
        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            10%, 30%, 50%, 70%, 90% {
                transform: translateX(-10px);
            }
            20%, 40%, 60%, 80% {
                transform: translateX(10px);
            }
        }

        .alert-danger {
            animation: shake 0.5s ease-in-out;
        }

        /* Animation de succès avec scale */
        @keyframes successPop {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .alert-success {
            animation: successPop 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* ===== CARTES D'ACTIONS RAPIDES ===== */
        .action-buttons-creative {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .action-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 2px solid;
            border-radius: 20px;
            padding: 30px 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
        }

        .action-card-don {
            border-color: rgba(67, 233, 123, 0.5);
            box-shadow: 0 20px 60px rgba(67, 233, 123, 0.2);
        }

        .action-card-challenge {
            border-color: rgba(102, 126, 234, 0.5);
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.2);
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(255, 255, 255, 0.1), transparent 30%);
            animation: rotate 6s linear infinite;
            opacity: 0;
            transition: opacity 0.5s;
        }

        .action-card:hover::before {
            opacity: 1;
        }

        .action-card:hover {
            transform: translateY(-20px) scale(1.05);
        }

        .action-card-don:hover {
            border-color: rgba(67, 233, 123, 1);
            box-shadow: 
                0 30px 80px rgba(67, 233, 123, 0.4),
                0 0 60px rgba(67, 233, 123, 0.3),
                inset 0 0 40px rgba(67, 233, 123, 0.1);
        }

        .action-card-challenge:hover {
            border-color: rgba(102, 126, 234, 1);
            box-shadow: 
                0 30px 80px rgba(102, 126, 234, 0.4),
                0 0 60px rgba(102, 126, 234, 0.3),
                inset 0 0 40px rgba(102, 126, 234, 0.1);
        }

        .action-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            animation: iconBounce 2s ease-in-out infinite;
            display: inline-block;
        }

        @keyframes iconBounce {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-15px) scale(1.1);
            }
        }

        .action-card h4 {
            color: white;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .action-card p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .action-card .btn {
            margin-top: 10px;
            position: relative;
            z-index: 2;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero-banner h2 {
                font-size: 2rem;
            }
            
            .hero-banner p {
                font-size: 1rem;
            }
            
            .dons-grid, .challenges-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .action-buttons-creative {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .btn-donate, .btn-challenge {
                padding: 12px 30px;
                font-size: 1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .section-icon {
                font-size: 2rem;
            }

            .action-icon {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>

    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots"><span></span><span></span><span></span></div>
        </div>
    </div>

    <!-- HEADER -->
    <header id="mainHeader" class="header-area header-sticky">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12">
                    <nav class="main-nav d-flex align-items-center justify-content-between">
                        <a href="index.html" class="logo">
                            <img src="assets/images/logooo.png" alt="Play to Help - Manette Solidaire" height="50">
                        </a>
                        <div class="search-input" style="flex-grow: 1; max-width: 400px; margin-left: 20px;">
                            <form id="search" action="search.html" class="d-flex align-items-center">
                                <input type="text" class="form-control" placeholder="Rechercher association, don ou challenge..." name="q" />
                                <button type="submit" style="background:none; border:none; color:#666; font-size:1.2em; cursor:pointer;">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                    <span class="sr-only">Rechercher</span>
                                </button>
                            </form>
                        </div>
                        <ul class="nav d-flex align-items-center mb-0">
                            <li><a href="index.html">Accueil</a></li>
                            <li><a href="browse.html">Événements</a></li>
                            <li><a href="streams.html">Streams Solidaires</a></li>
                            <li><a href="association.php">Associations</a></li>
                            <li><a href="don.php" class="active">Dons & Challenges</a></li>
                            <li><a href="../backoffice/indexsinda.php">Back-Office</a></li>
                            <li><a href="profile.html">Profil</a></li>
                        </ul>
                        <a class="menu-trigger" role="button" aria-label="Menu toggle" tabindex="0"><span>Menu</span></a>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <!-- HERO BANNER -->
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="hero-banner">
                    <h2>Rejoignez la Révolution Gaming Solidaire !</h2>
                    <p>Plongez dans un univers où chaque kill, chaque stream, et chaque don devient une arme contre l'injustice.</p>
                    <div class="main-button">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalDon" class="btn-donate" role="button">Faire un Don Maintenant</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTIONS SÉPARÉES CRÉATIVES -->
    <div class="container">
        <!-- SECTION DONS RÉCENTS -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="section-header-creative">
                    <div class="section-icon">💚</div>
                    <h3 class="section-title">DONS RÉCENTS</h3>
                    <div class="section-subtitle">Les héros qui changent le monde</div>
                    <div class="section-line"></div>
                </div>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="dons-carousel-wrapper">
                    <div class="dons-grid" id="liste-dons">
                        <?php if (!empty($dons_recents)): ?>
                            <?php foreach ($dons_recents as $index => $don): ?>
                                <div class="item don-item">
                                    <div class="thumb position-relative">
                                        <img src="assets/images/challenge-1.png" alt="Don <?= htmlspecialchars($don['association_name']) ?>">
                                        <div class="hover-effect">
                                            <ul style="list-style:none; padding:0; margin:0;">
                                                <li><?= number_format($don['montant'], 2, ',', ' ') ?>€</li>
                                                <li><?= date('d/m/Y', strtotime($don['date_don'])) ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="down-content">
                                        <div class="avatar">
                                            <img src="assets/images/avatar-<?= sprintf('%02d', ($index % 4) + 1) ?>.jpg" alt="<?= htmlspecialchars($don['prenom'] ?: 'Anonyme') ?>">
                                        </div>
                                        <span><?= htmlspecialchars($don['prenom'] ?: 'Anonyme') ?> <?= htmlspecialchars($don['nom'] ?: '') ?></span>
                                        <h4>Pour <?= htmlspecialchars($don['association_name']) ?></h4>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Affichage par défaut si aucun don -->
                            <div class="item don-item">
                                <div class="thumb position-relative">
                                    <img src="assets/images/challenge-1.png" alt="Aucun don">
                                    <div class="hover-effect">
                                        <ul style="list-style:none; padding:0; margin:0;">
                                            <li>Aucun don</li>
                                            <li>pour le moment</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="down-content">
                                    <div class="avatar">
                                        <img src="assets/images/avatar-01.jpg" alt="Soyez le premier">
                                    </div>
                                    <span>Soyez le premier !</span>
                                    <h4>À faire un don</h4>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION CHALLENGES ÉPIQUES -->
        <div class="row mb-5 mt-5">
            <div class="col-lg-12">
                <div class="section-header-creative">
                    <div class="section-icon">🎮</div>
                    <h3 class="section-title" style="background: linear-gradient(135deg, #667eea, #764ba2, #f093fb); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">CHALLENGES ÉPIQUES</h3>
                    <div class="section-subtitle">Relevez le défi et changez des vies</div>
                    <div class="section-line" style="background: linear-gradient(90deg, transparent, #667eea, #764ba2, transparent);"></div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="challenges-carousel-wrapper">
                    <div class="challenges-grid">
                        <?php if (!empty($challenges_epiques)): ?>
                            <?php foreach ($challenges_epiques as $index => $challenge): ?>
                                <?php 
                                    $pourcentage = ($challenge['objectif'] > 0) ? ($challenge['progression'] / $challenge['objectif']) * 100 : 0;
                                    $pourcentage = min(100, max(0, $pourcentage)); // Limiter entre 0 et 100%
                                ?>
                                <div class="item challenge-card">
                                    <div class="thumb position-relative">
                                        <img src="assets/images/feature-<?= ($index % 2 == 0) ? 'left' : 'right' ?>.jpg" alt="Défi <?= htmlspecialchars($challenge['association_name']) ?>">
                                        <div class="hover-effect">
                                            <h6 style="margin:0;">Défi : <?= htmlspecialchars($challenge['name']) ?></h6>
                                        </div>
                                    </div>
                                    <div class="down-content">
                                        <h4><?= htmlspecialchars($challenge['name']) ?> pour <?= htmlspecialchars($challenge['association_name']) ?></h4>
                                        <p>Récompense : <?= htmlspecialchars($challenge['recompense']) ?></p>
                                        <div class="progress-container">
                                            <div class="progress-bar-bg">
                                                <div class="progress-fill" style="width: <?= $pourcentage ?>%;"></div>
                                            </div>
                                        </div>
                                        <small class="progress-text"><?= number_format($challenge['progression'], 0, ',', ' ') ?>€ / <?= number_format($challenge['objectif'], 0, ',', ' ') ?>€ (<?= number_format($pourcentage, 0) ?>%)</small>
                                        <a href="streams.html" class="btn-challenge mt-2" role="button">Rejoindre en Stream</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Affichage par défaut si aucun challenge épique -->
                            <div class="item challenge-card">
                                <div class="thumb position-relative">
                                    <img src="assets/images/feature-left.jpg" alt="Aucun challenge épique">
                                    <div class="hover-effect">
                                        <h6 style="margin:0;">Aucun challenge épique</h6>
                                    </div>
                                </div>
                                <div class="down-content">
                                    <h4>Aucun challenge épique pour le moment</h4>
                                    <p>Les challenges épiques apparaissent quand ils dépassent 1000€</p>
                                    <div class="progress-container">
                                        <div class="progress-bar-bg">
                                            <div class="progress-fill" style="width: 0%;"></div>
                                        </div>
                                    </div>
                                    <small class="progress-text">Soyez le premier à créer un challenge épique !</small>
                                    <a href="#modalDon" data-bs-toggle="modal" class="btn-challenge mt-2" role="button">Créer un Challenge</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION ACTIONS RAPIDES -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="action-buttons-creative">
                    <div class="action-card action-card-don">
                        <div class="action-icon">💚</div>
                        <h4>Don Simple</h4>
                        <p>Soutenez directement une cause</p>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalDon" class="btn btn-donate" role="button">Faire un Don</a>
                    </div>
                    <div class="action-card action-card-challenge">
                        <div class="action-icon">🎮</div>
                        <h4>Challenge Stream</h4>
                        <p>Créez votre propre défi gaming</p>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalChallenge" class="btn btn-challenge" role="button">Lancer un Challenge</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DON -->
    <div class="modal fade" id="modalDon" tabindex="-1" aria-labelledby="modalDonLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-light border border-success">
                <div class="modal-header border-success">
                    <h5 class="modal-title" id="modalDonLabel">Faire un Don Solidaire</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formDon">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom <small class="text-muted">(facultatif)</small></label>
                                <input type="text" class="form-control bg-secondary text-light border-success" name="nom" placeholder="Dupont">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prénom <small class="text-muted">(facultatif)</small></label>
                                <input type="text" class="form-control bg-secondary text-light border-success" name="prenom" placeholder="Jean">
                            </div>
                        </div>

                        <div class="mb-3 position-relative">
                            <label class="form-label">Email <small class="text-muted">(facultatif)</small></label>
                            <input 
                                type="text" 
                                class="form-control bg-secondary text-light border-success" 
                                name="email" 
                                placeholder="jean@example.com"
                                id="emailInput">
                            <div class="invalid-feedback">
                                Email invalide ! Exemple correct : jean@exemple.com
                            </div>
                        </div>

                        <div class="alert alert-info small p-3 mb-4" role="alert" style="background: rgba(0, 212, 255, 0.15); border: 2px solid rgba(0, 212, 255, 0.4); border-radius: 15px;">
                            <strong>ℹ️ Ces informations sont 100 % facultatives.</strong> Tu peux donner de façon totalement anonyme !
                        </div>

                        <!-- Montant -->
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold">Montant (€) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control bg-secondary text-light border-success" name="montant" step="0.01" placeholder="300" style="font-size: 1.2rem; padding: 15px;">
                            <div class="invalid-feedback">Le montant doit être supérieur à 0 €</div>
                        </div>

                        <!-- Association -->
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold">Association <span class="text-danger">*</span></label>
                            <select class="form-select bg-secondary text-light border-success" name="id_association" style="font-size: 1.1rem; padding: 15px;">
                                <option value="">Choisissez votre cause...</option>
                                <?php
                                try {
                                    $stmt = config::getConnexion()->query("SELECT id_association, name FROM association ORDER BY name");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="'.$row['id_association'].'">'.htmlspecialchars($row['name']).'</option>';
                                    }
                                } catch (Exception $e) {
                                    echo '<option disabled>Erreur chargement associations</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner une association</div>
                        </div>

                        <!-- Mode de Paiement -->
                        <div class="mb-4">
                            <label class="form-label text-uppercase fw-bold">Mode de Paiement <span class="text-danger">*</span></label>
                            
                            <div class="payment-options row g-3">
                                <!-- Option Stripe -->
                                <div class="col-md-6">
                                    <div class="payment-option payment-option-stripe" style="background: transparent; border: 2px solid rgba(0, 212, 255, 0.5); border-radius: 15px; padding: 20px; cursor: pointer; transition: all 0.3s; height: 100%;">
                                        <label class="d-flex align-items-start cursor-pointer" style="cursor: pointer; margin: 0;">
                                            <input type="radio" name="payment_mode" value="stripe" checked class="me-3 mt-1" style="width: 20px; height: 20px; cursor: pointer; flex-shrink: 0;">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span style="font-size: 1.3rem; margin-right: 8px;">💳</span>
                                                    <strong style="font-size: 1rem; color: #fff;">Paiement en ligne via Stripe</strong>
                                                </div>
                                                
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Option Don Direct -->
                                <div class="col-md-6">
                                    <div class="payment-option payment-option-direct" style="background: transparent; border: 2px solid rgba(255, 193, 7, 0.5); border-radius: 15px; padding: 20px; cursor: pointer; transition: all 0.3s; height: 100%;">
                                        <label class="d-flex align-items-start cursor-pointer" style="cursor: pointer; margin: 0;">
                                            <input type="radio" name="payment_mode" value="direct" class="me-3 mt-1" style="width: 20px; height: 20px; cursor: pointer; flex-shrink: 0;">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span style="font-size: 1.3rem; margin-right: 8px;">💰</span>
                                                    <strong style="font-size: 1rem; color: #fff;">Don Direct</strong>
                                                </div>
                                               
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="btnSubmitDon" class="btn btn-success btn-lg w-100 py-3 fs-4 shadow-lg text-uppercase fw-bold" style="background: linear-gradient(135deg, #00d4ff, #0099ff); border: none; border-radius: 15px; letter-spacing: 1px;">
                            💳 Procéder au Paiement
                        </button>
                        <div id="donResult" class="mt-3 text-center fw-bold fs-5"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL CHALLENGE AMÉLIORÉ -->
    <div class="modal fade" id="modalChallenge" tabindex="-1" aria-labelledby="modalChallengeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-light border border-purple">
                <div class="modal-header border-purple">
                    <h5 class="modal-title" id="modalChallengeLabel">
                        🎮 Créer un Challenge Don Stream
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <form id="formChallenge" class="needs-validation" novalidate>
                        
                        <!-- Association -->
                        <div class="mb-3">
                            <label for="challenge-assoc" class="form-label">
                                Association <span class="text-danger">*</span>
                            </label>
                            <select class="form-control bg-secondary text-light border-purple" 
                                    id="challenge-assoc" 
                                    name="challenge-assoc" 
                                    required>
                                <option value="">Sélectionnez votre cause héroïque...</option>
                                <?php
                                try {
                                    $stmt = config::getConnexion()->query("SELECT id_association, name FROM association ORDER BY name");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="'.$row['id_association'].'">'.htmlspecialchars($row['name']).'</option>';
                                    }
                                } catch (Exception $e) {
                                    echo '<option disabled>Erreur chargement associations</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Veuillez sélectionner une association</div>
                        </div>

                        <!-- Défi In-Game -->
                        <div class="mb-3">
                            <label for="defi" class="form-label">
                                Défi In-Game <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control bg-secondary text-light border-purple" 
                                   id="defi" 
                                   name="defi" 
                                   placeholder="Ex: 10 kills Fortnite, Marathon WoW 24h..." 
                                   required>
                            <div class="invalid-feedback">Le défi est requis</div>
                            <small class="text-muted">Décrivez votre mission épique !</small>
                        </div>

                        <!-- Objectif Dons -->
                        <div class="mb-3">
                            <label for="objectif" class="form-label">
                                Objectif Dons (€) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control bg-secondary text-light border-purple" 
                                   id="objectif" 
                                   name="objectif" 
                                   min="10" 
                                   step="0.01" 
                                   placeholder="100.00" 
                                   required>
                            <div class="invalid-feedback">L'objectif doit être d'au moins 10€</div>
                            <small class="text-muted">Le niveau à atteindre (minimum 10€)</small>
                        </div>

                        <!-- Récompense -->
                        <div class="mb-4">
                            <label for="recompense" class="form-label">
                                Récompense <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control bg-secondary text-light border-purple" 
                                   id="recompense" 
                                   name="recompense" 
                                   placeholder="Ex: Badge Épique + Shoutout, NFT Solidaire..." 
                                   required>
                            <div class="invalid-feedback">La récompense est requise</div>
                            <small class="text-muted">Le trésor pour les héros !</small>
                        </div>

                        <div class="alert alert-info small p-2" role="alert">
                            <strong>💡 Astuce :</strong> Plus votre challenge est créatif et engageant, plus vous mobiliserez la communauté !
                        </div>

                        <button type="submit" class="btn btn-challenge w-100 py-3 fs-5 shadow-lg">
                            🚀 Lancer le Challenge Épique !
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER FUTURISTE HEXAGONAL -->
    <footer class="footer-compact">
        <div class="footer-glow"></div>
        
        <div class="container">
            <div class="footer-main">
                <!-- Logo et titre -->
                <div class="footer-brand">
                    <img src="assets/images/logooo.png" alt="Play to Help" class="footer-logo">
                    <h3>PLAY TO HELP</h3>
                    <p>🎮 Gaming pour l'Humanitaire</p>
                </div>

                <!-- Navigation rapide -->
                <div class="footer-nav">
                    <a href="index.html">Accueil</a>
                    <a href="don.php">Dons</a>
                    <a href="streams.html">Streams</a>
                    <a href="association.php">Associations</a>
                </div>

                <!-- Réseaux sociaux -->
                <div class="footer-social">
                    <a href="#" class="social-btn discord"><i class="fab fa-discord"></i></a>
                    <a href="#" class="social-btn twitch"><i class="fab fa-twitch"></i></a>
                    <a href="#" class="social-btn youtube"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
                </div>
            </div>

            <!-- Copyright -->
            <div class="footer-copyright">
                <div class="glow-line"></div>
                <p>© 2025 Play to Help - Gaming Solidaire • Tous droits réservés</p>
            </div>
        </div>
    </footer>


    <style>
        /* ===== FOOTER COMPACT CRÉATIF ===== */
        .footer-compact {
            background: linear-gradient(135deg, rgba(15, 12, 41, 0.95), rgba(30, 30, 50, 1));
            position: relative;
            padding: 60px 0 30px;
            margin-top: 80px;
            overflow: hidden;
        }

        .footer-glow {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 50% 0%, rgba(102, 126, 234, 0.1), transparent 70%);
            pointer-events: none;
        }

        .footer-main {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-brand {
            text-align: center;
        }

        .footer-logo {
            width: 50px;
            height: 50px;
            margin-bottom: 15px;
            filter: drop-shadow(0 0 10px rgba(102, 126, 234, 0.6));
        }

        .footer-brand h3 {
            color: white;
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .footer-brand p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            margin: 0;
        }

        .footer-nav {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .footer-nav a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .footer-nav a:hover {
            color: #667eea;
            transform: translateY(-2px);
        }

        .footer-nav a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.3s ease;
        }

        .footer-nav a:hover::after {
            width: 100%;
        }

        .footer-social {
            display: flex;
            gap: 15px;
        }

        .social-btn {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .social-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .social-btn:hover::before {
            left: 100%;
        }

        .social-btn:hover {
            transform: translateY(-5px) scale(1.1);
        }

        .discord { background: linear-gradient(135deg, #5865F2, #4752C4); }
        .twitch { background: linear-gradient(135deg, #9146FF, #6441A5); }
        .youtube { background: linear-gradient(135deg, #FF0000, #CC0000); }
        .twitter { background: linear-gradient(135deg, #1DA1F2, #0C85D0); }

        .footer-copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .glow-line {
            width: 200px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #667eea, #764ba2, transparent);
            margin: 0 auto 20px;
            animation: lineGlow 2s ease-in-out infinite;
        }

        @keyframes lineGlow {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        .footer-copyright p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .footer-main {
                flex-direction: column;
                text-align: center;
                gap: 30px;
            }

            .footer-nav {
                justify-content: center;
            }

            .footer-social {
                justify-content: center;
            }
        }

        /* Copyright futuriste */
        .footer-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 30px 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
        }

        .copyright-line {
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #00ffff, #ff00ff, #00ffff, transparent);
            margin-bottom: 20px;
            animation: lineGlow 3s ease-in-out infinite;
        }

        @keyframes lineGlow {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        .copyright-text {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 2px;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #00ffff;
            border-radius: 50%;
            animation: pulseDot 1s ease-in-out infinite;
        }

        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.5); }
        }

        /* Effet de scan */
        .scan-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #00ffff, transparent);
            animation: scanMove 4s linear infinite;
        }

        @keyframes scanMove {
            0% { transform: translateY(0); }
            100% { transform: translateY(100vh); }
        }

        .footer-heading::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .footer-links a:hover {
            color: #667eea;
            transform: translateX(5px);
            text-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
        }

        /* Réseaux sociaux */
        .footer-social {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .social-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .social-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .social-icon:hover::before {
            opacity: 1;
        }

        .social-twitch {
            background: linear-gradient(135deg, #9146FF, #6441A5);
            color: white;
        }

        .social-discord {
            background: linear-gradient(135deg, #5865F2, #4752C4);
            color: white;
        }

        .social-twitter {
            background: linear-gradient(135deg, #1DA1F2, #0C85D0);
            color: white;
        }

        .social-youtube {
            background: linear-gradient(135deg, #FF0000, #CC0000);
            color: white;
        }

        .social-instagram {
            background: linear-gradient(135deg, #E1306C, #C13584, #833AB4);
            color: white;
        }

        .social-icon:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }

        /* Stats */
        .footer-stats {
            display: flex;
            gap: 20px;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 12px;
            border: 1px solid rgba(102, 126, 234, 0.3);
            flex: 1;
        }

        .stat-number {
            color: #667eea;
            font-size: 1.5rem;
            font-weight: 800;
            display: block;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.85rem;
            margin-top: 5px;
        }

        /* Bottom bar */
        .footer-bottom {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-copyright {
            color: rgba(255, 255, 255, 0.6);
            margin: 0;
            font-size: 0.9rem;
        }

        .brand-highlight {
            color: #667eea;
            font-weight: 700;
        }

        .footer-legal {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
        }

        .footer-legal a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .footer-legal a:hover {
            color: #667eea;
        }

        .footer-legal .separator {
            color: rgba(255, 255, 255, 0.3);
        }

        /* Vague animée */
        .footer-wave {
            position: absolute;
            top: -100px;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
        }

        .footer-wave svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 120px;
        }

        .wave-path {
            fill: rgba(102, 126, 234, 0.1);
            animation: waveAnimation 10s ease-in-out infinite;
        }

        @keyframes waveAnimation {
            0%, 100% {
                transform: translateX(0);
            }
            50% {
                transform: translateX(-25px);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .footer-gaming {
                padding: 50px 0 20px;
                margin-top: 60px;
            }

            .footer-social {
                justify-content: center;
            }

            .footer-stats {
                justify-content: center;
            }

            .footer-legal {
                margin-top: 15px;
            }

            .footer-wave {
                top: -50px;
            }

            .footer-wave svg {
                height: 60px;
            }
        }
    </style>

    <!-- SCRIPTS -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/isotope.min.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/tabs.js"></script>
    <script src="assets/js/popup.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/dons-assoc.js"></script>

    <!-- STRIPE SDK -->
    <script src="https://js.stripe.com/v3/"></script>

    <!-- ANIMATIONS GAMING AVANCÉES -->
    <script>
    // Effet de particules flottantes
    function createParticles() {
        const particlesContainer = document.createElement('div');
        particlesContainer.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        `;
        document.body.appendChild(particlesContainer);

        for (let i = 0; i < 30; i++) {
            const particle = document.createElement('div');
            const size = Math.random() * 4 + 2;
            const colors = ['#667eea', '#764ba2', '#00f2fe', '#43e97b', '#f5576c'];
            const color = colors[Math.floor(Math.random() * colors.length)];
            
            particle.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                background: ${color};
                border-radius: 50%;
                top: ${Math.random() * 100}%;
                left: ${Math.random() * 100}%;
                opacity: ${Math.random() * 0.5 + 0.3};
                box-shadow: 0 0 ${size * 3}px ${color};
                animation: floatParticle ${Math.random() * 10 + 10}s linear infinite;
            `;
            
            particlesContainer.appendChild(particle);
        }
    }

    // Effet de glitch sur les cartes au survol
    document.addEventListener('DOMContentLoaded', function() {
        createParticles();

        // Effet de son au hover (simulation visuelle)
        const cards = document.querySelectorAll('.don-item, .challenge-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.filter = 'brightness(1.1)';
                
                // Effet de ripple
                const ripple = document.createElement('div');
                ripple.style.cssText = `
                    position: absolute;
                    width: 20px;
                    height: 20px;
                    background: rgba(255, 255, 255, 0.5);
                    border-radius: 50%;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%) scale(0);
                    animation: rippleEffect 0.6s ease-out;
                    pointer-events: none;
                `;
                this.appendChild(ripple);
                
                setTimeout(() => ripple.remove(), 600);
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.filter = 'brightness(1)';
            });
        });

        // Effet de typing sur le hero
        const heroTitle = document.querySelector('.hero-banner h2');
        if (heroTitle) {
            const text = heroTitle.textContent;
            heroTitle.textContent = '';
            let i = 0;
            
            const typeWriter = setInterval(() => {
                if (i < text.length) {
                    heroTitle.textContent += text.charAt(i);
                    i++;
                } else {
                    clearInterval(typeWriter);
                }
            }, 50);
        }

        // Effet 3D sur les cartes au mouvement de la souris
        const cards = document.querySelectorAll('.don-item, .challenge-card');
        cards.forEach(card => {
            card.addEventListener('mousemove', function(e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 10;
                const rotateY = (centerX - x) / 10;
                
                this.style.transform = `
                    perspective(1000px)
                    rotateX(${rotateX}deg)
                    rotateY(${rotateY}deg)
                    translateY(-15px)
                    scale(1.05)
                `;
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0) scale(1)';
            });
        });

        // Effet de parallax global
        document.addEventListener('mousemove', function(e) {
            const allCards = document.querySelectorAll('.don-item, .challenge-card, .action-card');
            const mouseX = e.clientX / window.innerWidth;
            const mouseY = e.clientY / window.innerHeight;
            
            allCards.forEach((card, index) => {
                if (!card.matches(':hover')) {
                    const speed = (index % 2 === 0) ? 15 : -15;
                    const x = (mouseX - 0.5) * speed;
                    const y = (mouseY - 0.5) * speed;
                    
                    card.style.transform = `translateX(${x}px) translateY(${y}px)`;
                }
            });
        });

        // Effet de confetti sur succès
        window.createConfetti = function() {
            const colors = ['#667eea', '#764ba2', '#00f2fe', '#43e97b', '#f5576c'];
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                const color = colors[Math.floor(Math.random() * colors.length)];
                const size = Math.random() * 10 + 5;
                
                confetti.style.cssText = `
                    position: fixed;
                    width: ${size}px;
                    height: ${size}px;
                    background: ${color};
                    top: -10px;
                    left: ${Math.random() * 100}%;
                    z-index: 9999;
                    animation: confettiFall ${Math.random() * 3 + 2}s linear forwards;
                    transform: rotate(${Math.random() * 360}deg);
                `;
                
                document.body.appendChild(confetti);
                setTimeout(() => confetti.remove(), 5000);
            }
        };
    });

    // Animations CSS dynamiques
    const style = document.createElement('style');
    style.textContent = `
        @keyframes floatParticle {
            0% {
                transform: translateY(0) translateX(0) rotate(0deg);
            }
            50% {
                transform: translateY(-100vh) translateX(${Math.random() * 100 - 50}px) rotate(180deg);
            }
            100% {
                transform: translateY(-200vh) translateX(${Math.random() * 100 - 50}px) rotate(360deg);
            }
        }

        @keyframes rippleEffect {
            0% {
                transform: translate(-50%, -50%) scale(0);
                opacity: 1;
            }
            100% {
                transform: translate(-50%, -50%) scale(20);
                opacity: 0;
            }
        }

        @keyframes confettiFall {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
    </script>

    <!-- AJAX POUR LE DON AVEC 2 MODES DE PAIEMENT -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("formDon");
        if (!form) return;

        // Effet hover sur les options de paiement
        const paymentOptions = document.querySelectorAll('.payment-option');
        paymentOptions.forEach(option => {
            const isDirect = option.classList.contains('payment-option-direct');
            const isStripe = option.classList.contains('payment-option-stripe');
            
            option.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                if (isDirect) {
                    this.style.boxShadow = '0 8px 25px rgba(255, 193, 7, 0.3)';
                } else if (isStripe) {
                    this.style.boxShadow = '0 8px 25px rgba(0, 212, 255, 0.3)';
                }
            });
            option.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
            option.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
                updatePaymentSelection();
            });
        });

        // Mettre à jour la sélection visuelle
        function updatePaymentSelection() {
            paymentOptions.forEach(opt => {
                const radio = opt.querySelector('input[type="radio"]');
                const isDirect = opt.classList.contains('payment-option-direct');
                const isStripe = opt.classList.contains('payment-option-stripe');
                
                if (radio.checked) {
                    if (isDirect) {
                        opt.style.borderColor = 'rgba(255, 193, 7, 1)';
                        opt.style.borderWidth = '3px';
                        opt.style.background = 'rgba(255, 193, 7, 0.15)';
                    } else if (isStripe) {
                        opt.style.borderColor = 'rgba(0, 212, 255, 1)';
                        opt.style.borderWidth = '3px';
                        opt.style.background = 'rgba(0, 212, 255, 0.15)';
                    }
                } else {
                    if (isDirect) {
                        opt.style.borderColor = 'rgba(255, 193, 7, 0.5)';
                        opt.style.borderWidth = '2px';
                        opt.style.background = 'transparent';
                    } else if (isStripe) {
                        opt.style.borderColor = 'rgba(0, 212, 255, 0.5)';
                        opt.style.borderWidth = '2px';
                        opt.style.background = 'transparent';
                    }
                }
            });
        }

        // Écouter les changements de radio
        document.querySelectorAll('input[name="payment_mode"]').forEach(radio => {
            radio.addEventListener('change', function() {
                updatePaymentSelection();
                updateButtonText();
            });
        });

        // Mettre à jour le texte du bouton selon le mode
        function updateButtonText() {
            const btn = document.getElementById('btnSubmitDon');
            const selectedMode = document.querySelector('input[name="payment_mode"]:checked').value;
            
            if (selectedMode === 'stripe') {
                btn.innerHTML = '💳 Procéder au Paiement';
            } else {
                btn.innerHTML = '💰 Procéder au Paiement';
            }
        }

        // Initialiser la sélection
        updatePaymentSelection();
        updateButtonText();

        form.addEventListener("submit", async function (e) {
            e.preventDefault();

            const btn = form.querySelector("button[type='submit']");
            const result = document.getElementById("donResult");

            // Validation
            const montant = parseFloat(form.querySelector('[name="montant"]').value);
            const id_association = form.querySelector('[name="id_association"]').value;
            const nom = form.querySelector('[name="nom"]').value.trim() || 'Anonyme';
            const prenom = form.querySelector('[name="prenom"]').value.trim();
            const email = form.querySelector('[name="email"]').value.trim() || 'anonyme@playtohelp.com';
            const paymentMode = form.querySelector('input[name="payment_mode"]:checked').value;

            // Validation du montant
            if (!montant || montant <= 0) {
                result.innerHTML = '<div class="alert alert-danger">Le montant doit être supérieur à 0 €</div>';
                return;
            }

            // Validation de l'association
            if (!id_association) {
                result.innerHTML = '<div class="alert alert-danger">Veuillez sélectionner une association</div>';
                return;
            }

            // Construire le nom complet
            const nomComplet = prenom ? `${prenom} ${nom}` : nom;

            btn.disabled = true;

            // MODE STRIPE - Redirection vers Stripe Checkout
            if (paymentMode === 'stripe') {
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Redirection vers Stripe...';
                result.innerHTML = '<div class="alert alert-info">🔒 Redirection sécurisée vers Stripe...</div>';

                const formData = new FormData();
                formData.append('montant', montant);
                formData.append('nom', nomComplet);
                formData.append('email', email);
                formData.append('id_association', id_association);

                try {
                    const response = await fetch("../backoffice/process_payment.php", {
                        method: "POST",
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success && data.redirect_url) {
                        result.innerHTML = `
                            <div class="alert alert-success p-4 text-center">
                                <h5>✅ Redirection vers le paiement sécurisé...</h5>
                                <p class="mb-0">Vous allez être redirigé vers Stripe</p>
                            </div>`;
                        
                        // Rediriger vers Stripe Checkout
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 1000);
                    } else {
                        result.innerHTML = `<div class="alert alert-danger p-3">❌ ${data.error || 'Erreur lors de la création du paiement'}</div>`;
                        btn.disabled = false;
                        btn.innerHTML = '💳 Procéder au Paiement';
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    result.innerHTML = '<div class="alert alert-danger p-3">❌ Erreur réseau. Réessayez.</div>';
                    btn.disabled = false;
                    btn.innerHTML = '💳 Procéder au Paiement';
                }
            } 
            // MODE DON DIRECT - Enregistrement direct dans la BDD
            else if (paymentMode === 'direct') {
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement du don...';
                result.innerHTML = '<div class="alert alert-info">💰 Enregistrement de votre don direct...</div>';

                const formData = new FormData();
                formData.append('montant', montant);
                formData.append('prenom', prenom || '');
                formData.append('nom', nom);
                formData.append('email', email);
                formData.append('id_association', id_association);

                try {
                    const response = await fetch("../backoffice/add.php", {
                        method: "POST",
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        result.innerHTML = `
                            <div class="alert alert-success p-4 text-center">
                                <h5>✅ Don enregistré avec succès !</h5>
                                <p class="mb-0">Montant : ${data.message}</p>
                                <p class="mb-0 mt-2">Merci pour votre générosité ! 💚</p>
                            </div>`;
                        
                        // Réinitialiser le formulaire
                        form.reset();
                        updatePaymentSelection();
                        
                        // Fermer le modal après 3 secondes
                        setTimeout(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById("modalDon"));
                            if (modal) modal.hide();
                            window.location.reload();
                        }, 3000);
                    } else {
                        result.innerHTML = `<div class="alert alert-danger p-3">❌ ${data.error || 'Erreur lors de l\'enregistrement'}</div>`;
                        btn.disabled = false;
                        btn.innerHTML = '💳 Procéder au Paiement';
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    result.innerHTML = '<div class="alert alert-danger p-3">❌ Erreur réseau. Réessayez.</div>';
                    btn.disabled = false;
                    btn.innerHTML = '💳 Procéder au Paiement';
                }
            }
        });

        // Validation email en temps réel
        const emailInput = document.getElementById('emailInput');
        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                const email = this.value.trim();
                if (email && !isValidEmail(email)) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }

        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
    });
    </script>

    <!-- AJAX POUR LE CHALLENGE -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const formChallenge = document.getElementById("formChallenge");
        if (!formChallenge) return;

        formChallenge.addEventListener("submit", async function (e) {
            e.preventDefault();

            const btn = formChallenge.querySelector("button[type='submit']");
            const originalText = btn.innerHTML;

            // Validation côté client
            const assoc = formChallenge.querySelector('#challenge-assoc').value;
            const defi = formChallenge.querySelector('#defi').value.trim();
            const objectif = parseFloat(formChallenge.querySelector('#objectif').value);
            const recompense = formChallenge.querySelector('#recompense').value.trim();

            // Supprimer les messages d'erreur précédents
            const existingAlert = formChallenge.querySelector('.alert:not(.alert-info)');
            if (existingAlert) existingAlert.remove();

            // Validation
            if (!assoc) {
                showChallengeAlert('danger', 'Veuillez sélectionner une association');
                return;
            }
            if (!defi) {
                showChallengeAlert('danger', 'Le défi est requis');
                return;
            }
            if (!objectif || objectif < 10) {
                showChallengeAlert('danger', 'L\'objectif doit être d\'au moins 10€');
                return;
            }
            if (!recompense) {
                showChallengeAlert('danger', 'La récompense est requise');
                return;
            }

            // Désactiver le bouton
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Création en cours...';

            const formData = new FormData(formChallenge);

            try {
                const response = await fetch("../backoffice/addchallenge.php", {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showChallengeAlert('success', `
                        <h5 class="mb-2">🎮 Challenge Créé avec Succès !</h5>
                        <p class="mb-0">${data.message}</p>
                    `);
                    
                    formChallenge.reset();
                    
                    // Fermer le modal après 3 secondes
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById("modalChallenge"));
                        if (modal) modal.hide();
                        
                        // Recharger la page pour afficher le nouveau challenge
                        window.location.reload();
                    }, 3000);
                } else {
                    showChallengeAlert('danger', data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                showChallengeAlert('danger', 'Erreur réseau. Veuillez réessayer.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });

        // Fonction helper pour afficher les alertes
        function showChallengeAlert(type, message) {
            const existingAlert = formChallenge.querySelector('.alert:not(.alert-info)');
            if (existingAlert) existingAlert.remove();

            const alert = document.createElement('div');
            alert.className = `alert alert-${type} mt-3`;
            alert.innerHTML = message;
            
            const submitBtn = formChallenge.querySelector("button[type='submit']");
            submitBtn.parentNode.insertBefore(alert, submitBtn);
        }
    });
    </script>

</body>
</html>