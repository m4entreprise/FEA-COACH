<?php

namespace App\Services;

use App\DataTransferObjects\LegalData;
use App\Models\Coach;

class LegalContentGenerator
{
    private array $templates;

    public function __construct()
    {
        $this->templates = config('legal_templates');
    }

    public function generate(Coach|LegalData $input): string
    {
        $data = $input instanceof Coach 
            ? LegalData::fromCoach($input) 
            : $input;

        $cgv = $this->generateCGV($data);
        $privacy = $this->generatePrivacyPolicy($data);
        $date = now()->format('d/m/Y');

        return <<<HTML
        <div class="legal-container">
            <section id="cgv" class="mb-12">
                {$cgv}
            </section>
            
            <hr class="legal-separator my-12 border-gray-300" />
            
            <section id="privacy" class="mb-12">
                {$privacy}
            </section>
            
            <footer class="legal-footer mt-8 pt-6 border-t border-gray-200 text-sm text-gray-500">
                <p>Dernière mise à jour : {$date}</p>
            </footer>
        </div>
        HTML;
    }

    private function generateCGV(LegalData $data): string
    {
        $blocks = [];

        // Titre
        $blocks[] = '<h1 class="text-3xl font-bold mb-8 text-gray-900">Conditions Générales de Vente</h1>';

        // En-tête (PP ou SOC)
        $header = $data->type_entite === 'PP' 
            ? $this->renderBlock('cgv.header_pp', $data)
            : $this->renderBlock('cgv.header_soc', $data);
        $blocks[] = "<div class=\"mb-6 text-gray-700 leading-relaxed\">{$header}</div>";

        // Article 1 - Objet (Universel)
        $blocks[] = $this->renderArticle(
            '1',
            'Objet et nature des obligations',
            $this->renderBlock('cgv.article_objet', $data)
        );

        // Article 2 - Santé (Conditionnel)
        if ($data->is_presentiel || $data->is_online) {
            $blocks[] = $this->renderArticle(
                '2',
                'État de santé',
                $this->renderBlock('cgv.article_sante', $data)
            );
        }

        // Article 3/4 - Prix et paiement
        $articleNumber = ($data->is_presentiel || $data->is_online) ? '3' : '2';
        
        $prixContent = $data->regime_tva === 'FRANCHISE'
            ? $this->renderBlock('cgv.article_prix_franchise', $data)
            : $this->renderBlock('cgv.article_prix_assujetti', $data);
        
        $prixContent .= "\n\n" . $this->renderBlock('cgv.article_retard_paiement', $data);
        
        $blocks[] = $this->renderArticle($articleNumber, 'Prix et paiement', $prixContent);

        // Article Abonnements (Conditionnel)
        if ($data->has_subscriptions) {
            $articleNumber = ($data->is_presentiel || $data->is_online) ? '3 bis' : '2 bis';
            $blocks[] = $this->renderArticle(
                $articleNumber,
                'Durée et Résiliation des abonnements',
                $this->renderBlock('cgv.article_abonnements', $data)
            );
        }

        // Article Droit de rétractation
        $articleNumber = ($data->is_presentiel || $data->is_online) ? '4' : '3';
        $retractContent = $this->renderBlock('cgv.article_retractation_intro', $data);
        
        if ($data->has_digital_product) {
            $retractContent .= "\n\n" . $this->renderBlock('cgv.article_retractation_digital', $data);
        }
        
        if ($data->is_presentiel || $data->is_online) {
            $retractContent .= "\n\n" . $this->renderBlock('cgv.article_retractation_service', $data);
        }
        
        $blocks[] = $this->renderArticle($articleNumber, 'Droit de rétractation', $retractContent);

        // Article Annulation
        $articleNumber = ($data->is_presentiel || $data->is_online) ? '5' : '4';
        $blocks[] = $this->renderArticle(
            $articleNumber,
            'Politique d\'annulation',
            $this->renderBlock('cgv.article_annulation', $data)
        );

        // Article Responsabilité
        $articleNumber = ($data->is_presentiel || $data->is_online) ? '6' : '5';
        $respContent = '';
        
        if ($data->is_presentiel) {
            $respContent .= $this->renderBlock('cgv.article_resp_presentiel', $data);
        }
        
        if ($data->is_online || $data->has_digital_product) {
            if ($respContent) $respContent .= "\n\n";
            $respContent .= $this->renderBlock('cgv.article_resp_online', $data);
        }
        
        $respContent .= "\n\n" . $this->renderBlock('cgv.article_resp_limit', $data);
        
        $blocks[] = $this->renderArticle($articleNumber, 'Responsabilité', $respContent);

        // Article Propriété intellectuelle
        $articleNumber = ($data->is_presentiel || $data->is_online) ? '7' : '6';
        $blocks[] = $this->renderArticle(
            $articleNumber,
            'Propriété Intellectuelle',
            $this->renderBlock('cgv.article_propriete', $data)
        );

        // Article Droit à l'image (Conditionnel)
        if ($data->use_client_photos) {
            $articleNumber = ($data->is_presentiel || $data->is_online) ? '8' : '7';
            $blocks[] = $this->renderArticle(
                $articleNumber,
                'Droit à l\'image',
                $this->renderBlock('cgv.article_image', $data)
            );
        }

        // Article Assurance (Conditionnel)
        if ($data->assurance_nom && $data->assurance_police) {
            $currentArticle = 7;
            if ($data->is_presentiel || $data->is_online) $currentArticle = 8;
            if ($data->use_client_photos) $currentArticle++;
            
            $blocks[] = $this->renderArticle(
                (string)$currentArticle,
                'Assurance',
                $this->renderBlock('cgv.article_assurance', $data)
            );
        }

        // Article Final - Litiges
        $blocks[] = $this->renderArticle(
            'Final',
            'Droit applicable',
            $this->renderBlock('cgv.article_litiges', $data)
        );

        return implode("\n\n", $blocks);
    }

