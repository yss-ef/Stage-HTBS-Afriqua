# Module Splitpayment pour Dolibarr

Ce module pour Dolibarr ERP/CRM permet de ventiler un règlement de facture unique en deux transactions distinctes sur deux comptes bancaires différents. Il est spécialement conçu pour gérer les paiements internationaux où une partie est conservée dans une devise étrangère et l'autre est convertie dans la devise locale.

Ce module a été développé pour être compatible spécifiquement avec Dolibarr 14.0+. En raison des limitations de l'API de cette version, le module utilise des requêtes SQL directes pour garantir la robustesse et la cohérence des données comptables.

---

## Fonctionnalités

Bouton d'action : Ajoute un bouton "Ventiler un règlement" sur la fiche facture.

Ventilation simple : Permet de diviser un montant total en deux versements sur deux comptes bancaires distincts.

Gestion multi-devises : Gère la conversion d'une partie du paiement d'une devise étrangère vers la devise par défaut de Dolibarr.

Taux de change manuel : L'utilisateur saisit le taux de change réel appliqué lors de la transaction pour une comptabilité précise.

Calcul automatique : L'interface calcule et affiche en temps réel le montant converti.

Intégrité des données : Le module s'assure de créer correctement toutes les écritures nécessaires (paiement, liaison facture, extrafield, écriture en banque) pour maintenir la cohérence de la base de données.

---

## Fonctionnalités

- Bouton d'action : Ajoute un bouton "Ventiler un règlement" sur la fiche facture.

- Ventilation simple : Permet de diviser un montant total en deux versements sur deux comptes bancaires distincts.

- Gestion multi-devises : Gère la conversion d'une partie du paiement d'une devise étrangère vers la devise par défaut de Dolibarr.

- Taux de change manuel : L'utilisateur saisit le taux de change réel appliqué lors de la transaction pour une comptabilité précise.

- Calcul automatique : L'interface calcule et affiche en temps réel le montant converti.

- Intégrité des données : Le module s'assure de créer correctement toutes les écritures nécessaires (paiement, liaison facture, extrafield, écriture en banque) pour maintenir la cohérence de la base de données.

---

## Prérequis

- Dolibarr ERP/CRM : Version 14.0 ou supérieure.

- Module "Banques et Caisses" : Doit être activé.

- Module "Factures" : Doit être activé.

---

## Installation

1. Téléchargez les fichiers du module depuis ce dépôt GitHub.

2. Décompressez l'archive et copiez le répertoire splitpayment dans le dossier htdocs/custom/ de votre installation Dolibarr.

3. Connectez-vous à Dolibarr en tant qu'administrateur.

4. Allez dans Accueil -> Configuration -> Modules/Applications.

5. Trouvez le module "Splitpayment" dans la liste et cliquez sur le bouton d'activation.

Absolument ! Voici une proposition pour un fichier README.md complet et professionnel pour ton projet sur GitHub. Il explique le contexte, l'installation, et l'utilisation du module.

Tu peux copier-coller ce texte directement dans un fichier nommé README.md à la racine de ton projet.

Module Splitpayment pour Dolibarr
Ce module pour Dolibarr ERP/CRM permet de ventiler un règlement de facture unique en deux transactions distinctes sur deux comptes bancaires différents. Il est spécialement conçu pour gérer les paiements internationaux où une partie est conservée dans une devise étrangère et l'autre est convertie dans la devise locale.

Ce module a été développé pour être compatible spécifiquement avec Dolibarr 14.0+. En raison des limitations de l'API de cette version, le module utilise des requêtes SQL directes pour garantir la robustesse et la cohérence des données comptables.

## Installation 🔧

Téléchargez les fichiers du module depuis ce dépôt GitHub.

Décompressez l'archive et copiez le répertoire splitpayment dans le dossier htdocs/custom/ de votre installation Dolibarr.

Connectez-vous à Dolibarr en tant qu'administrateur.

Allez dans Accueil -> Configuration -> Modules/Applications.

Trouvez le module "Splitpayment" dans la liste et cliquez sur le bouton d'activation.

### Structure de la base de données

Lors de son activation, le module crée un attribut supplémentaire (extrafield) nommé batch_ref sur les paiements pour lier les deux transactions d'une même ventilation.

⚠️ Important : Ce module a été développé pour fonctionner avec une table d'extrafields manuellement créée, nommée llx_payment_extrafields. Assurez-vous que cette table existe dans votre base de données avec au minimum les colonnes suivantes :

- fk_object (INT) : Clé étrangère vers l'ID du paiement (llx_paiement.rowid).

- batch_ref (VARCHAR) : Champ pour stocker la référence de ventilation.

---

## Utilisation

1. Allez sur la fiche d'une facture client validée et non soldée.

2. Dans la zone "Ajouter règlement", cliquez sur le bouton "Ventiler un règlement".

3. Vous êtes redirigé vers le formulaire de ventilation. Remplissez les informations :

- Montant total reçu : Le montant total dans la devise de la facture.

- Ventilation 1 : Saisissez le montant à verser sans conversion et sélectionnez le compte bancaire de destination (généralement dans la même devise).

- Ventilation 2 : Saisissez le montant à convertir, le taux de change appliqué, et sélectionnez le compte bancaire de destination (généralement dans votre devise locale). Le montant converti se calcule automatiquement.

4. Cliquez sur "Enregistrer le règlement".

5. Le module crée alors deux enregistrements de paiement distincts, chacun lié à son compte bancaire, et vous redirige vers la facture qui est mise à jour.
