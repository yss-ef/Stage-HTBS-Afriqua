<?php
header('Content-Type: application/javascript');
?>
$(document).ready(function() {
    console.clear();
    console.log("--- DEBUG CustomTax --- LOG 1: Script Démarré.");

    if (window.location.pathname.includes('compta/facture/card.php')) {
        console.log("--- DEBUG CustomTax --- LOG 2: Page de facture détectée.");

        // On récupère l'ID de la facture avec du pur JavaScript
        var urlParams = new URLSearchParams(window.location.search);
        var facid = urlParams.get('id') || urlParams.get('facid');
        console.log("--- DEBUG CustomTax --- LOG 3: ID de la facture lu depuis l'URL = " + facid);

        if (facid) {
            var taxNameSelector = '#facture_extras_custom_tax_name_' + facid;
            var taxNameElement = $(taxNameSelector);
            console.log("--- DEBUG CustomTax --- LOG 4: Recherche du nom de la taxe avec le sélecteur : '" + taxNameSelector + "'. Trouvés : " + taxNameElement.length);

            if (taxNameElement.length > 0) {
                var taxNameValue = taxNameElement.text().trim();
                console.log("--- DEBUG CustomTax --- LOG 5: Nom de la taxe extrait : '" + taxNameValue + "'");

                if (taxNameValue) {
                    console.log("--- DEBUG CustomTax --- LOG 6: Application des remplacements...");
                    
                    $('#title_vat').parent().text(taxNameValue);
                    $('th.linecolvat:contains("TVA")').text(taxNameValue);
                    $(".tableforfield td:contains('Montant TVA')").text('Montant ' + taxNameValue);

                    console.log("--- DEBUG CustomTax --- LOG 7: Remplacements terminés.");
                }
            }
        }
    }
});