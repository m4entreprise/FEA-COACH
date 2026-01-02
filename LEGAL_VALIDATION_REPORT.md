# ⚖️ Rapport de Validation Juridique - Corrections Appliquées

**Date** : 2 janvier 2026  
**Statut** : ✅ Validé par juriste - Corrections appliquées  
**Fichier modifié** : `config/legal_templates.php`

---

## 📋 Résumé des corrections

Suite à l'analyse juridique approfondie, **5 corrections critiques** ont été apportées aux templates de CGV pour garantir la conformité au droit belge.

---

## 🔴 1. Clause de compétence territoriale (ILLÉGALE → CORRIGÉE)

### ❌ Problème identifié

**Texte initial :**
```
"les tribunaux de l'arrondissement judiciaire de {{ville_tribunal}} seront seuls compétents."
```

**Violation légale :**
- Clause abusive en B2C (Code Judiciaire + Code de Droit Économique)
- Impossibilité d'imposer au consommateur de plaider dans une autre juridiction
- Risque : Incompétence du tribunal, perte de temps et frais

### ✅ Correction appliquée

**Nouveau texte :**
```php
'article_litiges' => "Les présentes CGV sont soumises au droit belge. En cas de litige, 
et à défaut de résolution amiable via le Service de Médiation pour le Consommateur 
(https://mediationconsommateur.be), la compétence territoriale sera déterminée comme suit :

- Pour les litiges avec un Consommateur : les tribunaux compétents sont ceux désignés 
  par le Code Judiciaire (en principe, le tribunal du domicile du Consommateur).
- Pour les litiges entre Professionnels : les tribunaux de l'arrondissement judiciaire 
  de {{ville_tribunal}} sont seuls compétents.

Plateforme européenne de règlement en ligne des litiges (RLL) : 
https://ec.europa.eu/consumers/odr"
```

**Impact :**
- ✅ Distinction claire B2C / B2B
- ✅ Respect du Code Judiciaire
- ✅ Clause de compétence valable pour les professionnels

---

## 🔴 2. Recouvrement de dettes (IMPRÉCIS → CORRIGÉ)

### ❌ Problème identifié

**Texte initial :**
```
"après l'envoi d'un premier rappel gratuit... des intérêts seront dus"
```

**Violation légale :**
- Absence du délai de carence légal de 14 jours (Livre XIX CDE)
- Laisse croire que les pénalités s'appliquent immédiatement
- Risque : Nullité de la clause indemnitaire

### ✅ Correction appliquée

**Nouveau texte :**
```php
'article_retard_paiement' => "Retard de paiement : En cas de défaut de paiement à 
l'échéance, un premier rappel gratuit sera adressé au Client consommateur. Si le 
paiement n'est pas effectué dans un délai de 14 jours calendrier suivant l'envoi 
de ce rappel, des intérêts de retard au taux directeur majoré de 8 points de 
pourcentage ainsi qu'une indemnité forfaitaire seront dus de plein droit, 
conformément aux plafonds fixés par le Livre XIX du Code de droit économique."
```

**Impact :**
- ✅ Délai légal de 14 jours explicite
- ✅ Conformité Livre XIX CDE
- ✅ Protection contre l'annulation de la clause

---

## 🟡 3. Force majeure (RESTRICTIF → ÉLARGI)

### ⚠️ Problème identifié

**Texte initial :**
```
"sauf cas de force majeure prouvé (certificat médical)"
```

**Risque juridique :**
- Limitation excessive aux seuls cas médicaux du client
- Clause abusive (déséquilibre des droits)
- Exemples exclus : décès d'un proche, grève, panne

### ✅ Correction appliquée

**Nouveau texte :**
```php
'article_annulation' => "Toute séance annulée par le Client moins de 
{{delai_annulation}} heures avant l'horaire prévu est due dans son intégralité, 
sauf cas de force majeure dûment justifié par tout moyen probant (certificat 
médical, justificatif de décès d'un proche, attestation de panne, etc.).
```

**Impact :**
- ✅ Ouverture à tous types de force majeure
- ✅ Exemples non exhaustifs ("etc.")
- ✅ Charge de la preuve raisonnable

---

## 🟡 4. Droit de rétractation numérique (TECHNIQUE → PRÉCISÉ)

### ⚠️ Problème identifié

**Texte initial :**
```
"le Client marque son accord exprès... et reconnait perdre ainsi son droit de rétractation"
```

**Insuffisance légale :**
- La mention dans les CGV ne suffit pas (Art. VI.53 CDE)
- Il faut un consentement actif au moment de l'achat (case à cocher)
- Sans cela : le client peut se faire rembourser après téléchargement

### ✅ Correction appliquée

**Nouveau texte :**
```php
'article_retractation_digital' => "Exception pour les Contenus Numériques : Pour 
l'achat de produits numériques non fournis sur un support matériel (Ebooks, PDF, 
Programmes vidéo pré-enregistrés), le droit de rétractation est perdu si le Client 
marque son accord exprès, au moment de la commande, pour que la fourniture du 
contenu commence immédiatement. Cet accord doit être recueilli via une case à 
cocher spécifique ou un bouton de validation explicite lors du processus d'achat 
(Article VI.53 du Code de droit économique)."
```

**Impact :**
- ✅ Exigence UX explicite (case à cocher)
- ✅ Référence à l'article VI.53 CDE
- ⚠️ **Action requise** : Implémenter la case à cocher dans le tunnel d'achat

---

## 🟡 5. Responsabilité corporelle (LIMITE → NUANCÉ)

### ⚠️ Problème identifié

**Texte initial :**
```
"Le Prestataire décline toute responsabilité en cas de dommages corporels..."
```

