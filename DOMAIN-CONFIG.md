# Configuration du domaine pour les sites coachs 🌐

## 🎯 Problème résolu

Le lien "Voir le site" dans le dashboard générait une URL avec `.localhost:8000` même en production.

## ✅ Solution

Le système utilise maintenant la variable d'environnement `APP_DOMAIN` pour générer les URLs des sites coachs.

## ⚙️ Configuration

### Développement local

Dans votre fichier `.env` :

```env
APP_DOMAIN=localhost:8000
```

**URL générée** : `http://pierre-martin.localhost:8000`

### Production

Dans votre fichier `.env` de production :

```env
APP_DOMAIN=kineseducation.academy
```

**URL générée** : `http://pierre-martin.kineseducation.academy`

### Autre environnement (staging, test, etc.)

```env
APP_DOMAIN=staging.kineseducation.academy
```

**URL générée** : `http://pierre-martin.staging.kineseducation.academy`

## 🔧 Modifications apportées

### 1. HandleInertiaRequests.php

Partage la configuration du domaine avec toutes les pages Inertia :

```php
public function share(Request $request): array
{
    return [
        // ...
        'appDomain' => config('app.domain', 'localhost:8000'),
    ];
}
```

### 2. Dashboard.vue

Utilise la configuration partagée au lieu d'une URL codée en dur :

**Avant** :
```vue
<a :href="`http://${coach.subdomain}.localhost:8000`">
    Voir le site →
</a>
```

**Après** :
```vue
<a :href="`http://${coach.subdomain}.${$page.props.appDomain}`">
    Voir le site →
</a>
```

## 📝 Où est utilisé APP_DOMAIN

Le domaine est utilisé pour :

1. **Lien "Voir le site"** dans le dashboard (statistiques)
2. Génération des URLs des sites publics des coachs
3. Potentiellement d'autres liens internes

## 🚀 Déploiement

### Étapes pour la production

1. **Configurer le DNS** :
   - Créer un enregistrement wildcard `*.kineseducation.academy`
   - Pointer vers votre serveur

2. **Configurer Apache/Nginx** :
   - Activer les wildcard subdomains
   - Exemple Nginx :
   ```nginx
   server_name *.kineseducation.academy;
   ```

3. **Configurer le .env de production** :
   ```env
   APP_DOMAIN=kineseducation.academy
   APP_URL=https://kineseducation.academy
   ```

4. **Vider les caches** :
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

## 🔒 Configuration SSL (HTTPS)

Pour utiliser HTTPS en production :

1. **Obtenir un certificat wildcard** :
   ```bash
   # Exemple avec Let's Encrypt/Certbot
   certbot certonly --dns-cloudflare -d "*.kineseducation.academy" -d "kineseducation.academy"
   ```

2. **Modifier le lien dans Dashboard.vue** si besoin :
   ```vue
   <a :href="`https://${coach.subdomain}.${$page.props.appDomain}`">
   ```
   
   Ou mieux, utiliser une configuration :
   ```php
   'appDomain' => config('app.domain', 'localhost:8000'),
   'appProtocol' => config('app.env') === 'local' ? 'http' : 'https',
   ```

## 📊 Exemples d'URLs générées

### Développement
- Pierre Martin : `http://pierre-martin.localhost:8000`
- Sophie Dubois : `http://sophie-dubois.localhost:8000`

### Production
- Pierre Martin : `http://pierre-martin.kineseducation.academy`
- Sophie Dubois : `http://sophie-dubois.kineseducation.academy`

### Production avec HTTPS
- Pierre Martin : `https://pierre-martin.kineseducation.academy`
- Sophie Dubois : `https://sophie-dubois.kineseducation.academy`

## ✅ Checklist de déploiement

- [ ] DNS wildcard configuré
- [ ] Serveur web configuré pour wildcard subdomains
- [ ] Variable `APP_DOMAIN` définie dans `.env` de production
- [ ] Certificat SSL obtenu (si HTTPS)
- [ ] Caches vidés après déploiement
- [ ] Test d'un site coach en production
- [ ] Vérification du lien "Voir le site" depuis le dashboard

## 🔍 Vérification

Pour vérifier que la configuration fonctionne :

1. **Se connecter en tant que coach**
2. **Aller sur le dashboard**
3. **Regarder la carte "Statut site"**
4. **Vérifier l'URL du lien "Voir le site →"**

L'URL doit correspondre au domaine configuré dans `APP_DOMAIN`.

## 🐛 Dépannage

### Le lien pointe toujours vers localhost

1. Vérifier le fichier `.env` :
   ```bash
   grep APP_DOMAIN .env
   ```

2. Vider les caches :
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. Redémarrer le serveur de développement

### Le lien ne fonctionne pas en production

1. Vérifier le DNS :
   ```bash
   nslookup pierre-martin.kineseducation.academy
   ```

2. Vérifier la configuration du serveur web

3. Vérifier les logs d'erreur

---

**Date** : 12 novembre 2025  
**Statut** : ✅ Configuré et fonctionnel
