<?php

require '../../main.inc.php';

require_once DOL_DOCUMENT_ROOT . '/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/company.lib.php';

$langs->loadLangs(array("bills", "banks", "splitpayment@splitpayment"));

// --- Permissions check
if (empty($user->rights->facture->paiement)) {
    accessforbidden();
}

$facid = GETPOST('id', 'int');
$action = GETPOST('action', 'alpha');

$facture = new Facture($db);
if ($facid) {
    $facture->fetch($facid);
    $facture->fetch_thirdparty();
}

$default_currency = $conf->currency;

// ==============================================================================
//  TRAITEMENT DU FORMULAIRE (Action "addpayment")
// ==============================================================================

if ($action == 'addpayment' && !empty($facid)) {
    $error = 0;

    $amount1 = price2num(GETPOST('amount1', 'alpha'));
    $amount2 = price2num(GETPOST('amount2', 'alpha'));
    $taux_change = price2num(GETPOST('taux_change', 'alpha'));
    
    $datep_ts = dol_mktime(12, 0, 0, GETPOST('pday', 'int'), GETPOST('pmonth', 'int'), GETPOST('pyear', 'int'));
    $datep_sql = $db->idate($datep_ts);
    $payment_mode_id = (int) GETPOST('mode_reglement_id', 'int');
    $label = $db->escape(GETPOST('label', 'alpha'));
    $fk_bank1 = (int) GETPOST('fk_bank1', 'int');
    $fk_bank2 = (int) GETPOST('fk_bank2', 'int');

    if ($taux_change <= 0 && $amount2 > 0) {
         setEventMessage("Erreur : Le taux de change est obligatoire si un montant doit être converti.", 'errors');
         $error++;
    }
    
    if ($error == 0) {
        $db->begin();
        $batch_ref = 'SPLIT-'.dol_print_date(dol_now(), '%y%m%d%H%M%S').'-'.$facture->ref;
        $global_result = 1;

        // Fonction pour le paiement standard (Partie 1 - Devise étrangère)
        function create_standard_payment_sql($db, $facture, $date_sql, $amount, $mode_id, $label, $fk_bank, $batch_ref, $user) {
            if ($amount <= 0) return 1;
            // Cette fonction est maintenant correcte et validée
            $sql1 = "INSERT INTO ".MAIN_DB_PREFIX."paiement (entity, datec, datep, amount, fk_paiement, num_paiement, fk_user_creat) VALUES (".$facture->entity.", NOW(), '".$date_sql."', ".$amount.", ".$mode_id.", '".$label."', ".$user->id.")";
            $resql1 = $db->query($sql1);
            if (!$resql1) { setEventMessage("SQL Error Step 1.1: ".$db->lasterror(), 'errors'); return -1; }
            $payment_id = $db->db->insert_id;
            $sql2 = "INSERT INTO ".MAIN_DB_PREFIX."paiement_facture (fk_paiement, fk_facture, amount) VALUES (".$payment_id.", ".$facture->id.", ".$amount.")";
            $resql2 = $db->query($sql2);
            if (!$resql2) { setEventMessage("SQL Error Step 1.2: ".$db->lasterror(), 'errors'); return -1; }
            $sql3 = "INSERT INTO ".MAIN_DB_PREFIX."payment_extrafields (fk_object, batch_ref) VALUES (".$payment_id.", '".$db->escape($batch_ref)."')";
            $resql3 = $db->query($sql3);
            if (!$resql3) { setEventMessage("SQL Error Step 1.3: ".$db->lasterror(), 'errors'); return -1; }
            $payment_ref = 'PA'.dol_print_date(dol_now(), '%y%m').'-'.str_pad($payment_id, 4, '0', STR_PAD_LEFT);
            $bank_label = "Paiement facture " . $facture->ref;
            $sql4 = "INSERT INTO ".MAIN_DB_PREFIX."bank (dateo, datev, amount, label, fk_account, fk_user_author, fk_type, num_releve) VALUES ('".$date_sql."', '".$date_sql."', ".$amount.", '".$db->escape($bank_label)."', ".$fk_bank.", ".$user->id.", 'payment', '".$db->escape($payment_ref)."')";
            $resql4 = $db->query($sql4);
            if (!$resql4) { setEventMessage("SQL Error Step 1.4: ".$db->lasterror(), 'errors'); return -1; }
            $bank_line_id = $db->db->insert_id;
            $sql5 = "INSERT INTO ".MAIN_DB_PREFIX."bank_url (fk_bank, url_id, label) VALUES (".$bank_line_id.", ".$payment_id.", '(paiement)')";
            $resql5 = $db->query($sql5);
            if (!$resql5) { setEventMessage("SQL Error Step 1.5: ".$db->lasterror(), 'errors'); return -1; }
            return 1;
        }

        // Fonction corrigée pour le paiement multi-devises v14 (Partie 2 - Convertie en MAD)
        function create_multicurrency_payment_sql($db, $facture, $date_sql, $amount_foreign, $rate, $mode_id, $label, $fk_bank, $batch_ref, $user) {
            if ($amount_foreign <= 0) return 1;
            
            $amount_local = $amount_foreign * $rate; // Calcul du montant en MAD

            // ÉTAPE 1 : Insérer dans llx_paiement LE MONTANT REÇU EN MAD
            $sql1 = "INSERT INTO ".MAIN_DB_PREFIX."paiement (entity, datec, datep, amount, fk_paiement, num_paiement, fk_user_creat)";
            $sql1 .= " VALUES (".$facture->entity.", NOW(), '".$date_sql."', ".$amount_local.", ".$mode_id.", '".$label."', ".$user->id.")";
            $resql1 = $db->query($sql1);
            if (!$resql1) { setEventMessage("<b>Erreur SQL (Étape 2.1):</b><br>".$db->lasterror(), 'errors'); return -1; }
            $payment_id = $db->db->insert_id;
            
            // ÉTAPE 2 : Lier à la facture en utilisant LE MONTANT D'ORIGINE EN EUR
            $sql2 = "INSERT INTO ".MAIN_DB_PREFIX."paiement_facture (fk_paiement, fk_facture, amount) VALUES (".$payment_id.", ".$facture->id.", ".$amount_foreign.")";
            $resql2 = $db->query($sql2);
            if (!$resql2) { setEventMessage("<b>Erreur SQL (Étape 2.2):</b><br>".$db->lasterror(), 'errors'); return -1; }

            // ÉTAPE 3 : Extrafield (inchangé)
            $sql3 = "INSERT INTO ".MAIN_DB_PREFIX."payment_extrafields (fk_object, batch_ref) VALUES (".$payment_id.", '".$db->escape($batch_ref)."')";
            $resql3 = $db->query($sql3);
            if (!$resql3) { setEventMessage("<b>Erreur SQL (Étape 2.3):</b><br>".$db->lasterror(), 'errors'); return -1; }

            // ÉTAPE 4 : Créer l'écriture en banque AVEC LE MONTANT REÇU EN MAD
            $payment_ref = 'PA'.dol_print_date(dol_now(), '%y%m').'-'.str_pad($payment_id, 4, '0', STR_PAD_LEFT);
            $bank_label = "Paiement facture " . $facture->ref;
            $sql4 = "INSERT INTO ".MAIN_DB_PREFIX."bank (dateo, datev, amount, label, fk_account, fk_user_author, fk_type, num_releve)";
            $sql4 .= " VALUES ('".$date_sql."', '".$date_sql."', ".$amount_local.", '".$db->escape($bank_label)."', ".$fk_bank.", ".$user->id.", 'payment', '".$db->escape($payment_ref)."')";
            $resql4 = $db->query($sql4);
            if (!$resql4) { setEventMessage("<b>Erreur SQL (Étape 2.4):</b><br>".$db->lasterror(), 'errors'); return -1; }
            $bank_line_id = $db->db->insert_id;

            // ÉTAPE 5 : Liaison banque (inchangé)
            $sql5 = "INSERT INTO ".MAIN_DB_PREFIX."bank_url (fk_bank, url_id, label) VALUES (".$bank_line_id.", ".$payment_id.", '(paiement)')";
            $resql5 = $db->query($sql5);
            if (!$resql5) { setEventMessage("<b>Erreur SQL (Étape 2.5):</b><br>".$db->lasterror(), 'errors'); return -1; }

            return 1;
        }

        if ($global_result > 0) {
            $global_result = create_standard_payment_sql($db, $facture, $datep_sql, $amount1, $payment_mode_id, $label, $fk_bank1, $batch_ref, $user);
        }
        if ($global_result > 0) {
            $global_result = create_multicurrency_payment_sql($db, $facture, $datep_sql, $amount2, $taux_change, $payment_mode_id, $label, $fk_bank2, $batch_ref, $user);
        }

        if ($global_result > 0) {
            $facture->fetch($facid);
            if ($facture->statut == 1 && price2num($facture->total_ttc - $facture->getSommePaiement()) < 0.01) {
                $facture->set_paid($user);
            }
        }

        if ($global_result > 0) {
            $db->commit();
            setEventMessage("Paiement ventilé enregistré avec succès.", 'mesgs');
            header('Location: '.DOL_URL_ROOT.'/compta/facture/card.php?id='.$facid);
            exit;
        } else {
            $db->rollback();
        }
    }
}

