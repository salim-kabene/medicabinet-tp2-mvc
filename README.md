# MédiCabinet — TP2 MVC

Application de gestion des rendez-vous d’un cabinet médical, migrée du TP1 vers une architecture MVC en PHP orienté objet avec Twig et MySQL. Les données de démonstration sont fictives. Le projet n’utilise pas JavaScript.

## Fonctionnalités

- Accueil statique, navigation et interface responsive.
- Liste des rendez-vous, filtres par date et statut, compteurs.
- Création, modification et suppression des rendez-vous après confirmation.
- Association d’un patient, d’un médecin et d’un ou plusieurs services à un rendez-vous.
- Liste des patients, fiche patient et historique des rendez-vous.
- Validation côté PHP, messages d’erreur et conservation des saisies.
- Protection CSRF des écritures et transactions pour les rendez-vous et leurs services.

## Architecture

| Emplacement | Responsabilité |
| --- | --- |
| `index.php` | Entrée unique de l’application |
| `config.php` | BASE, ASSET, fuseau horaire et session |
| `routes/` | Déclarations des routes et dispatch |
| `controllers/` | Traitement des requêtes et communication modèles/vues |
| `models/` | Requêtes SQL et relations |
| `providers/` | Rendu Twig et validation |
| `views/` | Templates Twig et composants de mise en page |
| `config/` | Configuration MySQL d’exemple et fichier privé local |
| `public/assets/` | CSS, icônes, polices et images |
| `database/` | Script SQL et données fictives |
| `docs/` | ERD et documentation |

Les anciens dossiers `Classe/` et `includes/` et les anciennes pages PHP de `public/` sont à retirer après vérification de leurs références. Ils ne font pas partie de l’architecture MVC finale.

## Installation

Prérequis : PHP 8.1 ou supérieur pour les dépendances du verrou examiné, PDO MySQL, Composer et MySQL 8.0.16 ou supérieur pour les contraintes CHECK du script. Composer vérifie les autres extensions nécessaires.

Depuis la racine du projet :

```bash
composer install
composer check-platform-reqs
cp -n config/database.example.php config/database.php
```

Compléter `config/database.php` avec les identifiants MySQL. Ce fichier privé doit rester ignoré par Git.

Importer `database/medicabinet.sql`. Attention : une réimportation supprime et recrée les cinq tables et leurs données fictives initiales. Ne pas le réimporter sur des données à conserver.

Dans `config.php`, pour une application accessible à la racine :

```php
define("BASE", "");
define("ASSET", BASE . "/public/assets");
```

Démarrer le serveur :

```bash
php -S localhost:8000 router.php
```

Ouvrir http://localhost:8000/. La commande du TP1 utilisant `-t public` ne convient plus : l’entrée MVC est à la racine du projet.

## Base de données

| Table | Contenu |
| --- | --- |
| `patients` | Identité et coordonnées des patients |
| `doctors` | Identité et spécialité des médecins |
| `services` | Nom et durée des services |
| `appointments` | Patient, médecin, date, heure, statut et motif |
| `appointment_service` | Association entre rendez-vous et services |

Les patients et médecins sont reliés aux rendez-vous par des relations un-à-plusieurs. La relation plusieurs-à-plusieurs entre rendez-vous et services utilise `appointment_service`. Supprimer un rendez-vous supprime ses associations, en conservant les patients, médecins et services.

Les compteurs dépendent de la date courante dans le fuseau `America/Toronto`. Les données de démonstration d’août et septembre 2026 ne restent pas indéfiniment dans les compteurs « Aujourd’hui » et « À venir ».

## Vérifications avant remise

Cette liste indique les tests à exécuter sur la dernière version locale puis hébergée ; elle ne constitue pas un relevé de tests déjà réussis.

- Créer un rendez-vous avec plusieurs services ; vérifier sa présence dans la liste et l’historique patient.
- Modifier sa date, son statut et ses services ; vérifier le remplacement des associations.
- Soumettre un formulaire sans service ; vérifier les erreurs et la conservation des autres champs.
- Vérifier côté serveur les champs manquants, les identifiants inexistants, les dates/heures invalides, les statuts inconnus et le motif de plus de 500 caractères.
- Vérifier le refus d’une écriture avec un jeton CSRF absent ou incorrect.
- Annuler une suppression, puis supprimer un rendez-vous de test ; vérifier la conservation des données liées.
- Tester les identifiants absents, invalides et inexistants et une route inconnue.
- Tester les liens de retour, les confirmations et erreurs, le clavier et les vues à 390 px et 1440 px.

## Hébergement et remise

Installer le TP2 dans un dossier distinct du TP1. La racine du site doit pointer vers le dossier contenant l’`index.php` MVC. Apache utilise `.htaccess` ; le serveur local PHP utilise `router.php`.

Sur le serveur, installer les dépendances avec `composer install --no-dev --optimize-autoloader`, puis vérifier `composer check-platform-reqs --no-dev`. Créer le fichier privé `config/database.php` sur place. Adapter le nom de base à celui imposé par l’hébergeur lors de l’import, sans réinitialiser les données du TP1.

Pour un sous-domaine pointant directement sur le projet, conserver BASE vide. Pour une URL avec sous-dossier, utiliser uniquement ce préfixe. Vérifier que les fichiers internes ne sont pas exposés publiquement et que les détails des erreurs restent dans les journaux du serveur.

Le ZIP final contient à sa racine : le dossier du code complet, le rapport Word/PDF et le script SQL. Le rapport contient les liens GitHub et site TP2, la description des fonctionnalités, l’ERD et la structure de la base.

Le ZIP de remise inclut les dépendances nécessaires au fonctionnement. `vendor/` reste ignoré dans Git ; `composer.json` et `composer.lock` permettent leur installation. Fournir une configuration d’exemple et les instructions d’installation, sans inclure de mot de passe réel.

Le code ZIP, GitHub et hébergé doit correspondre à la même version finale.

## Auteur

Salim Kabene — Programmation Web avancée, Collège de Maisonneuve.

Dépôt : https://github.com/salim-kabene/medicabinet-tp2-mvc
