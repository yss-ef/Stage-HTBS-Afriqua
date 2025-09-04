# Modèle PV Intervention Personnalisé pour Dolibarr

Le modèle PV Intervention Personnalisé est un document PDF avancé pour l'ERP/CRM Dolibarr, conçu pour générer des procès-verbaux (PV) d'intervention sur mesure.

Il remplace les modèles standards par un design professionnel et ajoute des fonctionnalités intelligentes pour automatiser la saisie d'informations.

Ce modèle résout le problème des documents génériques en offrant un PV qui correspond à un besoin métier spécifique et qui s'adapte dynamiquement aux données de l'intervention.

---

## 2. Installation

1.  Téléchargez le fichier pdf_perso.modules.php.

2.  Connectez-vous à votre serveur (via FTP, cPanel, ou en local).

3.  Copiez le fichier pdf_perso.modules.php dans le répertoire suivant : <racine_dolibarr>/htdocs/core/modules/fichinter/doc/

---

## 3. Configuration (Étape Manuelle Obligatoire)

_Important_ : Pour que le modèle soit fonctionnel, une configuration manuelle des champs personnalisés est indispensable. Cette étape n'est à réaliser qu'une seule fois.

1.  Depuis le menu principal de Dolibarr, naviguez vers :

Accueil → Configuration → Modules/Applications.

2.  Recherchez le module Interventions et cliquez sur son icône de configuration (roue crantée ⚙️).

3.  Accédez à l'onglet "Attributs supplémentaires".

4.  Cliquez sur le bouton "Nouvel attribut" et créez les cinq attributs suivants en respectant exactement les valeurs ci-dessous :

| Libellé              | Code de l'attribut | Type                           |

| :------------------- | :----------------- | :----------------------------- |
| **Type d’intervention**        | `typeintervention`        | Chaîne de caractères (1 ligne) |

| **Intervenant**      | `intervenant`      | Chaîne de caractères (1 ligne) |

| **Objet**            | `objet`            | Chaîne de caractères (1 ligne) |

| **Lieu**             | `lieu`             | Chaîne de caractères (1 ligne) |

| **Numéro du marché** | `numeromarche`     | Chaîne de caractères (1 ligne) |

| **Trimestre**        | `trimestre`        | Chaîne de caractères (1 ligne) |

---

## 4. Activation du Modèle

Une fois le fichier copié et les attributs créés, retournez dans la configuration du module Interventions.

Le nouveau modèle (par exemple, "PV Intervention Final") devrait apparaître dans la liste des "Modèles de document des fiches d'intervention".

Cliquez sur l'interrupteur pour l'activer. Vous pouvez également le définir comme modèle par défaut en cliquant sur l'icône en forme d'étoile.

---

## 5. Utilisation

Une fois le modèle installé et les attributs configurés :

1.  Créez une nouvelle fiche d'intervention ou modifiez-en une existante.

2.  Ajoutez les différentes tâches dans la section des lignes en bas de la page, en précisant pour chacune une date, une heure et une durée.

3.  Dans le menu déroulant "Modèle à utiliser", sélectionnez votre nouveau modèle (ex: "PV Intervention Final").

4.  Cliquez à nouveau sur "Générer".

Le document final affichera toutes les informations, avec les dates et heures calculées automatiquement.

voila le readme de l'ancien modele modifie le pour le rendre compatible
