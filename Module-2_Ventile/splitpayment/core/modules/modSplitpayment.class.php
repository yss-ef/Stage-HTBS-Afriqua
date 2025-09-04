<?php
/* Copyright (C) 2024-2025 SuperAdmin
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 */

include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

/**
 * Description and activation class for module Splitpayment
 */
class modSplitpayment extends DolibarrModules
{
    /**
     * Constructor. Define names, constants, directories, boxes, permissions
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        global $conf, $langs;

        $this->db = $db;

        $this->numero = 500016; // Numéro d'identification du module
        $this->rights_class = 'splitpayment';
        $this->family = "financial"; // Famille du module (Compta/Finance)
        $this->module_position = '90';

        $this->name = preg_replace('/^mod/i', '', get_class($this));
        $this->description = "Permet de ventiler un règlement sur plusieurs comptes bancaires.";
        $this->version = '3.0';
        $this->editor_name = 'Youssef Fellah';
        $this->editor_url  = 'https://github.com/yss-ef';

        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
        $this->picto = 'dollar'; // Utilise une icône Font Awesome

        // IMPORTANT : On active les triggers. C'est essentiel pour que notre bouton apparaisse.
        $this->module_parts = array(
            'triggers' => 1,
            'hooks' => array('invoicecard') // On précise le contexte de notre hook
        );

        // Répertoire pour les fichiers temporaires
        $this->dirs = array("/splitpayment/temp");

        // Fichier de langue
        $this->langfiles = array("splitpayment@splitpayment");

        // Prérequis de version
        $this->phpmin = array(7, 1);
        $this->need_dolibarr_version = array(14, 0); // Compatible à partir de la v14

        $this->const = array();
        $this->tabs = array();
        $this->rights = array();
        $this->menu = array();
    }

    /**
     * Function called when module is enabled.
     *
     * @param string $options Options when enabling module ('', 'nobox', 'log')
     * @return int             1 if OK, 0 if KO
     */
    public function init($options = '')
    {
        // --- DEBUT DU CODE DE DÉBOGAGE ---
        require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';
        $elementtype = 'payment';
        $extrafields = new ExtraFields($this->db);

        $existing_fields = $extrafields->fetch_name_optionals_label($elementtype);
        if (empty($existing_fields['batch_ref']))
        {
            // On exécute la création et on stocke le résultat
            $result = $extrafields->addExtraField(
                'batch_ref', 'Référence de ventilation', 'varchar',
                0, 50, $elementtype, 1, '', '1', 0, 0, 0, 0, 0, 0
            );

            // Si le résultat est une erreur (inférieur à 0), on l'affiche et on arrête tout.
            if ($result < 0) {
                dol_print_error($this->db, $extrafields->error);
                exit;
            }
        }
        // --- FIN DU CODE DE DÉBOGAGE ---

        $sql = array();
        return $this->_init($sql, $options);
    }

    /**
     * Function called when module is disabled.
     *
     * @param  string $options Options when enabling module ('', 'noboxes')
     * @return int             1 if OK, <=0 if KO
     */
    public function remove($options = '')
    {
        // --- DEBUT DU CODE FINAL ---
        require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';
        $extrafields = new ExtraFields($this->db);
        $elementtype = 'payment';
        $fieldname = 'batch_ref';
        $fieldid = 0;

        // Approche directe et fiable : on cherche l'ID du champ dans la table des extrafields
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."extrafields";
        $sql .= " WHERE elementtype = '".$this->db->escape($elementtype)."' AND name = '".$this->db->escape($fieldname)."'";

        $resql = $this->db->query($sql);
        if ($resql) {
            $obj = $this->db->fetch_object($resql);
            if ($obj) {
                $fieldid = $obj->rowid;
            }
        }

        // Si on a trouvé l'ID, on supprime le champ
        if ($fieldid > 0) {
            // CORRECTION : La méthode s'appelle delete() et non deleteExtraField()
            $extrafields->delete($fieldid);
        }
        // --- FIN DU CODE FINAL ---
        
        $sql = array();
        return $this->_remove($sql, $options);
    }
}