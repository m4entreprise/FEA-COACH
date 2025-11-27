<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Politique de Confidentialité - Ignite Coach" />
    
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
        <!-- Navigation -->
        <nav class="fixed w-full bg-white/10 backdrop-blur-md z-50 border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <Link href="/" class="flex items-center space-x-2">
                        <div class="text-2xl font-bold text-white">
                            Ignite <span class="text-purple-400">Coach</span>
                        </div>
                    </Link>
                    
                    <Link
                        href="/"
                        class="px-4 py-2 text-white hover:text-purple-300 transition"
                    >
                        ← Retour
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="pt-24 pb-12 px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                        Politique de Confidentialité
                    </h1>
                    <p class="text-purple-300 text-lg">
                        Ignite Coach
                    </p>
                    <p class="text-gray-400 mt-2">
                        Dernière mise à jour : 27 novembre 2025
                    </p>
                </div>

                <!-- Content Card -->
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl border border-white/20 p-8 md:p-12">
                    <div class="prose prose-invert prose-purple max-w-none">
                        
                        <!-- Section 1 -->
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-purple-300 mb-4">1. IDENTITÉ DU RESPONSABLE DE TRAITEMENT</h2>
                            
                            <div class="text-gray-300 space-y-4">
                                <div>
                                    <p class="font-semibold text-purple-200 mb-2">Pour les données des Utilisateurs (professionnels ou consommateurs) :</p>
                                    <p><strong>M4 Entreprise</strong></p>
                                    <p>TVA / BCE : BE1030.459.209</p>
                                    <p>Siège social : Rue de la Digue 92, 4683 Vivegnis, Belgique</p>
                                    <p>Email : <a href="mailto:support@ignitecoach.app" class="text-purple-400 hover:text-purple-300">support@ignitecoach.app</a></p>
                                </div>

                                <div>
                                    <p class="font-semibold text-purple-200 mb-2">Pour les données de paiement :</p>
                                    <p><strong>Paddle.com Market Ltd</strong> agit comme Merchant of Record et responsable de traitement pour toutes les transactions.</p>
                                </div>

                                <div>
                                    <p class="font-semibold text-purple-200 mb-2">Pour les données des Clients Finaux encodées par les coachs :</p>
                                    <ul class="list-disc list-inside space-y-2 ml-4">
                                        <li>Le Coach est <strong>Responsable de traitement</strong>.</li>
                                        <li>M4 Entreprise est <strong>Sous-traitant</strong>.</li>
                                    </ul>
                                </div>
                            </div>
                        </section>

                        <div class="border-t border-white/10 my-8"></div>

                        <!-- Section 2 -->
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-purple-300 mb-4">2. DONNÉES COLLECTÉES</h2>
                            
                            <h3 class="text-xl font-semibold text-purple-200 mb-3 mt-6">2.1. Données collectées auprès de l'Utilisateur</h3>
                            <div class="text-gray-300">
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Identité : nom, prénom, email, numéro de téléphone (optionnel).</li>
                                    <li>Informations professionnelles : diplôme, certification, statut indépendant (le cas échéant).</li>
                                    <li>Données techniques : adresse IP, logs de connexion, type d'appareil, navigateur.</li>
                                    <li>Données de configuration : contenu du Site Coach, images, textes, profils clients, offres.</li>
                                    <li>Données d'assistance : échanges avec le support.</li>
                                </ul>
                            </div>

                            <h3 class="text-xl font-semibold text-purple-200 mb-3 mt-6">2.2. Données collectées par Paddle</h3>
                            <div class="text-gray-300 space-y-2">
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Données de paiement et facturation.</li>
                                    <li>TVA, adresse de facturation, moyen de paiement.</li>
                                </ul>
                                <p>Ces données sont traitées selon la politique de Paddle.</p>
                            </div>

                            <h3 class="text-xl font-semibold text-purple-200 mb-3 mt-6">2.3. Données collectées concernant les Clients Finaux</h3>
                            <div class="text-gray-300 space-y-2">
                                <p>Données encodées par le Coach dans le CRM Ignite Coach :</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>identité, coordonnées, historique de contact, notes de suivi, informations sportives ou physiques, objectifs.</li>
                                </ul>
                                <p class="mt-3">Ces données ne sont <strong>pas</strong> utilisées par M4 Entreprise à des fins propres.</p>
                            </div>
                        </section>

                        <div class="border-t border-white/10 my-8"></div>

                        <!-- Section 3 -->
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-purple-300 mb-4">3. FINALITÉS DU TRAITEMENT</h2>
                            
                            <h3 class="text-xl font-semibold text-purple-200 mb-3 mt-6">3.1. Pour l'Utilisateur</h3>
                            <div class="text-gray-300">
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Fournir le Service SaaS Ignite Coach.</li>
                                    <li>Héberger et gérer le Site Coach.</li>
                                    <li>Gérer le compte, l'authentification et la sécurité.</li>
                                    <li>Assurer l'assistance technique et le support.</li>
                                    <li>Améliorer le Service (diagnostics, optimisation, mesure des performances).</li>
                                    <li>Respecter les obligations légales (facturation, comptabilité).</li>
                                </ul>
                            </div>

                            <h3 class="text-xl font-semibold text-purple-200 mb-3 mt-6">3.2. Pour les Clients Finaux</h3>
                            <div class="text-gray-300 space-y-2">
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Hébergement des données enregistrées par le Coach.</li>
                                    <li>Disponibilité de l'outil CRM et des fonctionnalités associées.</li>
                                </ul>
                                <p>M4 Entreprise agit uniquement sur instructions du Coach.</p>
                            </div>
                        </section>

                        <div class="border-t border-white/10 my-8"></div>

                        <!-- Section 4 -->
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-purple-300 mb-4">4. BASE LÉGALE DES TRAITEMENTS</h2>
                            <div class="text-gray-300">
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li><strong>Exécution du contrat</strong> : création du compte, utilisation du Service, hébergement du Site Coach.</li>
                                    <li><strong>Obligations légales</strong> : facturation via Paddle, sécurité, conservation minimale.</li>
                                    <li><strong>Intérêt légitime</strong> : amélioration du Service, prévention des fraudes, analyses techniques.</li>
                                    <li><strong>Consentement</strong> (si l'Utilisateur est consommateur ou pour certaines fonctionnalités facultatives).</li>
                                </ul>
                            </div>
                        </section>

                        <div class="border-t border-white/10 my-8"></div>

                        <!-- Section 5 -->
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-purple-300 mb-4">5. DESTINATAIRES ET SOUS-TRAITANTS</h2>
                            <div class="text-gray-300 space-y-3">
                                <p>Les données peuvent être transmises uniquement à :</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li><strong>Paddle</strong> : paiements et factures.</li>
                                    <li><strong>Hébergeur cloud</strong> localisé dans l'UE.</li>
                                    <li>Outils techniques (email transactionnel, CDN, monitoring), tous situés dans l'UE ou en pays reconnus adéquats.</li>
                                </ul>
                                <p class="mt-3">Aucun transfert sans cadre légal valide (décision d'adéquation ou clauses contractuelles types).</p>
                                <p>Aucun partage commercial, aucune revente de données.</p>
                            </div>
                        </section>

                        <div class="border-t border-white/10 my-8"></div>

                        <!-- Section 6 -->
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-purple-300 mb-4">6. DURÉE DE CONSERVATION</h2>
                            <div class="text-gray-300">
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Données du Coach : conservées tant que le compte est actif, puis 90 jours maximum après résiliation (sauf obligation légale).</li>
                                    <li>Données des Clients Finaux : conservées tant que le Coach maintient son compte ou selon ses propres règles.</li>
                                    <li>Logs techniques : 12 mois maximum.</li>
                                    <li>Données de facturation (Paddle) : durée légale comptable.</li>
                                </ul>
                            </div>
                        </section>

                        <div class="border-t border-white/10 my-8"></div>

                        <!-- Section 7 -->
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-purple-300 mb-4">7. DROITS DES UTILISATEURS</h2>
                            
                            <h3 class="text-xl font-semibold text-purple-200 mb-3 mt-6">7.1. Pour les Utilisateurs</h3>
                            <div class="text-gray-300 space-y-3">
                                <p>Droits :</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>accès,</li>
                                    <li>rectification,</li>
                                    <li>effacement,</li>
                                    <li>limitation,</li>
                                    <li>opposition,</li>
                                    <li>portabilité.</li>
                                </ul>
                                <p class="mt-3">Exercice des droits : <a href="mailto:support@ignitecoach.app" class="text-purple-400 hover:text-purple-300">support@ignitecoach.app</a></p>
                                <p>(M4 Entreprise peut demander une preuve d'identité).</p>
                                <p>Pour les données de paiement : s'adresser à Paddle.</p>
                            </div>

                            <h3 class="text-xl font-semibold text-purple-200 mb-3 mt-6">7.2. Pour les Clients Finaux</h3>
                            <div class="text-gray-300 space-y-2">
                                <p>Les demandes doivent être adressées directement au Coach (Responsable de traitement).</p>
                                <p>M4 Entreprise assistera le Coach si nécessaire.</p>
                            </div>
                        </section>

                        <div class="border-t border-white/10 my-8"></div>

                        <!-- Section 8 -->
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-purple-300 mb-4">8. SÉCURITÉ DES DONNÉES</h2>
                            <div class="text-gray-300 space-y-3">
                                <p>Mesures mises en place :</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>chiffrement des données en transit (HTTPS),</li>
                                    <li>firewalls et protections anti-intrusion,</li>
                                    <li>isolation des environnements (multi-tenant),</li>
                                    <li>sauvegardes régulières,</li>
                                    <li>contrôle d'accès strict selon les rôles,</li>
                                    <li>surveillance et audit technique.</li>
                                </ul>
                                <p class="mt-3">L'Utilisateur doit utiliser des mots de passe sécurisés et protéger ses accès.</p>
                            </div>
                        </section>

                        <div class="border-t border-white/10 my-8"></div>

                        <!-- Section 9 -->
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-purple-300 mb-4">9. COOKIES ET TRACEURS</h2>
                            <div class="text-gray-300 space-y-3">
                                <p>Ignite Coach utilise des cookies pour :</p>
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>l'authentification,</li>
                                    <li>la gestion de session,</li>
                                    <li>le fonctionnement du tableau de bord,</li>
                                    <li>la sécurité,</li>
                                    <li>les statistiques anonymisées.</li>
                                </ul>
                                <p class="mt-3">Aucun cookie publicitaire n'est utilisé.</p>
                                <p>Le Coach peut utiliser ses propres cookies sur son Site Coach (ex. analytics). Il en est seul responsable.</p>
                            </div>
                        </section>

                        <div class="border-t border-white/10 my-8"></div>

                        <!-- Section 10 -->
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-purple-300 mb-4">10. TRANSFERTS HORS UE</h2>
                            <div class="text-gray-300 space-y-2">
                                <p>Aucun transfert direct hors UE sans cadre juridique conforme (pays adéquat ou Clauses Contractuelles Types).</p>
                                <p>La liste des sous-traitants mis à jour est disponible sur demande.</p>
                            </div>
                        </section>

                        <div class="border-t border-white/10 my-8"></div>

                        <!-- Section 11 -->
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-purple-300 mb-4">11. DONNÉES DES CLIENTS FINAUX : RÔLES RGPD</h2>
                            <div class="text-gray-300 space-y-2">
                                <ul class="list-disc list-inside space-y-2 ml-4">
                                    <li>Le <strong>Coach</strong> est responsable du traitement de ses clients.</li>
                                    <li>M4 Entreprise agit en tant que <strong>Sous-traitant</strong>.</li>
                                    <li>M4 Entreprise ne traite les données des Clients Finaux que sur instructions du Coach.</li>
                                    <li>Aucun usage commercial ou personnel n'est effectué.</li>
                                </ul>
                                <p class="mt-3">Une <strong>Annexe RGPD (DPA)</strong> est fournie et fait partie intégrante des CGVU.</p>
                            </div>
                        </section>

                        <div class="border-t border-white/10 my-8"></div>

                        <!-- Section 12 -->
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-purple-300 mb-4">12. MODIFICATIONS DE LA POLITIQUE</h2>
                            <div class="text-gray-300 space-y-2">
                                <p>Notification en cas de modification significative.</p>
                                <p>Poursuite de l'utilisation du Service = acceptation de la nouvelle version.</p>
                            </div>
                        </section>

                        <div class="border-t border-white/10 my-8"></div>

                        <!-- Section 13 -->
                        <section class="mb-8">
                            <h2 class="text-2xl font-bold text-purple-300 mb-4">13. CONTACT RGPD</h2>
                            <div class="text-gray-300 space-y-2">
                                <p>Pour toute question :</p>
                                <p><a href="mailto:support@ignitecoach.app" class="text-purple-400 hover:text-purple-300">support@ignitecoach.app</a></p>
                                <p class="mt-4">Pour les plaintes :</p>
                                <p>Autorité de protection des données (Belgique).</p>
                            </div>
                        </section>
                    </div>
                </div>

                <!-- Back to home -->
                <div class="mt-8 text-center">
                    <Link
                        href="/"
                        class="inline-block px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold rounded-lg transition"
                    >
                        Retour à l'accueil
                    </Link>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="py-8 px-4 border-t border-white/10">
            <div class="max-w-7xl mx-auto text-center text-gray-400">
                <p>&copy; 2026 Ignite Coach - Développé par M4 Entreprise en partenariat avec Fitness Education Academy</p>
            </div>
        </footer>
    </div>
</template>