**Limitation juridique :**
- Impossible de s'exonérer totalement
- Obligation de surveillance du coach (pro)
- Si faute lourde (inattention), clause inefficace

### ✅ Correction appliquée

**Nouveau texte :**
```php
'article_resp_presentiel' => "Sécurité en présentiel : Le Prestataire s'engage à 
assurer une surveillance et un encadrement adéquat des séances. Le Client s'engage 
à respecter strictement les consignes de sécurité et à signaler immédiatement 
toute douleur ou gêne. Le Prestataire ne pourra être tenu responsable des dommages 
corporels résultant d'une mauvaise exécution des mouvements par le Client malgré 
des consignes claires et une surveillance appropriée, ou du non-respect délibéré 
des consignes de sécurité. Cette limitation de responsabilité ne s'applique pas 
en cas de faute lourde du Prestataire. Le Prestataire n'est pas responsable des 
vols ou pertes d'effets personnels du Client durant les séances."
```

**Impact :**
- ✅ Reconnaissance de l'obligation de surveillance
- ✅ Exception explicite pour faute lourde
- ✅ Équilibre entre protection coach et client

---

## 📊 Synthèse de conformité

| Critère juridique | Avant | Après |
|-------------------|-------|-------|
| **Clause de compétence B2C** | ❌ Illégale | ✅ Conforme |
| **Délai recouvrement (14j)** | ❌ Manquant | ✅ Conforme |
| **Force majeure** | ⚠️ Restrictif | ✅ Élargi |
| **Rétractation numérique** | ⚠️ Incomplet | ✅ Précisé* |
| **Responsabilité faute lourde** | ⚠️ Absente | ✅ Mentionnée |

**Légende :**
- ✅ Pleinement conforme
- ⚠️ Nécessite attention (voir actions requises)
- ❌ Non conforme

---

## ⚙️ Actions requises côté développement

### 1. ✅ Backend - Templates modifiés

**Fichier** : `config/legal_templates.php`  
**Statut** : ✅ Corrections appliquées  
**Effet** : Tous les coachs qui régénèrent leurs mentions légales auront le texte corrigé

### 2. ⚠️ Frontend - UX produits numériques

**Fichier à modifier** : Page de checkout des produits numériques  
**Action requise** :

Ajouter une case à cocher obligatoire :

```html
<label>
  <input type="checkbox" name="accept_immediate_delivery" required>
  J'accepte que le téléchargement commence immédiatement et je renonce 
  expressément à mon droit de rétractation de 14 jours pour ce contenu numérique 
  (Article VI.53 du Code de droit économique).
</label>
```

**Priorité** : MOYENNE (uniquement si vente de produits numériques)  
**Validation** : Conserver la trace du consentement en base de données

### 3. ✅ Documentation utilisateur

**Fichier** : `LEGAL_GENERATOR_USER_GUIDE.md`  
**Statut** : ✅ Déjà à jour avec les bonnes pratiques

---

## 🎓 Points de vigilance opérationnels

### Pour les coachs

1. **Recouvrement** : Toujours attendre 14 jours après le rappel avant de réclamer des intérêts
2. **Force majeure** : Accepter les justificatifs variés (pas seulement médicaux)
3. **Surveillance** : Documenter les consignes données (cahier de séance)
4. **Litiges** : Ne jamais convoquer un consommateur devant un tribunal éloigné

### Pour la plateforme

1. **Régénération** : Inciter les coachs à régénérer leurs mentions légales
2. **Checkout numérique** : Implémenter la case à cocher si fonctionnalité activée
3. **Migration** : Prévoir un email informatif aux coachs existants

---

## 📅 Calendrier de déploiement recommandé

### Phase 1 : Immédiat (2 janvier 2026)
- ✅ Templates corrigés en production
- ✅ Documentation juridique archivée

### Phase 2 : Semaine 1 (3-9 janvier 2026)
- [ ] Communication aux coachs existants
- [ ] Message dans le dashboard : "Nouvelles CGV conformes - Régénérez vos mentions légales"

### Phase 3 : Semaine 2 (si produits numériques actifs)
- [ ] Implémentation de la case à cocher checkout
- [ ] Tests de validation UX
- [ ] Mise à jour des CGV générées automatiquement

---

## 🔒 Archivage juridique

**Version précédente** : Sauvegardée dans Git (commit avant corrections)  
**Version actuelle** : Validée par juriste le 2 janvier 2026  
**Prochaine révision** : Recommandée tous les 12 mois ou en cas de changement législatif

---

## 📞 Contacts

**Validation juridique** : Juriste externe (2 janvier 2026)  
**Responsable technique** : Équipe développement FEA-COACH  
**Questions juridiques** : Consulter un avocat spécialisé en droit des affaires

---

## ✅ Checklist de conformité finale

- [x] Clause de compétence B2C/B2B distincte
- [x] Délai de 14 jours pour recouvrement
- [x] Force majeure élargie et justifiable
- [x] Droit de rétractation numérique précisé (UX à implémenter)
- [x] Responsabilité avec exception faute lourde
- [x] Templates mis à jour dans `config/legal_templates.php`
- [x] Documentation technique créée
- [ ] Communication aux coachs (à planifier)
- [ ] UX checkout numérique (si applicable)

---

**Conclusion** : Les CGV générées sont maintenant **juridiquement conformes** au droit belge selon l'analyse du juriste. Le système peut être déployé en production avec les templates corrigés.

**⚠️ Point d'attention** : Si la plateforme propose la vente de produits numériques, l'implémentation de la case à cocher spécifique au checkout est **obligatoire** pour que la renonciation au droit de rétractation soit valable.
