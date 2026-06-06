# Chronos 🎓

**Chronos** est une plateforme éducative complète et "full-stack" conçue pour moderniser et simplifier la gestion des emplois du temps et la navigation au sein d'un établissement scolaire ou universitaire. Le projet est divisé en une application d'administration web centralisée et une application mobile multiplateforme pour les utilisateurs finaux.

---

## 📖 Description Globale
L'objectif principal de Chronos est d'offrir une solution tout-en-un pour gérer la vie académique. L'application permet non seulement d'assigner des professeurs, des cours et des classes à des salles spécifiques, mais elle intègre également une fonctionnalité novatrice : **une carte interactive du campus** permettant aux étudiants et au personnel de s'orienter facilement.

---

## 🌟 Fonctionnalités Principales

*   📅 **Gestion Intelligente des Emplois du Temps :** Mise en relation dynamique des professeurs (`PROF`), des modules de cours (`COURS`), des salles de classe (`SALLE`), et des promotions/classes (`CLASSE`).
*   🗺️ **Carte Interactive du Campus :** Un système de grille personnalisable (`CARTE_LAYOUT`) qui cartographie virtuellement les infrastructures de l'université (routes, entrées, salles spécifiques comme "Salle Info" ou "Salle Biblio").
*   🔐 **Contrôle d'Accès Basé sur les Rôles (RBAC) :** Le système distingue quatre types d'utilisateurs avec des interfaces et des droits d'accès distincts :
    *   **Administrateur :** Accès total à la gestion de la base de données via le tableau de bord web.
    *   **Étudiant :** Peut consulter son emploi du temps spécifique à sa classe et utiliser la carte.
    *   **Professeur :** Visualise ses heures de cours et ses salles assignées.
    *   **Agent de Sécurité :** Dispose d'un aperçu global ou spécifique pour gérer les accès et la sécurité du campus.
*   📱 **Application Mobile Dédiée :** Une application portable et réactive permettant de garder son emploi du temps dans sa poche.

---

## 🏗️ Architecture du Projet et Hébergement

Chronos est structuré en trois grandes couches (Base de données, Serveur Web / API, et Client Mobile). L'hébergement de l'infrastructure backend est entièrement confié à **Alwaysdata**, ce qui permet à l'application mobile de fonctionner partout dans le monde de manière autonome, sans dépendre d'un serveur local (PC).

### 1. La Base de Données (`chronos_db.sql`)
Hébergée sur les serveurs MySQL/MariaDB d'**Alwaysdata** (ex: `mysql-chronos.alwaysdata.net`).
*   Elle agit comme la source de vérité unique pour toutes les données de l'établissement.
*   Gère l'authentification sécurisée des utilisateurs (avec des mots de passe hachés via `bcrypt`).
*   Gère le système de jetons d'accès (`API_TOKENS`) pour sécuriser les requêtes de l'application mobile.

### 2. L'Application Web et l'API REST (`WebApp/`)
Écrite en **PHP, HTML, CSS, et JavaScript**, cette partie est déployée dans le répertoire public (`www/`) du serveur **Alwaysdata**. Elle remplit deux rôles fondamentaux :
*   **Le Portail d'Administration (`admin/`) :** Un site web complet hébergé (ex: `https://chronos.alwaysdata.net/TIMETABLE_APP/admin/`) qui sert de tour de contrôle. C'est ici que les administrateurs se connectent pour saisir les emplois du temps, dessiner la carte du campus sur la grille interactive, et gérer les comptes utilisateurs.
*   **Le Fournisseur d'API REST (`api/`) :** C'est le cœur de la communication. Ce serveur écoute en permanence les requêtes HTTP provenant de l'application mobile. Lorsqu'un étudiant ouvre l'application sur son téléphone, l'API Alwaysdata s'occupe de l'authentifier, va chercher son emploi du temps dans la base de données MySQL, et lui renvoie les informations formatées en JSON.

### 3. L'Application Mobile (`MobileApp/chronos/`)
Une application moderne et multiplateforme développée avec le framework **Flutter** (Dart).
*   **100% Client-Side :** L'application ne contient aucune base de données locale complexe. Elle consomme exclusivement les données fournies par l'API REST d'Alwaysdata.
*   **Portabilité :** Compilable pour Android (`.apk`), iOS, ou même en version Web directement exécutable dans un navigateur (`flutter run -d chrome`).

---

## 🛠️ Stack Technologique (Technologies Utilisées)

*   **Frontend Mobile :** Flutter, Dart (Packages: `http` pour les requêtes API, gestionnaires d'état).
*   **Backend & API :** PHP (PDO pour la sécurité des requêtes SQL), Architecture RESTful.
*   **Frontend Web (Admin) :** HTML5, Vanilla CSS, JavaScript.
*   **Base de données :** MySQL / MariaDB (Relationnelle).
*   **Infrastructure Cloud :** Alwaysdata (Hébergement mutualisé/dédié).

---

## ⚙️ Guide d'Installation et de Déploiement

### Prérequis
*   Un compte **Alwaysdata** actif.
*   Le **Flutter SDK** installé sur votre machine locale pour compiler l'application mobile.
*   Un client FTP (ex: FileZilla) pour transférer les fichiers sur le serveur.

### Étape 1 : Déploiement du Backend sur Alwaysdata
1.  **Base de données :** Connectez-vous à phpMyAdmin sur Alwaysdata, créez une nouvelle base de données (ex: `chronos_db`), et importez le fichier `chronos_db.sql`.
2.  **Configuration :** Ouvrez le fichier `WebApp/config/db.php` et mettez à jour les informations de connexion avec vos identifiants Alwaysdata (Host, Nom de la DB, Utilisateur, Mot de passe).
3.  **Mise en ligne :** Via FTP, transférez l'intégralité du contenu du dossier `WebApp` vers le dossier racine public de votre hébergement Alwaysdata (généralement `www/`).

### Étape 2 : Configuration de l'Application Mobile (Flutter)
1.  Ouvrez un terminal et naviguez vers le dossier mobile : `cd MobileApp/chronos`
2.  Téléchargez les dépendances du projet : `flutter pub get`
3.  Ouvrez le fichier `lib/services/api_service.dart`.
4.  Assurez-vous que la variable `baseUrl` pointe exactement vers le dossier de votre API sur Alwaysdata (ex: `https://votre-compte.alwaysdata.net/TIMETABLE_APP/api`).

### Étape 3 : Tests et Compilation
*   **Pour tester rapidement sur PC :** Lancez la commande `flutter run -d chrome`. L'application s'ouvrira dans votre navigateur web, connectée à votre API en ligne.
*   **Pour générer l'application Android :** Lancez `flutter build apk`. Une fois la compilation terminée, transférez le fichier `.apk` généré sur votre smartphone Android pour l'installer.

---
*Projet développé dans le cadre de la formation / INITIATION.*
