<?php
require_once __DIR__ . '/../vendor/tcpdf/tcpdf.php';
require_once __DIR__ . '/../config/config.php';

class ExportController {
    
    public static function generatePDF($donId) {
        global $conn;
        
        // Récupérer les informations du don
        $stmt = $conn->prepare("
            SELECT d.*, 
                   CONCAT(COALESCE(d.prenom, ''), ' ', d.nom) as nom_complet,
                   a.name as association_nom
            FROM don d 
            JOIN association a ON d.id_association = a.id_association 
            WHERE d.id_don = ?
        ");
        $stmt->bind_param("i", $donId);
        $stmt->execute();
        $result = $stmt->get_result();
        $don = $result->fetch_assoc();
        
        if (!$don) {
            return ['success' => false, 'error' => 'Don introuvable'];
        }
        
        // Créer le PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Informations du document
        $pdf->SetCreator('Play to Help');
        $pdf->SetAuthor('Play to Help');
        $pdf->SetTitle('Reçu de don #' . $don['id_don']);
        
        // Supprimer header/footer par défaut
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Ajouter une page
        $pdf->AddPage();
        
        // Définir la police
        $pdf->SetFont('helvetica', '', 12);
        
        // Contenu du PDF
        $html = self::getPDFTemplate($don);
        
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Nom du fichier
        $filename = 'recu_don_' . $don['id_don'] . '_' . date('Ymd') . '.pdf';
        $filepath = __DIR__ . '/../exports/dons/' . $filename;
        
        // Créer le dossier s'il n'existe pas
        $dir = __DIR__ . '/../exports/dons/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        // Sauvegarder le PDF
        $pdf->Output($filepath, 'F');
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath
        ];
    }
    
    private static function getPDFTemplate($don) {
        $montant_format = number_format($don['montant'], 2, ',', ' ');
        $date_format = date('d/m/Y', strtotime($don['date_don']));
        
        return '
        <style>
            h1 { color: #667eea; text-align: center; font-size: 24px; }
            h2 { color: #333; font-size: 18px; margin-top: 20px; }
            .header { text-align: center; margin-bottom: 30px; }
            .info-box { background-color: #f0f0f0; padding: 15px; margin: 20px 0; border-left: 5px solid #667eea; }
            .amount { font-size: 28px; font-weight: bold; color: #667eea; text-align: center; margin: 30px 0; }
            .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #666; }
            table { width: 100%; margin: 20px 0; }
            td { padding: 8px; }
            .label { font-weight: bold; width: 40%; }
        </style>
        
        <div class="header">
            <h1>🎮 PLAY TO HELP</h1>
            <p>Reçu de don fiscal</p>
        </div>
        
        <div class="info-box">
            <h2>Informations du don</h2>
            <table>
                <tr>
                    <td class="label">Numéro de reçu :</td>
                    <td>#' . str_pad($don['id_don'], 6, '0', STR_PAD_LEFT) . '</td>
                </tr>
                <tr>
                    <td class="label">Date du don :</td>
                    <td>' . $date_format . '</td>
                </tr>
                <tr>
                    <td class="label">Association bénéficiaire :</td>
                    <td>' . htmlspecialchars($don['association_nom']) . '</td>
                </tr>
            </table>
        </div>
        
        <div class="amount">
            Montant : ' . $montant_format . ' €
        </div>
        
        <div class="info-box">
            <h2>Informations du donateur</h2>
            <table>
                <tr>
                    <td class="label">Nom :</td>
                    <td>' . htmlspecialchars($don['nom_complet']) . '</td>
                </tr>
                <tr>
                    <td class="label">Email :</td>
                    <td>' . htmlspecialchars($don['email'] ?? 'N/A') . '</td>
                </tr>
            </table>
        </div>
        
        <p style="margin-top: 40px; text-align: justify;">
            Ce reçu atteste que <strong>' . htmlspecialchars($don['nom_complet']) . '</strong> a effectué un don de 
            <strong>' . $montant_format . ' €</strong> en faveur de l\'association 
            <strong>' . htmlspecialchars($don['association_nom']) . '</strong> via la plateforme Play to Help.
        </p>
        
        <p style="margin-top: 20px; font-style: italic; color: #666;">
            Ce don peut être déductible de vos impôts selon la législation en vigueur. 
            Conservez précieusement ce reçu pour votre déclaration fiscale.
        </p>
        
        <div class="footer">
            <p>Document généré automatiquement le ' . date('d/m/Y à H:i') . '</p>
            <p>Play to Help - Plateforme de dons gaming solidaires</p>
            <p>© ' . date('Y') . ' Play to Help - Tous droits réservés</p>
        </div>
        ';
    }
    
    public static function exportDonsCSV($filters = []) {
        global $conn;
        
        $query = "SELECT d.*, a.nom as association_nom FROM dons d JOIN associations a ON d.id_association = a.id WHERE 1=1";
        
        if (!empty($filters['search'])) {
            $search = $conn->real_escape_string($filters['search']);
            $query .= " AND (d.nom LIKE '%$search%' OR d.email LIKE '%$search%' OR a.nom LIKE '%$search%')";
        }
        
        if (!empty($filters['association'])) {
            $assoc = $conn->real_escape_string($filters['association']);
            $query .= " AND d.id_association = '$assoc'";
        }
        
        $query .= " ORDER BY d.date_don DESC";
        
        $result = $conn->query($query);
        
        $filename = 'export_dons_' . date('Ymd_His') . '.csv';
        $filepath = __DIR__ . '/../exports/dons/' . $filename;
        
        $file = fopen($filepath, 'w');
        
        // En-têtes CSV
        fputcsv($file, ['ID', 'Nom', 'Email', 'Montant', 'Association', 'Date'], ';');
        
        while ($row = $result->fetch_assoc()) {
            fputcsv($file, [
                $row['id'],
                $row['nom'],
                $row['email'],
                $row['montant'],
                $row['association_nom'],
                $row['date_don']
            ], ';');
        }
        
        fclose($file);
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath
        ];
    }
}
