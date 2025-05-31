<?php
// Get the base64 encoded images
$npcLogoPath = __DIR__ . '/public/img/npc-logo.png';
$icsLogoPath = __DIR__ . '/public/img/ics-logo.png';

if (file_exists($npcLogoPath) && file_exists($icsLogoPath)) {
    $npcLogo = base64_encode(file_get_contents($npcLogoPath));
    $icsLogo = base64_encode(file_get_contents($icsLogoPath));
    
    echo "NPC Logo (first 100 chars): " . substr($npcLogo, 0, 100) . "...\n";
    echo "Length: " . strlen($npcLogo) . " characters\n\n";
    
    echo "ICS Logo (first 100 chars): " . substr($icsLogo, 0, 100) . "...\n";
    echo "Length: " . strlen($icsLogo) . " characters\n\n";
    
    // Save to files for reference
    file_put_contents('npc_logo_base64.txt', $npcLogo);
    file_put_contents('ics_logo_base64.txt', $icsLogo);
    
    echo "Base64 encoded images saved to files.\n";
} else {
    echo "Error: Logo files not found.\n";
    if (!file_exists($npcLogoPath)) {
        echo "NPC Logo not found at: $npcLogoPath\n";
    }
    if (!file_exists($icsLogoPath)) {
        echo "ICS Logo not found at: $icsLogoPath\n";
    }
}
?>
