# Module Tableau de Bord de Trésorerie pour Dolibarr

Ce module ajoute un tableau de bord financier complet à Dolibarr, offrant une vue claire et détaillée de la trésorerie mensuelle et une synthèse annuelle des flux financiers. Il est conçu pour aider les gestionnaires à anticiper les flux de trésorerie et à analyser l'activité financière.

---

## Fonctionnalités

- **Tableau de Synthèse Mensuel :** Affiche une vue d'ensemble combinant les flux **réalisés et prévisionnels** du mois en cours pour :

  - Les entrées totales (TTC).
  - La TVA sur les entrées.
  - Les sorties fournisseurs.
  - Les charges et dépenses spéciales.
  - La TVA à décaisser (estimation sur les débits).
  - La synthèse nette du mois.

- **Vue Annuelle Interactive :** Un tableau récapitulatif présentant les indicateurs clés mois par mois pour une année complète, avec les totaux annuels. La page inclut un sélecteur pour choisir l'année à afficher.

- **Suivi des Retards :** Des tableaux dédiés pour identifier rapidement les factures clients et fournisseurs dont la date de règlement est dépassée.

- **Listes Détaillées :** Sépare clairement les transactions prévisionnelles (factures à encaisser/payer, charges à régler) de l'activité déjà réalisée (factures réglées, charges payées) pour un suivi précis.

- **Graphiques Visuels :** Deux graphiques sur la vue mensuelle pour visualiser la synthèse des flux de trésorerie et l'activité commerciale hebdomadaire.

---

## Prérequis

- **Dolibarr ERP/CRM :** Version 7.0 ou supérieure.
- **Modules Dolibarr activés :**
  - Factures
  - Banques et Caisses
  - Notes de Frais
  - Taxes, TVA et Charges Sociales

---

## Installation

1.  Copiez le dossier `tresoreriemensuelle` dans le répertoire `htdocs/custom/` de votre installation Dolibarr.

2.  Connectez-vous à Dolibarr en tant qu'administrateur.

3.  Allez dans `Accueil > Configuration > Modules/Applications`.

4.  Trouvez le module **"Tableau de Bord Trésorerie"** dans la liste et cliquez sur le bouton d'activation.

5.  Allez dans `Accueil > Utilisateurs & Groupes`, sélectionnez un utilisateur ou un groupe, et dans l'onglet "Permissions", accordez le droit **"Voir le tableau de bord de trésorerie"**.

---

## Utilisation

Une fois le module activé, une nouvelle entrée de menu nommée **"Trésorerie"** apparaît dans la barre de menu supérieure.

- **Trésorerie > Vue Mensuelle :** Affiche le tableau de bord détaillé pour le mois en cours.
- **Trésorerie > Vue Annuelle :** Affiche le résumé financier pour chaque mois de l'année. Utilisez le champ en haut de cette page pour changer l'année affichée.

---
