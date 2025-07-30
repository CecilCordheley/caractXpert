# CaracXpert

[![License](https://img.shields.io/badge/License-Apache_2.0-blue.svg)](https://opensource.org/licenses/Apache-2.0)

**CaracXpert** est un système expert conçu pour améliorer la **coordination** des équipes et garantir une **gestion des rôles fluide et efficace** dans les environnements complexes (support technique, supervision, maintenance...).

Le projet part d’un constat simple : trop d’outils favorisent soit le contrôle autoritaire, soit l’absence de structure claire. CaracXpert apporte une réponse humaine et logique.

---

## 🚀 Objectif

Permettre à chaque acteur d’un système d’avoir une place utile et définie, sans surcharger ni bloquer.

- ✅ Coordination renforcée
- ✅ Visibilité sur les pannes détectées
- ✅ Droits différenciés selon le rôle
- ✅ Historique clair et traçabilité
- ✅ Détection et désaccords sur les pannes
- ✅ Interface simple, modulaire et extensible

---

## 🔐 Rôles et droits

| Rôle      | Capacités principales |
|-----------|------------------------|
| **Agent**   | Identifie les pannes, peut commenter, consulter l’historique |
| **Manager** | Crée/modifie des pannes, gère les comptes assignés |
| **Admin**   | Gère tous les comptes et les pannes, peut exporter les données |
| **Dev**     | Console, logs, triggers automatiques |

---

## 📊 Modules inclus

- Détection des pannes par les agents
- Fiche de désaccord (disclaim) si désaccord sur un diagnostic
- Vue des historiques personnalisés
- Tableau de bord statistique (via Chart.js)
- Gestion utilisateur multi-niveaux
- Système de jetons sécurisé (TokenManager)

---

## 🔧 Stack technique

- **Backend** : PHP 8+, EasyFramework
- **Frontend** : HTML / JS natif + Chart.js
- **Base de données** : MySQL
- **JSON** : pour la gestion de sessions et de délégations

---

## 🛠️ Installation

```bash
git clone https://github.com/CecilCordheley/caractXpert.git
cd caractXpert
# Configurez votre base de données, puis :
php -S localhost:8000