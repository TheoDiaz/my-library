# Ma Biblio - Application de Gestion de Bibliothèque

Application mobile/web de gestion de bibliothèque personnelle développée avec Angular et Ionic.

## Prérequis

- Node.js (v14 ou supérieur)
- npm (v6 ou supérieur)
- Ionic CLI (`npm install -g @ionic/cli`)

## Installation

1. Cloner le repository :
```bash
git clone [URL_DU_REPO]
cd frontend
```

2. Installer les dépendances :
```bash
npm install
```

3. Lancer l'application en mode développement :
```bash
ionic serve
```

## Structure du Projet

```
src/
├── app/
│   ├── core/           # Services globaux, intercepteurs, guards
│   ├── shared/         # Composants réutilisables
│   └── features/       # Modules fonctionnels
├── assets/            # Images, icônes, etc.
├── environments/      # Configuration par environnement
└── theme/            # Variables de thème et styles globaux
```

## Fonctionnalités

- Recherche de livres
- Gestion de bibliothèque personnelle
- Suivi des lectures
- Notation et commentaires
- Statistiques de lecture
- Gestion des prêts
- Catégories personnalisées
- Liste de souhaits
- Thème clair/sombre

## Développement

### Commandes utiles

- `ionic serve` : Lance le serveur de développement
- `ionic build` : Compile l'application
- `ionic test` : Lance les tests unitaires
- `ionic lint` : Vérifie le code avec ESLint

### Configuration

- Les variables d'environnement sont dans `src/environments/`
- Le thème est configuré dans `src/theme/variables.scss`
- Les styles globaux sont dans `src/global.scss`

## Contribution

1. Créer une branche pour votre fonctionnalité
2. Commiter vos changements
3. Pousser vers la branche
4. Créer une Pull Request

## Licence

[À DÉFINIR] 