    private function generatePrivacyPolicy(LegalData $data): string
    {
        $blocks = [];

        $blocks[] = '<h1 class="text-3xl font-bold mb-8 text-gray-900">Politique de Confidentialité</h1>';

        // Introduction
        $blocks[] = "<div class=\"mb-6\">";
        $blocks[] = "<h2 class=\"text-xl font-semibold mb-3 text-gray-800\">Introduction</h2>";
        $blocks[] = "<p class=\"text-gray-700 leading-relaxed\">" . $this->renderBlock('privacy.header', $data) . "</p>";
        $blocks[] = "</div>";

        // 1. Données collectées
        $blocks[] = "<div class=\"mb-6\">";
        $blocks[] = "<h2 class=\"text-xl font-semibold mb-3 text-gray-800\">1. Données collectées</h2>";
        $blocks[] = "<div class=\"text-gray-700 leading-relaxed whitespace-pre-line\">" . $this->renderBlock('privacy.article_donnees', $data) . "</div>";
        $blocks[] = "</div>";

        // 2. Finalités
        $blocks[] = "<div class=\"mb-6\">";
        $blocks[] = "<h2 class=\"text-xl font-semibold mb-3 text-gray-800\">2. Pourquoi traitons-nous vos données ?</h2>";
        $blocks[] = "<div class=\"text-gray-700 leading-relaxed whitespace-pre-line\">" . $this->renderBlock('privacy.article_finalites', $data) . "</div>";
        $blocks[] = "</div>";

        // 3. Conservation
        $blocks[] = "<div class=\"mb-6\">";
        $blocks[] = "<h2 class=\"text-xl font-semibold mb-3 text-gray-800\">3. Durée de conservation</h2>";
        $blocks[] = "<div class=\"text-gray-700 leading-relaxed whitespace-pre-line\">" . $this->renderBlock('privacy.article_conservation', $data) . "</div>";
        $blocks[] = "</div>";

        // 4. Droits
        $blocks[] = "<div class=\"mb-6\">";
        $blocks[] = "<h2 class=\"text-xl font-semibold mb-3 text-gray-800\">4. Vos droits</h2>";
        $blocks[] = "<p class=\"text-gray-700 leading-relaxed\">" . $this->renderBlock('privacy.article_droits', $data) . "</p>";
        $blocks[] = "</div>";

        // 5. Contact
        $blocks[] = "<div class=\"mb-6\">";
        $blocks[] = "<h2 class=\"text-xl font-semibold mb-3 text-gray-800\">5. Contact</h2>";
        $blocks[] = "<p class=\"text-gray-700 leading-relaxed\">" . $this->renderBlock('privacy.article_contact', $data) . "</p>";
        $blocks[] = "</div>";

        return implode("\n", $blocks);
    }

    private function renderArticle(string $number, string $title, string $content): string
    {
        return <<<HTML
        <article class="mb-8">
            <h2 class="text-xl font-semibold mb-3 text-gray-800">Article {$number} - {$title}</h2>
            <div class="text-gray-700 leading-relaxed whitespace-pre-line">{$content}</div>
        </article>
        HTML;
    }

    private function renderBlock(string $path, LegalData $data): string
    {
        $template = data_get($this->templates, $path, '');
        return $this->interpolateVariables($template, $data->toArray());
    }

    private function interpolateVariables(string $template, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value ?? '', $template);
        }
        return $template;
    }
}
