# Comptes de test - FEA-COACH

Ce document liste tous les comptes de test créés par les seeders.

## 🔐 Comptes utilisateurs

### Admin

- **Email**: `admin@fea-coach.com`
- **Mot de passe**: `password`
- **Rôle**: Admin
- **Accès**: Dashboard d'administration

### Coachs

#### 1. Pierre Martin (Actif)

- **Email**: `pierre@example.com`
- **Mot de passe**: `password`
- **Rôle**: Coach
- **Slug**: `pierre-martin`
- **URL**: `http://pierre-martin.kineseducation.academy` (en local: configuration requise)
- **Couleurs**: 
  - Primaire: `#3b82f6` (bleu)
  - Secondaire: `#8b5cf6` (violet)

#### 2. Sophie Dubois (Actif)

- **Email**: `sophie@example.com`
- **Mot de passe**: `password`
- **Rôle**: Coach
- **Slug**: `sophie-dubois`
- **URL**: `http://sophie-dubois.kineseducation.academy` (en local: configuration requise)
- **Couleurs**:
  - Primaire: `#ec4899` (rose)
  - Secondaire: `#f59e0b` (orange)

#### 3. Thomas Leroy (Inactif)

- **Email**: `thomas@example.com`
- **Mot de passe**: `password`
- **Rôle**: Coach
- **Slug**: `thomas-leroy`
- **Statut**: Inactif (ne s'affichera pas publiquement)
- **Couleurs**:
  - Primaire: `#10b981` (vert)
  - Secondaire: `#3b82f6` (bleu)

---

## 📊 Données générées

### Plans tarifaires (par coach)

Chaque coach actif dispose de 4 plans :

1. **Découverte** - 49,99€
   - Séance d'essai
   
2. **Suivi Mensuel** - 199,99€
   - 4 séances par mois
   - Programme nutritionnel
   
3. **Transformation 3 mois** - 549,99€
   - 12 séances
   - Plan nutritionnel personnalisé
   - Suivi quotidien
   
4. **Premium VIP** - 999,99€
   - Séances illimitées
   - Disponibilité 7j/7
   - Note: Actif uniquement pour Pierre Martin

### Transformations

#### Pierre Martin & Sophie Dubois (4 transformations chacun)

1. Transformation -15kg en 3 mois
2. Prise de masse musculaire
3. Remise en forme post-grossesse
4. Préparation marathon

#### Thomas Leroy

Aucune transformation (compte inactif)

---

## 🧪 Tests recommandés

### Authentification

```bash
# Se connecter en tant que coach
Email: pierre@example.com
Password: password

# Se connecter en tant qu'admin
Email: admin@fea-coach.com
Password: password
```

### Accès multi-tenant

Pour tester les sous-domaines en local, ajouter à `C:\Windows\System32\drivers\etc\hosts` :

```
127.0.0.1 pierre-martin.localhost
127.0.0.1 sophie-dubois.localhost
127.0.0.1 thomas-leroy.localhost
```

Puis accéder via :
- `http://pierre-martin.localhost:8000`
- `http://sophie-dubois.localhost:8000`

### Commandes utiles

```bash
# Réinitialiser et reseed la base
php artisan migrate:fresh --seed

# Afficher les données
php artisan tinker
>>> App\Models\Coach::with('user', 'plans', 'transformations')->get()

# Lister les coachs
php artisan tinker --execute="App\Models\Coach::all()->pluck('name', 'slug')"
```

---

## 📝 Notes

- Tous les mots de passe sont `password` (à changer en production!)
- Les URLs Calendly sont des exemples et doivent être remplacées
- Les images des transformations ne sont pas encore uploadées (Media Library configurée)
- Thomas Leroy est inactif pour tester le filtrage `is_active`

---

_Document créé le 12 novembre 2025_
