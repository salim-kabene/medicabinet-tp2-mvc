# MédiCabinet — TP3 MVC

MédiCabinet est une application de gestion des rendez-vous d’un cabinet médical. Le TP3 prolonge le TP2 avec une interface administrateur, un espace client et une gestion des accès. Le projet utilise PHP orienté objet, Twig et MySQL. Les données de démonstration sont fictives. L’interface fonctionne sans JavaScript.

## Fonctionnalités

- **Visiteur** — accueil, connexion et choix de langue.
- **Client** — consultation des rendez-vous du patient associé au compte, téléchargement d’un récapitulatif PDF et gestion de sa photo de profil.
- **Administrateur** — création, modification et suppression des rendez-vous, filtres et compteurs, consultation des fiches patients et de leur historique, création de comptes client ou administrateur et consultation du journal.
- **Journal de bord** — enregistrement en base de l’adresse IP, de la date et l’heure, de l’identifiant de connexion ou de la mention « Visiteur », et du chemin demandé. L’interface paginée est accessible depuis le menu administrateur uniquement.
- **Accès** — hachage des mots de passe avec bcrypt, vérification à la connexion, renouvellement de l’identifiant de session, contrôle des rôles côté serveur et protection CSRF des formulaires POST.

## Architecture

| Emplacement | Rôle |
| --- | --- |
| `index.php`, `config.php` | Point d’entrée, configuration et session |
| `routes/` | Déclaration et distribution des requêtes |
| `controllers/` | Traitement des requêtes, validation et autorisations |
| `models/` | Accès à MySQL avec PDO et transactions |
| `providers/` | Rendu Twig, authentification, validation, journal, PDF, photos et traduction |
| `views/`, `lang/` | Vues et libellés français et anglais |
| `public/assets/` | CSS, icônes, images et polices |
| `storage/avatars/` | Photos de profil, accessibles via le contrôleur |
| `database/`, `docs/` | Script d’installation et documentation |

## Fonctionnalités ajoutées au TP3

| Fonctionnalité | Objectif et implémentation |
| --- | --- |
| Récapitulatif PDF | `PdfController` vérifie l’accès, récupère les rendez-vous via `Appointment` et rend `views/pdf/appointments.php`. `providers/Pdf.php` utilise Dompdf pour convertir le HTML et transmettre le PDF en téléchargement. L’utilisateur peut l’enregistrer sur son appareil. |
| Photo de profil | `ProfileController` traite l’ajout, le remplacement et la suppression. `ImageUpload` vérifie le type réel avec Fileinfo, la taille et les dimensions. Le fichier JPEG ou PNG reçoit un nom aléatoire dans `storage/avatars/`. Le modèle `Users` enregistre ce nom dans `users.photo`. Le contrôleur sert la photo du compte connecté. |
| Application bilingue | `LanguageController` valide le choix FR/EN, le stocke en session et revient sur une page interne autorisée. `Lang` charge `lang/fr.php` ou `lang/en.php`. `View` transmet les traductions à Twig. Les contrôleurs utilisent également ces dictionnaires pour les messages. |

Twig et Dompdf sont déclarés dans `composer.json`, verrouillés dans `composer.lock` et chargés par `vendor/autoload.php`. Twig rend les vues avec échappement HTML et Dompdf produit le document à télécharger. PDO MySQL connecte les modèles à la base et Fileinfo identifie le type des images téléversées.

## Installation

Prérequis **PHP 8.1+**, Composer et **MySQL 8.0.16+**. Activer PDO MySQL, DOM, Mbstring, Iconv et Fileinfo. Depuis la racine, dans Git Bash ou un terminal compatible Bash :

```bash
composer install
composer check-platform-reqs
cp -n config/database.example.php config/database.php
```

