# Module CustomTax pour Dolibarr

Le module **CustomTax** est une extension pour l'ERP Dolibarr conçue pour offrir plus de flexibilité dans la gestion des libellés de taxe sur les factures. Il permet de personnaliser le nom de la taxe (par exemple, "TVA", "VAT", "GST", "Sales Tax") directement sur chaque facture, ce qui est particulièrement utile pour les entreprises travaillant avec une clientèle internationale.

Ce module résout le problème des libellés de taxe fixes en ajoutant un champ personnalisé sur les factures et en utilisant un modèle de document ODT pour afficher dynamiquement le nom de taxe choisi.

---

## 2. Installation

1.  Copiez le dossier du module `customtax` dans le répertoire `<racine_dolibarr>/htdocs/custom/`.
2.  Connectez-vous à votre instance Dolibarr avec un compte administrateur.
3.  Allez dans **Accueil -> Configuration -> Modules/Applications**.
4.  Trouvez le module **CustomTax** dans la liste et cliquez sur le bouton "Activer".

---

## 3. Configuration (Étape Manuelle Obligatoire)

Pour que le module soit fonctionnel, une configuration manuelle est **indispensable** après l'activation. Cette étape ne doit être réalisée qu'une seule fois.

1.  Depuis le menu principal, naviguez vers : **Accueil -> Configuration -> Modules/Applications**.
2.  Recherchez le module **Factures et Avoirs** et cliquez sur son icône de configuration (roue crantée).
3.  Accédez à l'onglet **"Attributs supplémentaires (factures)"**.
4.  Cliquez sur le bouton **"Nouvel attribut"**.
5.  Remplissez le formulaire avec les valeurs **exactes** ci-dessous :
    - **Libellé ou clé de traduction :** `Nom de la taxe`
    - **Code de l'attribut :** `custom_tax_name`
    - **Type :** `Chaine de caractères (1 ligne)`
    - **Valeur par défaut :** `TVA`
    - **Visibilité :** `1`
6.  Enregistrez l'attribut.

---

## 4. Ajout du Modèle de Document (Étape Manuelle Obligatoire)

Une fois l'attribut créé, vous devez ajouter le modèle de document `.odt` personnalisé qui permettra d'afficher le nom de la taxe.

1.  Toujours dans la configuration du module **Factures et Avoirs** (Accueil -> Modules/Applications -> Paramètres de "Factures et Avoirs").
2.  Dans l'onglet **"Modèles de documents"**, repérez la section pour les factures.
3.  Utilisez le formulaire d'ajout en bas de la page : cliquez sur **"Choisir un fichier"** et sélectionnez le fichier `.odt` fourni avec ce module.
4.  Cliquez sur le bouton **"Ajouter modèle"** pour téléverser le document.
5.  Le nouveau modèle devrait maintenant apparaître dans la liste des modèles de factures disponibles.

---

## 5. Utilisation

Une fois le module installé, l'attribut configuré et le modèle ajouté :

1.  Créez une nouvelle facture ou modifiez-en une existante.
2.  Un nouveau champ intitulé **"Nom de la taxe"** apparaîtra.
3.  Saisissez le libellé de taxe souhaité dans ce champ (par exemple, "VAT").
4.  Pour générer le document, dans le menu déroulant "Modèle à utiliser", **sélectionnez le nouveau modèle** que vous venez d'ajouter.
5.  Cliquez sur "Générer". Le document final affichera le nom de la taxe que vous avez saisi.
