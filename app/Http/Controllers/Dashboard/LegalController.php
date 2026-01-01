<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LegalController extends Controller
{
    /**
     * Show the legal terms edit form.
     */
    public function edit(Request $request)
    {
        $coach = auth()->user()->coach;

        if (!$coach) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous devez avoir un profil coach pour accéder à cette page.');
        }

        // Texte par défaut des CGV
        $defaultLegalTerms = $this->getDefaultLegalTerms($coach);

        return Inertia::render('Coach/LegalBeta', [
            'coach' => $coach,
            'user' => auth()->user(),
            'defaultLegalTerms' => $defaultLegalTerms,
        ]);
    }

    /**
     * Update the legal terms.
     */
    public function update(Request $request)
    {
        $coach = auth()->user()->coach;

        if (!$coach) {
            return back()->with('error', 'Profil coach non trouvé.');
        }

        $validated = $request->validate([
            'vat_number' => 'nullable|string|max:255',
            'legal_terms' => 'nullable|string|max:50000',
        ]);

        // Mise à jour du numéro de TVA dans le modèle User
        if (isset($validated['vat_number'])) {
            auth()->user()->update([
                'vat_number' => $validated['vat_number'],
            ]);
        }

        // Mise à jour des mentions légales dans le modèle Coach
        $coach->update([
            'legal_terms' => $validated['legal_terms'] ?? null,
        ]);

        return back()->with('success', 'Mentions légales enregistrées avec succès !');
    }

    /**
     * Get default legal terms template.
     */
    private function getDefaultLegalTerms($coach)
    {
        $coachName = $coach->name ?? '[Nom du coach]';
        $coachEmail = auth()->user()->email ?? '[email@exemple.com]';
        $coachAddress = auth()->user()->legal_address ?? '[Adresse légale]';
        $vatNumber = auth()->user()->vat_number ?? '[Numéro de TVA si applicable]';

        return "Conditions Générales de Vente – Coaching Sportif

1. Objet
Les présentes conditions encadrent la vente de prestations de coaching sportif proposées par {$coachName}, incluant séances individuelles, cours collectifs, programmes personnalisés et accompagnement à distance.

2. Nature des prestations
Les prestations comprennent un encadrement sportif adapté au niveau du client. Les résultats ne peuvent être garantis, ceux-ci dépendant de l'implication, de la régularité et de l'état de santé du client. Toute demande spécifique doit être validée par écrit via {$coachEmail}.

3. Obligations du coach
Le coach {$coachName} s'engage à fournir un service professionnel, sécurisant et progressif. Il peut adapter ou interrompre une séance en cas de risque pour la santé du client. Il préserve la confidentialité de toutes les informations communiquées.

4. Obligations du client
Le client atteste être apte à la pratique sportive et fournit un certificat médical si nécessaire. Il informe immédiatement le coach de tout problème de santé, douleur ou contre-indication.
Le client respecte les consignes de sécurité, arrive à l'heure et utilise un matériel personnel en bon état pour les séances à distance.
Lieu des séances : [à préciser : studio / domicile / salle partenaire / en ligne].

5. Tarifs et paiement
Les prix sont fixés par {$coachName} et affichés en euros.
N° TVA : {$vatNumber}
Paiement exigé avant la première séance ou à la commande selon échéancier.
Modes acceptés : [virement / carte / espèces / site web].
Tout retard ou impayé entraîne la suspension immédiate des prestations.

6. Réservation et annulation
Annulation client < 24h : séance due.
Annulation > 24h : report possible.
Retard > 15 min : séance écourtée ou annulée, non remboursée.
En cas d'annulation par le coach, une nouvelle date ou un remboursement de la séance est proposé.
Réservation via : [site web / application / message].

7. Validité des packs
Validité des packs : [durée en semaines / mois] à compter de l'achat.
Une marge de prolongation peut être accordée en cas de circonstances exceptionnelles.

8. Droit de rétractation (vente à distance)
Délai légal : 14 jours à compter de la commande.
Si une prestation a déjà commencé, les séances réalisées sont dues.
Aucune rétractation n'est possible pour les séances déjà intégralement effectuées.
Contact pour exercer ce droit : {$coachEmail}

9. Report ou suspension
Suspension possible en cas de raison médicale justifiée, pour une durée maximale de [X semaines / mois].

10. Santé, risques et responsabilité
Le coaching sportif ne remplace pas un avis médical.
Le coach n'est pas responsable des blessures causées par :
– un non-respect des consignes ;
– un matériel défectueux appartenant au client ;
– une mauvaise condition physique non signalée.
Le client déclare pratiquer en pleine conscience de ses capacités.
Assurance RC professionnelle du coach : [nom de l'assureur / numéro de police].

11. Lieux et matériel
Le coach peut modifier le lieu de la séance si nécessaire.
Pour les séances à distance, le client garantit un espace sécurisé.

12. Programmes personnalisés
Accès aux programmes, vidéos et supports valable [durée].
Partage, diffusion ou reproduction interdits sans accord écrit du coach.

13. Données personnelles
Données collectées : informations de contact, état de santé relatif à la pratique sportive.
Usage limité à la gestion des prestations.
Contact RGPD : {$coachEmail}
Droits : accès, rectification, suppression.

14. Propriété intellectuelle
Tous les contenus créés par {$coachName} restent sa propriété exclusive.

15. Force majeure
En cas d'événement imprévisible rendant la prestation impossible, les obligations peuvent être suspendues ou annulées.

16. Résiliation
Résiliation possible en cas de non-respect des obligations.
Les prestations entamées ne sont pas remboursées, sauf faute du coach.

17. Litiges
Tentative de résolution amiable en priorité via {$coachEmail}.
Pour les clients consommateurs, compétence des tribunaux du domicile du client.

---

Informations légales :
Nom commercial : {$coachName}
Adresse : {$coachAddress}
Email : {$coachEmail}
N° TVA : {$vatNumber}";
    }
}
