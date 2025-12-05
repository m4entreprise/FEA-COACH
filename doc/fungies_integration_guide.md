# Guide d'intégration Fungies.io pour FEA-COACH

## 📋 Vue d'ensemble

Ce guide explique comment configurer et utiliser l'intégration Fungies.io pour gérer les abonnements SaaS de FEA-COACH.

**Modèle économique:**
- FEA-COACH vend des abonnements aux coaches (20€ HTVA/mois)
- Fungies.io gère la TVA, les factures, et la conformité fiscale
- Les coaches gèrent leurs propres paiements clients en dehors de la plateforme

---

## 🔧 Configuration initiale

### 1. Compte Fungies.io

1. Créez un compte sur [https://fungies.io](https://fungies.io)
2. Accédez à la section **Developers** du dashboard
3. Récupérez vos clés API:
   - **API Key (public)**: `pub_...`
   - **Write API Key (secret)**: `sec_...`

### 2. Créer votre plan d'abonnement

1. Dans votre dashboard Fungies.io, créez un nouveau **Plan/Product**:
   - **Nom**: FEA Coach Pro
   - **Prix**: 20€ HTVA/mois
   - **Interval**: Mensuel
   - **Description**: Plateforme complète pour coaches FEA

2. Récupérez l'**ID du plan** (format: `plan_xxx`)

### 3. Configuration des variables d'environnement

Ajoutez ces lignes à votre fichier `.env`:

```env
# Fungies.io Configuration
FUNGIES_API_KEY=pub_n+QMjT+koWFwxx4ZqvDnMSjIbugRnrYuuOzh94FliE0=
FUNGIES_WRITE_API_KEY=sec_HQnmOLIN4JnC1sTK0DGzDEpX7ZEnYzHQuIqXJi7IZi0=
FUNGIES_PLAN_ID=plan_your_plan_id_here
FUNGIES_WEBHOOK_SECRET=JQv7drTp/bWNkueR6XumbkLC7iogBJ3G8lcxGO0EJas=
```

**⚠️ Important:** Remplacez `plan_your_plan_id_here` par votre vrai ID de plan Fungies.io.

### 4. Configuration du Webhook

Dans votre dashboard Fungies.io, configurez le webhook:

**URL du webhook:**
- **Développement local**: Utilisez ngrok ou expose
  ```bash
  ngrok http 8000
  # Puis utilisez: https://xxxx.ngrok.io/webhooks/fungies
  ```
- **Production**: `https://kineseducation.academy/webhooks/fungies`

**Secret du webhook:**
```
JQv7drTp/bWNkueR6XumbkLC7iogBJ3G8lcxGO0EJas=
```

**Événements à activer:**
- ✅ payment_success
- ✅ subscription_created
- ✅ subscription_updated
- ✅ subscription_cancelled
- ✅ subscription_interval
- ✅ payment_failed
- ✅ payment_refunded

---

## 🚀 Fonctionnalités implémentées

### 1. Checkout pour nouveau abonnement

**Route**: `POST /dashboard/subscription/checkout`
**Contrôleur**: `SubscriptionController::createCheckoutSession`

Crée une session de paiement Fungies et redirige l'utilisateur vers la page de checkout.

**Flux:**
1. Utilisateur clique sur "S'abonner maintenant"
2. Backend crée une session via API Fungies
3. Redirection vers Fungies Checkout
4. Après paiement → webhook `subscription_created`
5. Activation automatique du compte

### 2. Customer Portal

**Route**: `POST /dashboard/subscription/portal`
**Contrôleur**: `SubscriptionController::customerPortal`

Redirige vers le portail client Fungies où l'utilisateur peut:
- Voir ses factures
- Mettre à jour ses informations de paiement
- Annuler son abonnement

### 3. Annulation d'abonnement

**Route**: `POST /dashboard/subscription/cancel`
**Contrôleur**: `SubscriptionController::cancelSubscription`

Annule l'abonnement à la fin de la période de facturation (pas de remboursement).

### 4. Webhooks Fungies

**Route**: `POST /webhooks/fungies`
**Contrôleur**: `FungiesWebhookController::handle`

Gère tous les événements Fungies.io:

| Événement | Action |
|-----------|--------|
| `subscription_created` | Active l'abonnement, stocke les IDs |
| `subscription_updated` | Met à jour le statut et la période |
| `subscription_cancelled` | Marque l'annulation à la fin de période |
| `subscription_interval` | Renouvelle l'abonnement (nouveau cycle) |
| `payment_success` | Confirme le paiement |
| `payment_failed` | Notifie l'échec (à implémenter) |
| `payment_refunded` | Confirme le remboursement |

---

## 📊 Structure de la base de données

### Champs ajoutés à la table `users`:

| Champ | Type | Description |
|-------|------|-------------|
| `fungies_customer_id` | string | ID client Fungies.io |
| `fungies_subscription_id` | string | ID abonnement actif |
| `subscription_status` | string | Status: trial, active, cancelled, etc. |
| `trial_ends_at` | datetime | Date de fin d'essai |
| `subscription_current_period_start` | datetime | Début période de facturation |
| `subscription_current_period_end` | datetime | Fin période de facturation |
| `cancel_at_period_end` | boolean | Annulation programmée |

---

## 🧪 Tests

### Test en développement local

1. **Installer ngrok**:
   ```bash
   npm install -g ngrok
   # ou téléchargez depuis https://ngrok.com
   ```

2. **Lancer ngrok**:
   ```bash
   ngrok http 8000
   ```

3. **Copier l'URL ngrok** et configurez-la dans Fungies.io

4. **Tester le checkout**:
   - Allez sur `/dashboard/subscription`
   - Cliquez sur "S'abonner maintenant"
   - Complétez le checkout en mode test

5. **Vérifier les webhooks**:
   - Vérifiez les logs Laravel: `storage/logs/laravel.log`
   - Vérifiez la table `users` pour voir les mises à jour

### Test de la carte en mode test

Fungies.io utilise Stripe en backend, donc vous pouvez utiliser les cartes de test Stripe:

- **Succès**: `4242 4242 4242 4242`
- **Échec**: `4000 0000 0000 0002`
- **Date**: N'importe quelle date future
- **CVC**: N'importe quel 3 chiffres

---

## 🔄 Workflow complet

### Pour diplômés FEA:

```
1. Inscription → Onboarding Step 1-2
   ↓
2. Step 3: Demande code promo FEA
   ↓
3. Admin approuve → 1 mois d'essai gratuit
   ↓
4. Setup wizard → Configuration site
   ↓
5. Dashboard accessible
   ↓
6. Fin d'essai → Notification
   ↓
7. Clic "S'abonner" → Fungies Checkout
   ↓
8. Paiement → webhook subscription_created
   ↓
9. Compte activé avec abonnement actif
```

### Pour non-diplômés FEA:

```
1. Inscription → Onboarding Step 1-2
   ↓
2. Step 3: Bouton "Payer 20€/mois"
   ↓
3. Fungies Checkout
   ↓
4. Paiement → webhook subscription_created
   ↓
5. Compte activé + Setup wizard
   ↓
6. Dashboard accessible
```

---

## 🚨 Gestion des erreurs

### Échec de paiement

Quand un paiement échoue:
1. Webhook `payment_failed` est reçu
2. Log l'erreur
3. **À implémenter**: Envoyer email de notification
4. **À implémenter**: Grace period de 3 jours
5. **À implémenter**: Désactivation après échecs multiples

### Annulation d'abonnement

Quand un utilisateur annule:
1. `cancel_at_period_end` = true
2. L'utilisateur garde accès jusqu'à la fin de la période
3. Webhook `subscription_cancelled` à la fin
4. Désactivation du compte

---

## 📧 Emails à implémenter (TODO)

Les emails suivants doivent être créés:

1. **Email de bienvenue** - Après création d'abonnement
2. **Email de confirmation de paiement** - Chaque renouvellement
3. **Email d'échec de paiement** - Quand un paiement échoue
4. **Email de fin d'essai** - 3 jours avant la fin d'essai
5. **Email d'annulation** - Confirmation d'annulation
6. **Email de désactivation** - Compte désactivé

---

## 🔐 Sécurité

### Vérification des webhooks

Tous les webhooks sont vérifiés via signature HMAC SHA256:

```php
$signature = hash_hmac('sha256', $payload, $secret);
if (!hash_equals($expectedSignature, $signature)) {
    // Rejeter la requête
}
```

### Protection CSRF

La route webhook est exemptée de CSRF:

```php
Route::post('/webhooks/fungies', ...)
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
```

---

## 🐛 Débogage

### Logs Laravel

Tous les événements Fungies sont logués:

```bash
tail -f storage/logs/laravel.log
```

### Vérifier un webhook manuellement

```bash
curl -X POST http://localhost:8000/webhooks/fungies \
  -H "Content-Type: application/json" \
  -H "X-Fungies-Signature: YOUR_SIGNATURE" \
  -d '{"event":"subscription_created",...}'
```

### Dashboard Fungies

Vérifiez les événements dans votre dashboard Fungies.io:
- Section "Webhooks" → Voir tous les webhooks envoyés
- Réessayer un webhook en cas d'échec

---

## 📚 Ressources

- [Documentation Fungies.io](https://help.fungies.io)
- [API Reference](https://help.fungies.io/for-saas-developers/getting-started-with-the-api)
- [Subscription API](https://help.fungies.io/for-saas-developers/managing-subscriptions-through-api)
- [Support Fungies.io](https://fungies.io/contact)

---

## ✅ Checklist avant production

- [ ] Créer le plan FEA Coach Pro sur Fungies.io
- [ ] Récupérer l'ID du plan et le mettre dans `.env`
- [ ] Configurer le webhook avec l'URL de production
- [ ] Tester le checkout en mode test
- [ ] Vérifier que les webhooks sont bien reçus
- [ ] Implémenter les emails de notification
- [ ] Configurer un monitoring des échecs de paiement
- [ ] Tester l'annulation d'abonnement
- [ ] Vérifier le customer portal
- [ ] Documenter le processus pour l'équipe

---

## 🎯 Prochaines étapes

1. **Onboarding non-FEA**: Intégrer le checkout dans Step3.vue
2. **Notifications email**: Créer tous les templates d'email
3. **Analytics**: Tracker les conversions et MRR
4. **Grace period**: Implémenter la logique de grace period
5. **Admin dashboard**: Stats abonnements, MRR, churn rate

---

**Dernière mise à jour**: 5 décembre 2025
**Version**: 1.0.0
**Auteur**: Claude (Assistant IA)