// ==============================================================================
//  AFFICHAGE DU FORMULAIRE (inchangé)
// ==============================================================================
llxHeader('', 'Saisir un règlement ventilé');
print load_fiche_titre('Saisir un règlement ventilé sur la facture ' . $facture->ref);
dol_htmloutput_errors();

$form = new Form($db);
?>
<form name="splitpaymentform" action="<?php echo $_SERVER["PHP_SELF"]; ?>?id=<?php echo $facid; ?>" method="POST">
<input type="hidden" name="token" value="<?php echo $_SESSION['newtoken']; ?>">
<input type="hidden" name="action" value="addpayment">
<input type="hidden" name="id" value="<?php echo $facid; ?>">

<table class="border" width="100%">
    <tr>
        <td class="titlefieldcreate"><?php echo $langs->trans("Date"); ?></td>
        <td><?php print $form->select_date(dol_now(), 'p', '', '', '', "splitpaymentform"); ?></td>
    </tr>
    <tr>
        <td class="titlefieldcreate"><?php echo $langs->trans("PaymentMode"); ?></td>
        <td><?php $form->select_types_paiements(GETPOST('mode_reglement_id', 'int'), 'mode_reglement_id'); ?></td>
    </tr>
    <tr>
        <td class="titlefieldcreate">Montant total reçu (<?php echo $facture->multicurrency_code; ?>)</td>
        <td><input type="text" name="total_amount" id="total_amount" value="<?php echo price($facture->total_ttc - $facture->getSommePaiement()); ?>"></td>
    </tr>
     <tr>
        <td class="titlefieldcreate">Libellé / Référence</td>
        <td><input type="text" name="label" class="maxwidth" value="<?php echo dol_escape_htmltag(GETPOST('label', 'alpha')); ?>"></td>
    </tr>
    <tr class="liste_titre">
        <td colspan="2">Ventilation 1 (versement en <?php echo $facture->multicurrency_code; ?>)</td>
    </tr>
    <tr>
        <td class="titlefieldcreate">Montant à verser (<?php echo $facture->multicurrency_code; ?>)</td>
        <td><input type="text" name="amount1" id="amount1" style="width: 100px;" value="<?php echo dol_escape_htmltag(GETPOST('amount1', 'alpha')); ?>"></td>
    </tr>
    <tr>
        <td class="titlefieldcreate">Compte de destination (<?php echo $facture->multicurrency_code; ?>)</td>
        <td><?php $form->select_comptes(GETPOST('fk_bank1', 'int'), 'fk_bank1', 0, '', 1); ?></td>
    </tr>
    <tr class="liste_titre">
        <td colspan="2">Ventilation 2 (conversion en <?php echo $default_currency; ?>)</td>
    </tr>
    <tr>
        <td class="titlefieldcreate">Montant à convertir (<?php echo $facture->multicurrency_code; ?>)</td>
        <td><input type="text" name="amount2" id="amount2" style="width: 100px;" value="<?php echo dol_escape_htmltag(GETPOST('amount2', 'alpha')); ?>"></td>
    </tr>
    <tr>
        <td class="titlefieldcreate">Taux de change (1 <?php echo $facture->multicurrency_code; ?> = X <?php echo $default_currency; ?>)</td>
        <td><input type="text" name="taux_change" id="taux_change" style="width: 100px;" value="<?php echo dol_escape_htmltag(GETPOST('taux_change', 'alpha')); ?>"></td>
    </tr>
    <tr>
        <td class="titlefieldcreate">Montant converti (<?php echo $default_currency; ?>)</td>
        <td><input type="text" name="montant_converti" id="montant_converti" style="width: 100px; font-weight: bold; background-color: #eee;" disabled="disabled"></td>
    </tr>
    <tr>
        <td class="titlefieldcreate">Compte de destination (<?php echo $default_currency; ?>)</td>
        <td><?php $form->select_comptes(GETPOST('fk_bank2', 'int'), 'fk_bank2', 0, '', 1); ?></td>
    </tr>
     <tr class="liste_titre">
        <td colspan="2">Récapitulatif</td>
    </tr>
    <tr>
        <td class="titlefieldcreate">Reste à ventiler (<?php echo $facture->multicurrency_code; ?>)</td>
        <td><span id="remaining" style="font-weight: bold; color: red;"></span></td>
    </tr>
