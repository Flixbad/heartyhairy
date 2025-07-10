# 🍔 Hearty & Hairy — Projet de restaurant Symfony

Bienvenue dans le projet **Hearty & Hairy**, une application web gourmande réalisée avec **Symfony**, **EasyAdmin**, et **TailwindCSS**, destinée à la gestion d’un menu de restaurant moderne et dynamique.

---

## ✨ Fonctionnalités principales

- 📋 Affichage des plats par catégorie : Entrées, Plats, Desserts, Boissons
- 🛒 Ajout de plats au panier avec sélection de la quantité
- 💳 Page de paiement avec choix du mode : Carte bancaire, PayPal, Sur place
- 📁 Backoffice EasyAdmin pour gérer les plats et les catégories
- 🖼️ Page d’accueil stylisée avec galerie, horaires, informations de contact
- 🎨 Interface responsive et design chaleureux grâce à TailwindCSS

---

## 🔧 Technologies utilisées

| Technologie  | Description                       |
|--------------|-----------------------------------|
| Symfony 6.x  | Framework PHP principal           |
| EasyAdmin    | Gestion du backoffice             |
| Twig         | Moteur de template                |
| TailwindCSS  | Stylisation rapide et responsive |
| Doctrine ORM | Base de données relationnelle     |

---

## 🚀 Lancement du projet en local

```bash
git clone https://github.com/votre-utilisateur/hearty-hairy.git
cd hearty-hairy
composer install
npm install
npm run dev
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
symfony server:start
