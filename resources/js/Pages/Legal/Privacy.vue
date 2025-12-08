<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Politique de Confidentialité - UNICOACH" />
    
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
        <!-- Navigation -->
        <nav class="fixed w-full bg-white/10 backdrop-blur-md z-50 border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <Link href="/" class="flex items-center space-x-2">
                        <div class="text-2xl font-bold text-white">
                            UNI<span class="text-purple-400">COACH</span>
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
                        UNICOACH
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
                                    <p>Email : <a href="mailto:support@unicoach.app" class="text-purple-400 hover:text-purple-300">support@unicoach.app</a></p>
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
                                <p>Données encodées par le Coach dans le CRM UNICOACH :</p>
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
                                    <li>Fournir le Service SaaS UNICOACH.</li>
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
                                <p class="mt-3">Exercice des droits : <a href="mailto:support@unicoach.app" class="text-purple-400 hover:text-purple-300">support@unicoach.app</a></p>
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
                                <p>UNICOACH utilise des cookies pour :</p>
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
                                <p><a href="mailto:support@unicoach.app" class="text-purple-400 hover:text-purple-300">support@unicoach.app</a></p>
                                <p class="mt-4">Pour les plaintes :</p>
                                <p>Autorité de protection des données (Belgique).</p>
                            </div>
                        </section>

                        <div class="border-t-4 border-purple-500/50 my-12"></div>

                        <!-- ANNEXE RGPD -->
                        <div class="bg-purple-900/20 border-2 border-purple-500/30 rounded-xl p-6 md:p-8 mb-8">
                            <div class="text-center mb-8">
                                <h1 class="text-3xl md:text-4xl font-bold text-purple-200 mb-2">ANNEXE</h1>
                                <h2 class="text-xl md:text-2xl font-semibold text-white mb-2">Accord de Sous-Traitance RGPD (DPA)</h2>
                                <p class="text-purple-300">UNICOACH – M4 Entreprise</p>
                                <p class="text-gray-400 mt-2">Dernière mise à jour : 27 novembre 2025</p>
                            </div>

                            <!-- Section DPA 1 -->
                            <section class="mb-8">
                                <h3 class="text-xl font-bold text-purple-300 mb-4">1. OBJET DE L'ANNEXE</h3>
                                <div class="text-gray-300 space-y-2">
                                    <p>La présente Annexe (« DPA ») définit les conditions dans lesquelles M4 Entreprise traite, en tant que Sous-traitant, les données personnelles fournies par l'Utilisateur (« le Coach »), agissant en tant que Responsable du traitement, dans le cadre de l'utilisation du Service UNICOACH.</p>
                                    <p>Elle complète les CGVU et en fait partie intégrante.</p>
                                </div>
                            </section>

                            <div class="border-t border-purple-400/30 my-6"></div>

                            <!-- Section DPA 2 -->
                            <section class="mb-8">
                                <h3 class="text-xl font-bold text-purple-300 mb-4">2. RÔLES DES PARTIES</h3>
                                <div class="text-gray-300 space-y-2">
                                    <ul class="list-disc list-inside space-y-2 ml-4">
                                        <li><strong>Responsable de traitement :</strong> le Coach (Utilisateur).</li>
                                        <li><strong>Sous-traitant :</strong> M4 Entreprise.</li>
                                        <li><strong>Sous-traitants ultérieurs :</strong> prestataires techniques utilisés par M4 Entreprise (hébergeur, emails transactionnels, monitoring, etc.).</li>
                                    </ul>
                                    <p class="mt-3">Le Coach est seul responsable de la licéité des données collectées auprès de ses Clients Finaux.</p>
                                </div>
                            </section>

                            <div class="border-t border-purple-400/30 my-6"></div>

                            <!-- Section DPA 3 -->
                            <section class="mb-8">
                                <h3 class="text-xl font-bold text-purple-300 mb-4">3. DESCRIPTION DES TRAITEMENTS</h3>
                                
                                <h4 class="text-lg font-semibold text-purple-200 mb-3 mt-6">3.1. Nature des opérations</h4>
                                <div class="text-gray-300">
                                    <p>M4 Entreprise effectue les opérations suivantes :</p>
                                    <ul class="list-disc list-inside space-y-2 ml-4 mt-2">
                                        <li>hébergement,</li>
                                        <li>stockage,</li>
                                        <li>sécurisation,</li>
                                        <li>sauvegarde,</li>
                                        <li>transmission technique,</li>
                                        <li>affichage dans le CRM et le Site Coach,</li>
                                        <li>support technique,</li>
                                        <li>maintenance et mises à jour.</li>
                                    </ul>
                                </div>

                                <h4 class="text-lg font-semibold text-purple-200 mb-3 mt-6">3.2. Types de données traitées</h4>
                                <div class="text-gray-300 space-y-2">
                                    <p>Selon l'usage du Coach :</p>
                                    <ul class="list-disc list-inside space-y-2 ml-4">
                                        <li>identité : nom, prénom, email, numéro de téléphone,</li>
                                        <li>informations sportives (suivi, objectifs, notes),</li>
                                        <li>informations physiques (mensurations, habitudes sportives),</li>
                                        <li>données contractuelles (séances, suivi relationnel),</li>
                                        <li>données techniques (logs, IP).</li>
                                    </ul>
                                    <p class="mt-3">Aucune donnée médicale au sens strict ne doit être collectée via UNICOACH (diagnostic, pathologie).</p>
                                </div>

                                <h4 class="text-lg font-semibold text-purple-200 mb-3 mt-6">3.3. Personnes concernées</h4>
                                <div class="text-gray-300">
                                    <p>Les Clients Finaux du Coach.</p>
                                </div>

                                <h4 class="text-lg font-semibold text-purple-200 mb-3 mt-6">3.4. Durée du traitement</h4>
                                <div class="text-gray-300">
                                    <p>Durée d'utilisation du Service par le Coach + 90 jours après résiliation (export possible).</p>
                                </div>
                            </section>

                            <div class="border-t border-purple-400/30 my-6"></div>

                            <!-- Section DPA 4 -->
                            <section class="mb-8">
                                <h3 class="text-xl font-bold text-purple-300 mb-4">4. OBLIGATIONS DU RESPONSABLE DE TRAITEMENT (LE COACH)</h3>
                                <div class="text-gray-300 space-y-3">
                                    <p>Le Coach s'engage à :</p>
                                    <ul class="list-disc list-inside space-y-2 ml-4">
                                        <li>collecter et traiter les données de ses clients conformément au RGPD,</li>
                                        <li>informer ses clients de l'utilisation de leurs données,</li>
                                        <li>recueillir le consentement lorsqu'il est requis,</li>
                                        <li>définir la durée de conservation adéquate,</li>
                                        <li>répondre aux demandes d'exercice de droits,</li>
                                        <li>ne pas collecter de données sensibles non nécessaires.</li>
                                    </ul>
                                    <p class="mt-3">Le Coach est seul responsable du respect de ses obligations légales.</p>
                                </div>
                            </section>

                            <div class="border-t border-purple-400/30 my-6"></div>

                            <!-- Section DPA 5 -->
                            <section class="mb-8">
                                <h3 class="text-xl font-bold text-purple-300 mb-4">5. OBLIGATIONS DU SOUS-TRAITANT (M4 ENTREPRISE)</h3>
                                <div class="text-gray-300 space-y-3">
                                    <p>M4 Entreprise s'engage à :</p>
                                    <ul class="list-disc list-inside space-y-2 ml-4">
                                        <li>ne traiter les données que sur instruction documentée du Coach,</li>
                                        <li>garantir la confidentialité du personnel autorisé à traiter les données,</li>
                                        <li>mettre en œuvre des mesures de sécurité adaptées,</li>
                                        <li>assister le Coach dans le respect de ses obligations RGPD,</li>
                                        <li>notifier toute violation de données,</li>
                                        <li>supprimer ou restituer les données à la fin du contrat.</li>
                                    </ul>
                                    <p class="mt-3">Aucune donnée client n'est exploitée à des fins propres.</p>
                                </div>
                            </section>

                            <div class="border-t border-purple-400/30 my-6"></div>

                            <!-- Section DPA 6 -->
                            <section class="mb-8">
                                <h3 class="text-xl font-bold text-purple-300 mb-4">6. CONFIDENTIALITÉ</h3>
                                <div class="text-gray-300 space-y-2">
                                    <p>Le personnel de M4 Entreprise est soumis à une obligation stricte de confidentialité.</p>
                                    <p>Aucune divulgation à un tiers sans autorisation préalable du Coach, sauf obligation légale.</p>
                                </div>
                            </section>

                            <div class="border-t border-purple-400/30 my-6"></div>

                            <!-- Section DPA 7 -->
                            <section class="mb-8">
                                <h3 class="text-xl font-bold text-purple-300 mb-4">7. MESURES DE SÉCURITÉ</h3>
                                <div class="text-gray-300 space-y-3">
                                    <p>M4 Entreprise met en œuvre des mesures techniques et organisationnelles adaptées, incluant :</p>
                                    <ul class="list-disc list-inside space-y-2 ml-4">
                                        <li>chiffrement des données en transit (HTTPS),</li>
                                        <li>infrastructure isolée multi-tenant,</li>
                                        <li>firewalls et systèmes anti-intrusion,</li>
                                        <li>sauvegardes régulières,</li>
                                        <li>contrôle d'accès selon les rôles,</li>
                                        <li>surveillance et journalisation,</li>
                                        <li>mises à jour de sécurité régulières.</li>
                                    </ul>
                                    <p class="mt-3">Le Coach doit utiliser des mots de passe sécurisés et protéger ses accès.</p>
                                </div>
                            </section>

                            <div class="border-t border-purple-400/30 my-6"></div>

                            <!-- Section DPA 8 -->
                            <section class="mb-8">
                                <h3 class="text-xl font-bold text-purple-300 mb-4">8. SOUS-TRAITANTS ULTÉRIEURS</h3>
                                <div class="text-gray-300 space-y-3">
                                    <p>M4 Entreprise peut faire appel aux sous-traitants techniques suivants (liste indicative) :</p>
                                    <ul class="list-disc list-inside space-y-2 ml-4">
                                        <li>hébergeur cloud situé dans l'UE,</li>
                                        <li>service email transactionnel,</li>
                                        <li>CDN,</li>
                                        <li>solution de monitoring / logs.</li>
                                    </ul>
                                    <p class="mt-3">Tous sont basés dans l'UE ou dans un pays disposant d'un niveau de protection adéquat.</p>
                                    <p>Lorsque M4 Entreprise souhaite ajouter ou remplacer un sous-traitant, le Coach en est informé via tableau de bord ou email.</p>
                                    <p>Le Coach peut s'opposer au changement pour motif légitime.</p>
                                </div>
                            </section>

                            <div class="border-t border-purple-400/30 my-6"></div>

                            <!-- Section DPA 9 -->
                            <section class="mb-8">
                                <h3 class="text-xl font-bold text-purple-300 mb-4">9. TRANSFERTS DE DONNÉES HORS UE</h3>
                                <div class="text-gray-300 space-y-2">
                                    <p>Aucun transfert hors UE sans cadre légal approprié.</p>
                                    <p>En cas de transfert :</p>
                                    <ul class="list-disc list-inside space-y-2 ml-4">
                                        <li>pays adéquat ou</li>
                                        <li>Clauses Contractuelles Types.</li>
                                    </ul>
                                </div>
                            </section>

                            <div class="border-t border-purple-400/30 my-6"></div>

                            <!-- Section DPA 10 -->
                            <section class="mb-8">
                                <h3 class="text-xl font-bold text-purple-300 mb-4">10. ASSISTANCE DU SOUS-TRAITANT</h3>
                                <div class="text-gray-300 space-y-3">
                                    <p>M4 Entreprise aide le Coach à :</p>
                                    <ul class="list-disc list-inside space-y-2 ml-4">
                                        <li>répondre aux demandes d'accès, rectification, suppression,</li>
                                        <li>gérer les demandes d'opposition ou de portabilité,</li>
                                        <li>effectuer une analyse d'impact (DPIA) si nécessaire,</li>
                                        <li>notifier les violations de données aux autorités.</li>
                                    </ul>
                                    <p class="mt-3">Cette assistance est limitée au périmètre du Service.</p>
                                </div>
                            </section>

                            <div class="border-t border-purple-400/30 my-6"></div>

                            <!-- Section DPA 11 -->
                            <section class="mb-8">
                                <h3 class="text-xl font-bold text-purple-300 mb-4">11. VIOLATION DE DONNÉES</h3>
                                <div class="text-gray-300 space-y-3">
                                    <p>M4 Entreprise notifie au Coach toute violation de données dans un délai raisonnable après en avoir eu connaissance.</p>
                                    <p>La notification comprend :</p>
                                    <ul class="list-disc list-inside space-y-2 ml-4">
                                        <li>nature de l'incident,</li>
                                        <li>données concernées,</li>
                                        <li>mesures prises,</li>
                                        <li>recommandations au Coach.</li>
                                    </ul>
                                    <p class="mt-3">Le Coach reste responsable de notifier ses Clients Finaux et l'autorité de protection des données si requis.</p>
                                </div>
                            </section>

                            <div class="border-t border-purple-400/30 my-6"></div>

                            <!-- Section DPA 12 -->
                            <section class="mb-8">
                                <h3 class="text-xl font-bold text-purple-300 mb-4">12. SORT DES DONNÉES EN FIN DE CONTRAT</h3>
                                <div class="text-gray-300 space-y-2">
                                    <p>À la résiliation de l'abonnement :</p>
                                    <ul class="list-disc list-inside space-y-2 ml-4">
                                        <li>le Coach peut exporter ses données pendant 30 jours,</li>
                                        <li>au-delà : suppression sous 90 jours, sauf obligation légale de conservation.</li>
                                    </ul>
                                    <p class="mt-3">Aucune copie n'est conservée hors procédures techniques (logs, sauvegardes, obligations légales).</p>
                                </div>
                            </section>

                            <div class="border-t border-purple-400/30 my-6"></div>

                            <!-- Section DPA 13 -->
                            <section class="mb-8">
                                <h3 class="text-xl font-bold text-purple-300 mb-4">13. AUDITS</h3>
                                <div class="text-gray-300 space-y-2">
                                    <p>Le Coach peut demander des informations nécessaires pour vérifier la conformité du Sous-traitant.</p>
                                    <p>Aucun audit intrusif ou technique n'est autorisé sans justification sérieuse et préavis écrit.</p>
                                </div>
                            </section>

                            <div class="border-t border-purple-400/30 my-6"></div>

                            <!-- Section DPA 14 -->
                            <section class="mb-8">
                                <h3 class="text-xl font-bold text-purple-300 mb-4">14. RESPONSABILITÉ</h3>
                                <div class="text-gray-300 space-y-2">
                                    <p>Responsabilité du Sous-traitant limitée au périmètre technique.</p>
                                    <p>Le Coach reste responsable de :</p>
                                    <ul class="list-disc list-inside space-y-2 ml-4">
                                        <li>la collecte des données,</li>
                                        <li>ses pratiques commerciales,</li>
                                        <li>l'information de ses Clients Finaux.</li>
                                    </ul>
                                </div>
                            </section>

                            <div class="border-t border-purple-400/30 my-6"></div>

                            <!-- Section DPA 15 -->
                            <section class="mb-8">
                                <h3 class="text-xl font-bold text-purple-300 mb-4">15. DOCUMENTS CONTRACTUELS</h3>
                                <div class="text-gray-300 space-y-2">
                                    <p>Hiérarchie :</p>
                                    <ol class="list-decimal list-inside space-y-2 ml-4">
                                        <li>CGVU</li>
                                        <li>Présent Accord de sous-traitance</li>
                                        <li>Politique de confidentialité</li>
                                    </ol>
                                    <p class="mt-3">En cas de contradiction, le DPA prévaut sur les CGVU pour les aspects RGPD.</p>
                                </div>
                            </section>

                            <div class="border-t border-purple-400/30 my-6"></div>

                            <!-- Section DPA 16 -->
                            <section class="mb-8">
                                <h3 class="text-xl font-bold text-purple-300 mb-4">16. CONTACT RGPD</h3>
                                <div class="text-gray-300 space-y-2">
                                    <p>Email dédié : <strong><a href="mailto:support@unicoach.app" class="text-purple-400 hover:text-purple-300">support@unicoach.app</a></strong></p>
                                    <p>Autorité de contrôle : Autorité de Protection des Données (Belgique).</p>
                                </div>
                            </section>
                        </div>
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
                <p>&copy; 2026 UNICOACH - Développé par M4 Entreprise en partenariat avec Fitness Education Academy</p>
            </div>
        </footer>
    </div>
</template>
