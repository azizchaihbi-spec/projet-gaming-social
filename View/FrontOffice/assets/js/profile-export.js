/**
 * Export profile to PDF
 * Generates a formatted PDF document with user profile information
 */
function exportProfileToPDF() {
    // Utiliser jsPDF via window.jspdf
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    // Configuration des couleurs et styles
    const primaryColor = [34, 211, 238]; // Cyan
    const textColor = [50, 50, 50];
    const lightGray = [200, 200, 200];
    
    // En-tête du document
    doc.setFillColor(...primaryColor);
    doc.rect(0, 0, 210, 40, 'F');
    
    doc.setTextColor(255, 255, 255);
    doc.setFontSize(24);
    doc.setFont(undefined, 'bold');
    doc.text('PLAY TO HELP', 105, 18, { align: 'center' });
    
    doc.setFontSize(14);
    doc.setFont(undefined, 'normal');
    doc.text('Profil Utilisateur', 105, 28, { align: 'center' });
    
    // Date d'export
    doc.setFontSize(9);
    doc.setTextColor(...textColor);
    const today = new Date().toLocaleDateString('fr-FR');
    doc.text(`Document généré le ${today}`, 105, 35, { align: 'center' });
    
    let yPos = 50;
    
    // Informations personnelles
    doc.setFontSize(16);
    doc.setFont(undefined, 'bold');
    doc.setTextColor(...primaryColor);
    doc.text('INFORMATIONS PERSONNELLES', 20, yPos);
    
    // Ligne de séparation
    doc.setDrawColor(...primaryColor);
    doc.setLineWidth(0.5);
    doc.line(20, yPos + 2, 190, yPos + 2);
    
    yPos += 12;
    doc.setFontSize(11);
    doc.setFont(undefined, 'normal');
    doc.setTextColor(...textColor);
    
    // Username
    const username = document.getElementById('profileUsername')?.textContent || 'N/A';
    doc.setFont(undefined, 'bold');
    doc.text('Nom d\'utilisateur:', 25, yPos);
    doc.setFont(undefined, 'normal');
    doc.text(username, 75, yPos);
    yPos += 8;
    
    // Email
    const email = document.getElementById('profileEmail')?.textContent || 'N/A';
    doc.setFont(undefined, 'bold');
    doc.text('Email:', 25, yPos);
    doc.setFont(undefined, 'normal');
    doc.text(email, 75, yPos);
    yPos += 8;
    
    // Nom complet
    const fullName = document.getElementById('profileFullName')?.textContent || 'N/A';
    doc.setFont(undefined, 'bold');
    doc.text('Nom complet:', 25, yPos);
    doc.setFont(undefined, 'normal');
    doc.text(fullName, 75, yPos);
    yPos += 8;
    
    // Date de naissance
    const birthdate = document.getElementById('profileBirthdate')?.textContent || 'N/A';
    doc.setFont(undefined, 'bold');
    doc.text('Date de naissance:', 25, yPos);
    doc.setFont(undefined, 'normal');
    doc.text(birthdate, 75, yPos);
    yPos += 8;
    
    // Genre
    const gender = document.getElementById('profileGender')?.textContent || 'N/A';
    doc.setFont(undefined, 'bold');
    doc.text('Genre:', 25, yPos);
    doc.setFont(undefined, 'normal');
    doc.text(gender, 75, yPos);
    yPos += 8;
    
    // Localisation
    const location = document.getElementById('profileLocation')?.textContent || 'N/A';
    doc.setFont(undefined, 'bold');
    doc.text('Localisation:', 25, yPos);
    doc.setFont(undefined, 'normal');
    doc.text(location, 75, yPos);
    yPos += 8;
    
    // Date d'inscription
    const joinDate = document.getElementById('profileJoinDate')?.textContent || 'N/A';
    doc.setFont(undefined, 'bold');
    doc.text('Membre depuis:', 25, yPos);
    doc.setFont(undefined, 'normal');
    doc.text(joinDate, 75, yPos);
    yPos += 8;
    
    // Rôle
    const role = document.getElementById('profileRole')?.textContent || 'N/A';
    doc.setFont(undefined, 'bold');
    doc.text('Rôle:', 25, yPos);
    doc.setFont(undefined, 'normal');
    doc.text(role, 75, yPos);
    yPos += 12;
    
    // Section Streamer (si applicable)
    const streamerInfo = document.getElementById('streamerInfo');
    if (streamerInfo && streamerInfo.style.display !== 'none') {
        // Vérifier si on a assez d'espace, sinon nouvelle page
        if (yPos > 240) {
            doc.addPage();
            yPos = 20;
        }
        
        doc.setFontSize(16);
        doc.setFont(undefined, 'bold');
        doc.setTextColor(...primaryColor);
        doc.text('INFORMATIONS STREAMER', 20, yPos);
        
        doc.setDrawColor(...primaryColor);
        doc.setLineWidth(0.5);
        doc.line(20, yPos + 2, 190, yPos + 2);
        
        yPos += 12;
        doc.setFontSize(11);
        doc.setFont(undefined, 'normal');
        doc.setTextColor(...textColor);
        
        // Lien de stream
        const streamLink = document.getElementById('profileStreamLink')?.textContent || 'N/A';
        doc.setFont(undefined, 'bold');
        doc.text('Lien de stream:', 25, yPos);
        doc.setFont(undefined, 'normal');
        doc.setTextColor(0, 0, 255);
        doc.textWithLink(streamLink, 75, yPos, { url: streamLink });
        doc.setTextColor(...textColor);
        yPos += 8;
        
        // Plateforme
        const platform = document.getElementById('profileStreamPlatform')?.textContent || 'N/A';
        doc.setFont(undefined, 'bold');
        doc.text('Plateforme:', 25, yPos);
        doc.setFont(undefined, 'normal');
        doc.text(platform, 75, yPos);
        yPos += 8;
        
        // Description
        const description = document.getElementById('profileStreamDescription')?.textContent || 'Aucune description';
        doc.setFont(undefined, 'bold');
        doc.text('Description:', 25, yPos);
        yPos += 6;
        doc.setFont(undefined, 'normal');
        
        // Gérer le texte long avec retour à la ligne
        const maxWidth = 160;
        const lines = doc.splitTextToSize(description, maxWidth);
        lines.forEach(line => {
            if (yPos > 270) {
                doc.addPage();
                yPos = 20;
            }
            doc.text(line, 25, yPos);
            yPos += 6;
        });
        
        yPos += 6;
    }
    
    // Statistiques
    if (yPos > 200) {
        doc.addPage();
        yPos = 20;
    }
    
    doc.setFontSize(16);
    doc.setFont(undefined, 'bold');
    doc.setTextColor(...primaryColor);
    doc.text('STATISTIQUES', 20, yPos);
    
    doc.setDrawColor(...primaryColor);
    doc.setLineWidth(0.5);
    doc.line(20, yPos + 2, 190, yPos + 2);
    
    yPos += 12;
    doc.setFontSize(11);
    doc.setTextColor(...textColor);
    
    const statGames = document.getElementById('statGames')?.textContent || '0';
    const statFriends = document.getElementById('statFriends')?.textContent || '0';
    const statStreams = document.getElementById('statStreams')?.textContent || '0';
    const statClips = document.getElementById('statClips')?.textContent || '0';
    
    // Créer un tableau pour les stats
    doc.setFont(undefined, 'bold');
    doc.text('Jeux téléchargés:', 25, yPos);
    doc.setFont(undefined, 'normal');
    doc.text(statGames, 100, yPos);
    yPos += 8;
    
    doc.setFont(undefined, 'bold');
    doc.text('Amis en ligne:', 25, yPos);
    doc.setFont(undefined, 'normal');
    doc.text(statFriends, 100, yPos);
    yPos += 8;
    
    doc.setFont(undefined, 'bold');
    doc.text('Streams live:', 25, yPos);
    doc.setFont(undefined, 'normal');
    doc.text(statStreams, 100, yPos);
    yPos += 8;
    
    doc.setFont(undefined, 'bold');
    doc.text('Clips:', 25, yPos);
    doc.setFont(undefined, 'normal');
    doc.text(statClips, 100, yPos);
    
    // Pied de page
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150, 150, 150);
        doc.text(`Play to Help - Gaming pour l'Humanitaire`, 105, 285, { align: 'center' });
        doc.text(`Page ${i} sur ${pageCount}`, 105, 290, { align: 'center' });
    }
    
    // Télécharger le PDF
    const filename = `PlayToHelp_Profil_${username.replace(/[^a-z0-9]/gi, '_')}_${new Date().getTime()}.pdf`;
    doc.save(filename);
    
    // Message de confirmation
    alert('✅ Votre profil a été exporté en PDF avec succès !');
}
