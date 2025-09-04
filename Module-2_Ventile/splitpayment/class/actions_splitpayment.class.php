<?php

class ActionsSplitpayment
{
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * This function is called by the HookManager to add buttons on cards.
     *
     * @param   array   $parameters     Hook parameters
     * @return  int                     0 if OK, <0 if KO
     */
    public function addMoreActionsButtons($parameters)
    {
        global $langs, $user, $object;

        if ($parameters['currentcontext'] == 'invoicecard' && !empty($object->id) && $object->statut == 1 && $object->total_ttc > 0)
        {
            if (!empty($user->rights->facture->paiement)) {
                $langs->load("splitpayment@splitpayment");
                print '<a class="butAction" href="'.DOL_URL_ROOT.'/custom/splitpayment/ventilate.php?id='.$object->id.'">'.$langs->trans("AddSplitPayment").'</a>';
            }
        }
        
        return 0;
    }
}