</table>

<div class="center">
    <br><input type="submit" class="button" value="Enregistrer le règlement">
    &nbsp;&nbsp;&nbsp;
    <a href="<?php echo DOL_URL_ROOT.'/compta/facture/card.php?id='.$facid; ?>" class="button">Annuler</a>
</div>
</form>

<script type="text/javascript">
jQuery(document).ready(function() {
    function parseFrenchFloat(numStr) {
        if (!numStr) return 0;
        var cleanedStr = String(numStr).replace(/\s/g, '').replace(',', '.');
        var number = parseFloat(cleanedStr);
        return isNaN(number) ? 0 : number;
    }
    function formatNumber(num) {
        return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num);
    }
    function calculate() {
        var amount2 = parseFrenchFloat(jQuery('#amount2').val());
        var rate = parseFrenchFloat(jQuery('#taux_change').val());
        var convertedAmount = amount2 * rate;
        jQuery('#montant_converti').val(formatNumber(convertedAmount));
        var total = parseFrenchFloat(jQuery('#total_amount').val());
        var amount1 = parseFrenchFloat(jQuery('#amount1').val());
        var remaining = total - amount1 - amount2; 
        jQuery('#remaining').text(formatNumber(remaining)); 
        if (remaining.toFixed(2) == 0.00) {
            jQuery('#remaining').css('color', 'green');
        } else {
            jQuery('#remaining').css('color', 'red');
        }
    }
    calculate();
    jQuery('#total_amount, #amount1, #amount2, #taux_change').on('keyup change', function() {
        calculate();
    });
});
</script>

<?php
llxFooter();
$db->close();
?>