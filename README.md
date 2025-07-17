# 🍷 Hearty & Hairy — Application de restaurant

Bienvenue sur **Hearty & Hairy**, l’application web du restaurant raffiné qui permet à ses clients de commander en ligne, et à ses administrateurs de gérer les comptes, plats, et paiements.

---

## 🚀 Fonctionnalités

- 🧑‍🍳 Menu interactif avec ajout au panier
- 🛒 Panier dynamique (quantités, suppression, total)
- 💳 Paiement sécurisé via Stripe Checkout
- 👤 Espace “Mon compte” : profil, photo, mot de passe
- 📋 Historique de commandes
- 🔐 Gestion complète des utilisateurs en admin
- 🧭 Interface navbar personnalisée avec avatar, email et accès admin
- 🎨 Design doux avec Tailwind CSS et interactions fluides

---

## 🛠️ Technologies utilisées

- Symfony 6.x (PHP 8.x)
- Twig
- Doctrine ORM
- Stripe API
- Tailwind CSS

---

## 🔑 Accès admin

Les utilisateurs avec le rôle `ROLE_ADMIN` peuvent accéder à :

- `/admin/utilisateurs` : liste, édition et suppression des comptes utilisateurs
- Lien vers le panel admin visible uniquement dans le menu “Compte” pour les admins

---

## 📦 Structure des templates

templates/ ├── home/ → Page d’accueil ├── hearty/ → Menu du restaurant ├── order/ → Panier + Résumé de commande ├── user/ → Profil client + historique ├── admin/ → Panel d’administration


---

## 📂 Lancer le projet en local

```bash
composer install
symfony server:start


Configurer .env.local avec ta clé Stripe :
STRIPE_SECRET_KEY=sk_test_...


🎯 À venir
Recommander un plat déjà commandé
Statistiques de commandes dans le panel admin
Notification par email à la validation de commande


Hearty & Hairy™ — Bon appétit dans le code 👨‍🍳🍷