1. Créer ou sélectionner une **base vide réservée au TP3**, puis importer **une seule fois** `database/medicabinet.sql`. Le script sélectionne `wrkewdnksm_medicabinet_v3_bdd` avec `USE`. Pour une installation locale, crée une base TP3 distincte et adapte cette ligne. **Chaque import supprime puis recrée les huit tables, y compris les comptes et les visites.** Les données fictives et les deux rôles sont insérés après la recréation.
2. Renseigner la connexion MySQL dans `config/database.php`, qui est ignoré par Git.
3. Vérifier que PHP peut écrire dans `storage/avatars/`.
4. Préparer le compte administrateur dans `users` sur l’installation cible. Utiliser un identifiant au format courriel, un hachage bcrypt généré avec `password_hash($motDePasse, PASSWORD_BCRYPT)`, `privilege_id = 1` et `patient_id = NULL`. L’administrateur peut ensuite créer le compte client depuis l’interface en l’associant à un patient fictif. Pour le site de démonstration hébergé, utiliser les accès transmis dans le rapport de remise.

Le fichier `database/medicabinet.sql` regroupe le schéma complet du TP3.

```bash
php -S localhost:8001 router.php
```

Ouvrir [http://localhost:8001/](http://localhost:8001/). Pour une installation à la racine du domaine, conserver `BASE` vide dans `config.php`.

## Base de données

| Table | Contenu |
| --- | --- |
| `patients` et `doctors` | Patients et médecins |
| `services` | Nom et durée des services |
| `appointments` | Patient, médecin, date et heure, statut et motif |
| `appointment_service` | Lien entre rendez-vous et services |
| `privilege` | Rôles administrateur et client |
| `users` | Identité, identifiant, hachage, rôle, patient associé et nom de la photo |
| `logs` | Visites, utilisateur éventuel, IP, page, méthode et date |

Le patient associé à un compte est facultatif et unique. Un compte client doit être associé à un patient. Le lien du journal vers un compte est facultatif et passe à `NULL` si ce compte est supprimé. Les compteurs des rendez-vous utilisent le fuseau `America/Toronto` et la date courante, ce qui explique des compteurs à zéro avec des rendez-vous fictifs passés.

## Hébergement et remise

[Site de démonstration TP3](https://medicabinet-v3.e2595926.webdevmaisonneuve.ca/)

Déployer le TP3 dans un dossier et une base propres au TP3. Faire pointer le sous-domaine HTTPS vers le dossier contenant `index.php`. Sous Apache 2.4, activer `mod_rewrite` et autoriser les règles du `.htaccess` racine. Sur l’hébergeur, vérifier que l’accueil, la connexion et les ressources CSS répondent, et que l’accès HTTP aux fichiers internes est refusé. Créer la configuration privée MySQL sur place.

Pour préparer les dépendances du déploiement ou du ZIP :

```bash
composer install --no-dev --optimize-autoloader
composer check-platform-reqs --no-dev
```

Le ZIP de remise contient à sa racine le dossier du code complet avec ses dépendances, le rapport Word ou PDF et une copie de `medicabinet.sql`. Le rapport contient les liens du dépôt public et du site hébergé, la description du projet, les fonctionnalités ajoutées et leur implémentation, le diagramme ER à jour et les accès de démonstration. Le code du ZIP, de GitHub et du site hébergé doit correspondre à la même version. Exclure la configuration MySQL privée du ZIP et du dépôt.

## Vérifications avant remise

- Importer le SQL dans une base vide et tester la connexion et la déconnexion avec les deux rôles.
- Vérifier que le client voit ses rendez-vous et que l’accès direct au journal et aux opérations administrateur lui est refusé.
- Créer, modifier et supprimer un rendez-vous fictif. Vérifier la validation, la confirmation de suppression et les associations aux services.
- Contrôler le journal comme visiteur, puis après connexion.
- Changer la langue sur une fiche ou une page paginée et vérifier que l’adresse est conservée.
- Télécharger le PDF en français et en anglais. Ajouter, remplacer et supprimer une photo, puis essayer un format non autorisé.
- Vérifier les parcours sur l’environnement hébergé et sur écran étroit.

## Auteur

Salim Kabene — Programmation Web avancée, Collège de Maisonneuve.

[Dépôt public MédiCabinet](https://github.com/salim-kabene/medicabinet-tp2-mvc)
