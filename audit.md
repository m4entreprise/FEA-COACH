# Audit projet FEA-COACH

## Portee
- Stack: Laravel 11 (PHP 8.2), Vue 3 + Inertia, Vite, Tailwind, Spatie (backup, medialibrary, activitylog), Lemon Squeezy, Stripe Connect.
- Domaine: SaaS multi-tenant (sous-domaines coach), site public, onboarding + wizard, CRM clients, documents, booking, paiement.

## Points forts
- Separation claire back/front (Controllers, Services, Policies, middleware).
- Multi-tenant via middleware + routage par sous-domaine.
- Usage de Purify pour HTML service description.
- Integrations paiement et abonnements structurees.
- Spatie backup/medialibrary deja en place.

## Risques et anomalies (priorises)
### Critique
- IDOR sur page de succes booking: `/booking/{booking}/success` accessible sans auth, expose email + code partage + lien client. `app/Http/Controllers/BookingController.php`, `routes/web.php`.

### Eleve
- Incoherence `primary_color` vs `color_primary` (SetupWizard/Onboarding/PromoCode vs DB/Model). Couleurs non persistantes, UI incoherente. `app/Http/Controllers/SetupWizardController.php`, `app/Http/Controllers/OnboardingController.php`, `app/Http/Controllers/Admin/PromoCodeRequestController.php`, `app/Models/Coach.php`, `database/migrations/2025_11_12_125629_create_coaches_table.php`, `resources/js/Pages/Setup/Step1.vue`.
- Vue manquante `Dashboard/BookingDetails` appelee par `BookingsController::show` (route 404). `app/Http/Controllers/Dashboard/BookingsController.php`.
- Session key mismatch pour acces coach au dashboard client (`client_access_*` vs `client_share_access_*`). `app/Http/Controllers/Dashboard/ClientController.php`, `app/Http/Controllers/ClientShareController.php`.
- Logs de PII et payloads webhooks en clair (email, notes, metadata). Risque RGPD/PII, surface d attaque. `app/Http/Controllers/BookingController.php`, `app/Services/BookingService.php`, `app/Services/LemonSqueezyService.php`, `app/Http/Controllers/LemonSqueezyWebhookController.php`, `app/Services/StripeConnectService.php`.
- `APP_DOMAIN` contient un port par defaut (`localhost:8000`), peut casser `Route::domain`. `.env.example`, `config/app.php`, `routes/web.php`.

### Moyen
- Donnees sensibles sante stockees en clair (blessures, stress, etc). Pas de chiffrement applicatif ni retention explicite. `app/Models/Client.php`, migrations.
- Pas de rate limiting sur endpoints publics (share code, contact, checkout). `routes/web.php`, `app/Http/Controllers/ClientShareController.php`, `app/Http/Controllers/CoachSiteController.php`.
- Verification Stripe signature sans tolerance sur timestamp (replay possible). `app/Http/Controllers/StripeWebhookController.php`.
- Generation slug auto sans controle d unicite dans `BrandingController` (risque exception unique). `app/Http/Controllers/Dashboard/BrandingController.php`.
- Creation booking non atomique (race condition sur disponibilite). `app/Services/BookingService.php`.
- Charges lourdes sur listing clients (charge toutes relations) + multiples count queries dans vues. `app/Http/Controllers/Dashboard/ClientController.php`, `app/Http/Controllers/ClientShareController.php`.
- Backup Spatie inclut `base_path()` sans exclusion de `.env` et sans encryption par defaut (si `BACKUP_ARCHIVE_PASSWORD` vide). `config/backup.php`.

### Faible
- Double source de verite onboarding: `onboarding_completed` vs `has_completed_onboarding`. `app/Models/User.php`, `app/Http/Middleware/EnsureOnboardingCompleted.php`, `app/Http/Controllers/DashboardController.php`, `routes/console.php`.
- TODO email rejet promo code. `app/Http/Controllers/Admin/PromoCodeRequestController.php`.
- Encodage casse (mojibake) dans README/templates/legal/UI. `README.md`, `config/coach_site.php`, `config/legal_templates.php`, `resources/js/Pages/Setup/*.vue`, `routes/console.php`.
- Duplication logique (generation share code) dans plusieurs classes.

## Qualite logicielle
- Tests: uniquement tests Breeze/Auth; pas de tests metier (booking, paiements, partage). `tests/`.
- CI/lint: aucun pipeline detecte (pas de `.github`), pas de scripts lint/test front.
- Gestion erreurs: beaucoup de `Log::info`/`Log::error` avec donnees sensibles; a revoir.

## Recommandations (ordre propose)
1. Securiser `/booking/{id}/success`: signed URL, token de session, ou verification Stripe obligatoire avant rendu.
2. Normaliser les champs couleur (choisir `color_primary`/`color_secondary` partout) + migration de donnees si besoin.
3. Corriger la route booking details (creer la page manquante ou renvoyer vers `Coach/BookingsBeta`).
4. Fixer l acces coach au dashboard client (session key unique et partage).
5. Reduire la journalisation de PII, regler `LOG_LEVEL` en prod, ajouter masquage.
6. Ajouter throttling (rate limiting) sur share code/contact/checkout.
7. Clarifier `APP_DOMAIN` sans port + comportement fallback sans sous-domaine.
8. Plan RGPD: chiffrement champs sante, politique retention/suppression, audit trail.
9. Ajouter tests metier critiques (webhooks, booking, partage client, permissions).
10. Mettre en place CI (tests + lint) et audits deps (composer audit, npm audit).

## Notes
- Analyse basee sur lecture du code, sans execution ni base de donnees.
- Verifier la configuration prod (APP_DEBUG, storage, backups, queues).
