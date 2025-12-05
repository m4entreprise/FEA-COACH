# ✅ Checklist - Activation Fungies.io

## 📅 À faire une fois le store approuvé

### 1. Vérifier l'activation du store
- [ ] Le store Fungies.io est approuvé et actif
- [ ] Le plan "FEA Coach Pro" est publié et actif
- [ ] Les 2 SKUs sont configurés:
  - [ ] `fea-coach-pro-graduate` (diplômés FEA avec trial)
  - [ ] `fea-coach-pro-standard` (non-FEA)

### 2. Tester l'API
```bash
# Sur le VPS
php test-fungies.php
```
- [ ] L'API GET /v0/subscriptions/list retourne 200
- [ ] L'API POST /v0/elements/checkout/create retourne 200 (ou au moins pas 404)

### 3. Configuration finale

**Option A: Si l'API checkout fonctionne**
- [ ] Ajuster `FungiesService::createCheckoutSession()` avec le bon format
- [ ] Tester la création de checkout

**Option B: Si seuls les Payment Links fonctionnent**
- [ ] Récupérer le Payment Link du dashboard Fungies
- [ ] Ajouter dans `.env`: `FUNGIES_CHECKOUT_URL=https://fungies.io/checkout/...`
- [ ] Modifier `FungiesService::createCheckoutSession()` pour utiliser l'URL statique

### 4. Configurer le Webhook
- [ ] URL configurée: `https://VOTRE-VPS/webhooks/fungies`
- [ ] Secret configuré: `JQv7drTp/bWNkueR6XumbkLC7iogBJ3G8lcxGO0EJas=`
- [ ] Tous les événements activés:
  - [ ] payment_success
  - [ ] subscription_created
  - [ ] subscription_updated
  - [ ] subscription_cancelled
  - [ ] subscription_interval
  - [ ] payment_failed
  - [ ] payment_refunded

### 5. Tests complets

**Test 1: Diplômé FEA**
- [ ] Inscription → Step 1 (FEA) → Step 2 → Step 3
- [ ] Demande code promo FEA
- [ ] Admin approuve → 1 mois gratuit
- [ ] Setup wizard accessible

**Test 2: Non-diplômé FEA**
- [ ] Inscription → Step 1 (non-FEA) → Step 2 → Step 3
- [ ] Clic "Continuer vers le paiement (20€ HTVA/mois)"
- [ ] Redirection vers Fungies Checkout
- [ ] Paiement avec carte test: `4242 4242 4242 4242`
- [ ] Webhook `subscription_created` reçu
- [ ] User.subscription_status = 'active'
- [ ] Setup wizard accessible

**Test 3: Dashboard abonnement**
- [ ] Accès `/dashboard/subscription`
- [ ] Affichage correct des infos abonnement
- [ ] Bouton "Gérer mon abonnement" → Customer Portal Fungies

**Test 4: Annulation**
- [ ] Clic "Annuler l'abonnement"
- [ ] cancel_at_period_end = true
- [ ] Accès maintenu jusqu'à la fin de période
- [ ] Webhook `subscription_cancelled` reçu

### 6. Logs et monitoring
```bash
# Vérifier les logs
tail -f storage/logs/laravel.log | grep Fungies

# Vérifier les webhooks dans le dashboard Fungies
```
- [ ] Pas d'erreurs dans les logs Laravel
- [ ] Tous les webhooks envoyés par Fungies sont reçus (200 OK)

### 7. Production ready
- [ ] Tester avec une vraie carte (pas test mode)
- [ ] Vérifier que les factures Fungies sont envoyées
- [ ] Vérifier que les emails de notifications fonctionnent
- [ ] Tests sur tous les navigateurs
- [ ] Tests sur mobile

---

## 🐛 En cas de problème

### API retourne 401/403
→ Vérifier les clés API dans `.env`

### Checkout ne redirige pas
→ Vérifier que `returnUrl` et `cancelUrl` sont configurés

### Webhook non reçu
→ Vérifier que l'URL webhook est accessible publiquement
→ Vérifier les logs Fungies dans leur dashboard

### User non activé après paiement
→ Vérifier les logs du webhook
→ Vérifier que l'email ou le userId est bien passé

---

## 📞 Support

- **Discord Fungies.io:** https://discord.gg/yfH5ZyTZH4
- **Help Center:** https://help.fungies.io
- **Email:** support@fungies.io (à vérifier)

---

**Date d'activation prévue:** ___________
**Date des tests:** ___________
**Mise en production:** 12 décembre 2025
