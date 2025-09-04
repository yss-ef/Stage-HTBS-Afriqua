<?php

include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

class modCustomTax extends DolibarrModules
{
    public function __construct($db)
    {
        parent::__construct($db);

        $this->db = $db;

        $this->numero = 500020; 
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        $this->description = "Permet de définir un nom de taxe personnalisé sur les factures et de remplacer les mentions 'TVA'.";
        $this->version = '1.0';
        $this->family = "financial"; 
        $this->rights_class = 'customtax'; 
        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
        
        $this->editor_name = 'Youssef Fellah'; 
        $this->editor_url  = 'https://github.com/yss-ef';

        $this->picto = 'tag'; 

        $this->module_parts = array(
            'js' => array(
                '/customtax/js/customtax.js.php'
            )
        );
        
        $this->langfiles = array("customtax@customtax");
    }

    public function init($options = '')
    {
        require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';
        $extrafields = new ExtraFields($this->db);
        $elementtype = 'facture';
        $existing_fields = $extrafields->fetch_name_optionals_label($elementtype);
        if (empty($existing_fields['custom_tax_name']))
        {
            $extrafields->addExtraField('custom_tax_name', "Nom de la taxe", 'varchar', 0, 255, $elementtype, 1, '', '1', 10);
        }
        return $this->_init(array(), $options);
    }

    public function remove($options = '')
    {
        require_once DOL_DOCUMENT_ROOT . '/core/class/extrafields.class.php';
        $extrafields = new ExtraFields($this->db);
        $elementtype = 'facture';
        $sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."extrafields WHERE elementtype = '".$this->db->escape($elementtype)."' AND name = 'custom_tax_name'";
        $resql = $this->db->query($sql);
        if ($resql && ($obj = $this->db->fetch_object($resql)) && $obj->rowid > 0) {
            $extrafields->delete($obj->rowid);
        }
        return $this->_remove(array(), $options);
    }
}