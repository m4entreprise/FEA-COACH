<?php

namespace App\DataTransferObjects;

use App\Models\Coach;

class LegalData
{
    public function __construct(
        // Coach Entity
        public readonly string $type_entite,
        public readonly string $nom_commercial,
        public readonly string $nom_legal,
        public readonly string $siege_social,
        public readonly string $num_bce,
        public readonly ?string $num_tva,
        public readonly string $regime_tva,
        public readonly ?string $representant_legal,
        public readonly string $email_contact,
        public readonly ?string $tel_contact,
        
        // Service Flags
        public readonly bool $is_presentiel,
        public readonly bool $is_online,
        public readonly bool $has_digital_product,
        public readonly bool $has_subscription,
        public readonly bool $use_client_photos,
        
        // Business Rules
        public readonly int $delai_annulation,
        public readonly string $ville_tribunal,
        public readonly ?string $assurance_nom,
        public readonly ?string $assurance_police,
    ) {}

    public static function fromCoach(Coach $coach): self
    {
        $user = $coach->user;

        return new self(
            type_entite: $user->entity_type ?? 'PP',
            nom_commercial: $coach->name,
            nom_legal: $user->legal_name ?? $user->name ?? '',
            siege_social: $user->legal_address ?? '',
            num_bce: $user->company_number ?? '',
            num_tva: $user->vat_number,
            regime_tva: $coach->vat_regime ?? 'ASSUJETTI',
            representant_legal: $user->legal_representative,
            email_contact: $user->email ?? '',
            tel_contact: $user->phone_contact,
            is_presentiel: $coach->is_coaching_presentiel ?? false,
            is_online: $coach->is_coaching_online ?? false,
            has_digital_product: $coach->has_digital_products ?? false,
            has_subscription: $coach->has_subscriptions ?? false,
            use_client_photos: $coach->use_client_photos ?? false,
            delai_annulation: $coach->cancellation_delay ?? 24,
            ville_tribunal: $coach->tribunal_city ?? 'Bruxelles',
            assurance_nom: $coach->insurance_company,
            assurance_police: $coach->insurance_policy_number,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            type_entite: $data['entity_type'] ?? 'PP',
            nom_commercial: $data['nom_commercial'] ?? '',
            nom_legal: $data['legal_name'] ?? '',
            siege_social: $data['legal_address'] ?? '',
            num_bce: $data['company_number'] ?? '',
            num_tva: $data['vat_number'] ?? null,
            regime_tva: $data['vat_regime'] ?? 'ASSUJETTI',
            representant_legal: $data['legal_representative'] ?? null,
            email_contact: $data['email'] ?? '',
            tel_contact: $data['phone_contact'] ?? null,
            is_presentiel: $data['is_coaching_presentiel'] ?? false,
            is_online: $data['is_coaching_online'] ?? false,
            has_digital_product: $data['has_digital_products'] ?? false,
            has_subscription: $data['has_subscriptions'] ?? false,
            use_client_photos: $data['use_client_photos'] ?? false,
            delai_annulation: $data['cancellation_delay'] ?? 24,
            ville_tribunal: $data['tribunal_city'] ?? 'Bruxelles',
            assurance_nom: $data['insurance_company'] ?? null,
            assurance_police: $data['insurance_policy_number'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'type_entite' => $this->type_entite,
            'nom_commercial' => $this->nom_commercial,
            'nom_legal' => $this->nom_legal,
            'siege_social' => $this->siege_social,
            'num_bce' => $this->num_bce,
            'num_tva' => $this->num_tva,
            'regime_tva' => $this->regime_tva,
            'representant_legal' => $this->representant_legal,
            'email_contact' => $this->email_contact,
            'tel_contact' => $this->tel_contact,
            'is_presentiel' => $this->is_presentiel,
            'is_online' => $this->is_online,
            'has_digital_product' => $this->has_digital_product,
            'has_subscription' => $this->has_subscription,
            'use_client_photos' => $this->use_client_photos,
            'delai_annulation' => $this->delai_annulation,
            'ville_tribunal' => $this->ville_tribunal,
            'assurance_nom' => $this->assurance_nom,
            'assurance_police' => $this->assurance_police,
        ];
    }
}
