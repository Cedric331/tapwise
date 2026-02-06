<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Shield, Database, Lock, Eye, Trash2 } from 'lucide-vue-next';

const baseUrl = 'https://app.tapwise.fr';
const pageUrl = `${baseUrl}/politique-de-confidentialite`;
const ogImage = `${baseUrl}/assets/illustration-beer-glass.png`;
</script>

<template>
    <Head>
        <title>Politique de confidentialité - Tapwise</title>
        <meta name="description" content="Politique de confidentialité de Tapwise - Protection des données personnelles conforme RGPD." />
        <meta name="robots" content="index, follow" />
        <meta property="og:title" content="Politique de confidentialité - Tapwise" />
        <meta property="og:description" content="Protection des données personnelles conforme au RGPD pour Tapwise." />
        <meta property="og:type" content="article" />
        <meta property="og:url" :content="pageUrl" />
        <meta property="og:image" :content="ogImage" />
        <meta property="og:image:alt" content="Tapwise - Politique de confidentialité" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Politique de confidentialité - Tapwise" />
        <meta name="twitter:description" content="Protection des données personnelles conforme au RGPD." />
        <meta name="twitter:image" :content="ogImage" />
        <link rel="canonical" :href="pageUrl" />
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50">
        <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
            <Link href="/" class="mb-8 inline-flex items-center gap-2 text-sm font-medium text-indigo-600 transition-colors hover:text-indigo-700">
                <ArrowLeft class="h-4 w-4" />
                Retour à l'accueil
            </Link>

            <div class="rounded-xl bg-white p-8 shadow-sm ring-1 ring-gray-200">
                <div class="mb-8 flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                        <Shield class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900">Politique de confidentialité</h1>
                        <p class="mt-1 text-sm text-gray-500">Conforme au Règlement Général sur la Protection des Données (RGPD)</p>
                    </div>
                </div>

                <p class="mb-8 text-sm text-gray-600">
                    <strong>Dernière mise à jour :</strong> {{ new Date().toLocaleDateString('fr-FR') }}
                </p>

                <div class="space-y-8 prose prose-gray max-w-none">
                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Introduction</h2>
                        <p class="text-gray-700">
                            Tapwise s'engage à protéger la confidentialité et la sécurité des données personnelles de ses utilisateurs. 
                            Cette politique de confidentialité explique comment nous collectons, utilisons, stockons et protégeons vos données personnelles 
                            conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi Informatique et Libertés.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Données collectées</h2>
                        <div class="space-y-4 text-gray-700">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">2.1. Données collectées lors de l'inscription</h3>
                                <ul class="list-disc list-inside space-y-1 ml-4">
                                    <li>Nom complet</li>
                                    <li>Adresse email</li>
                                    <li>Mot de passe (hashé)</li>
                                    <li>Nom de l'établissement</li>
                                    <li>Identifiant unique (slug) de l'établissement</li>
                                </ul>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">2.2. Données collectées lors de l'utilisation</h3>
                                <ul class="list-disc list-inside space-y-1 ml-4">
                                    <li>Informations sur les bières ajoutées au catalogue</li>
                                    <li>Paramètres de personnalisation (logo, couleurs, messages)</li>
                                    <li>Préférences de configuration</li>
                                </ul>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">2.3. Données techniques</h3>
                                <ul class="list-disc list-inside space-y-1 ml-4">
                                    <li>Adresse IP</li>
                                    <li>Type de navigateur</li>
                                    <li>Données de connexion</li>
                                    <li>Cookies techniques nécessaires au fonctionnement</li>
                                </ul>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Finalités du traitement</h2>
                        <p class="text-gray-700 mb-4">Vos données personnelles sont utilisées pour :</p>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                            <li>Gérer votre compte et votre accès à la plateforme</li>
                            <li>Fournir les services demandés (gestion de bières, génération de QR codes)</li>
                            <li>Personnaliser votre expérience utilisateur</li>
                            <li>Vous contacter concernant votre compte ou nos services</li>
                            <li>Améliorer nos services et développer de nouvelles fonctionnalités</li>
                            <li>Respecter nos obligations légales et réglementaires</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Base légale du traitement</h2>
                        <p class="text-gray-700">
                            Le traitement de vos données personnelles est basé sur :
                        </p>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4 mt-2">
                            <li><strong>L'exécution d'un contrat :</strong> pour fournir les services souscrits</li>
                            <li><strong>Votre consentement :</strong> pour l'utilisation de cookies non essentiels</li>
                            <li><strong>L'intérêt légitime :</strong> pour améliorer nos services et prévenir la fraude</li>
                            <li><strong>L'obligation légale :</strong> pour respecter nos obligations comptables et fiscales</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Conservation des données</h2>
                        <p class="text-gray-700">
                            Vos données personnelles sont conservées pendant la durée nécessaire aux finalités pour lesquelles elles ont été collectées, 
                            et au maximum pendant 3 ans après la fermeture de votre compte, sauf obligation légale de conservation plus longue.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Vos droits</h2>
                        <p class="text-gray-700 mb-4">Conformément au RGPD, vous disposez des droits suivants :</p>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <Eye class="h-5 w-5 text-indigo-600 mb-2" />
                                <h3 class="font-semibold text-gray-900 mb-1">Droit d'accès</h3>
                                <p class="text-sm text-gray-600">Consulter vos données personnelles</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <Database class="h-5 w-5 text-indigo-600 mb-2" />
                                <h3 class="font-semibold text-gray-900 mb-1">Droit de rectification</h3>
                                <p class="text-sm text-gray-600">Corriger vos données inexactes</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <Trash2 class="h-5 w-5 text-indigo-600 mb-2" />
                                <h3 class="font-semibold text-gray-900 mb-1">Droit à l'effacement</h3>
                                <p class="text-sm text-gray-600">Supprimer vos données</p>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <Lock class="h-5 w-5 text-indigo-600 mb-2" />
                                <h3 class="font-semibold text-gray-900 mb-1">Droit à la portabilité</h3>
                                <p class="text-sm text-gray-600">Récupérer vos données</p>
                            </div>
                        </div>
                        <p class="mt-6 text-gray-700">
                            Pour exercer ces droits, contactez-nous à : 
                            <a href="mailto:contact@tapwise.fr" class="text-indigo-600 hover:text-indigo-700">contact@tapwise.fr</a>
                            ou via notre <Link href="/contact" class="text-indigo-600 hover:text-indigo-700">formulaire de contact</Link>.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Sécurité des données</h2>
                        <p class="text-gray-700">
                            Nous mettons en œuvre des mesures techniques et organisationnelles appropriées pour protéger vos données personnelles 
                            contre tout accès non autorisé, perte, destruction ou altération. Vos mots de passe sont hashés et vos données sont 
                            stockées sur des serveurs sécurisés.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. Partage des données</h2>
                        <p class="text-gray-700">
                            Nous ne vendons, ne louons ni ne partageons vos données personnelles avec des tiers, sauf :
                        </p>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4 mt-2">
                            <li>Lorsque cela est nécessaire pour fournir nos services (hébergeur, prestataires techniques)</li>
                            <li>Lorsque la loi l'exige</li>
                            <li>Avec votre consentement explicite</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">9. Cookies</h2>
                        <p class="text-gray-700">
                            Nous utilisons des cookies techniques nécessaires au fonctionnement du site. 
                            Ces cookies ne nécessitent pas votre consentement. Nous n'utilisons pas de cookies de tracking publicitaire.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">10. Contact</h2>
                        <p class="text-gray-700">
                            Pour toute question concernant cette politique de confidentialité ou pour exercer vos droits, 
                            vous pouvez nous contacter à : <a href="mailto:contact@tapwise.fr" class="text-indigo-600 hover:text-indigo-700">contact@tapwise.fr</a>
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">11. Réclamation</h2>
                        <p class="text-gray-700">
                            Si vous estimez que vos droits ne sont pas respectés, vous pouvez introduire une réclamation auprès de la 
                            Commission Nationale de l'Informatique et des Libertés (CNIL) : 
                            <a href="https://www.cnil.fr" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-700">www.cnil.fr</a>
                        </p>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>

