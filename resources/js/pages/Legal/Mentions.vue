<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Shield, Mail, MapPin } from 'lucide-vue-next';

const baseUrl = 'https://app.tapwise.fr';
const pageUrl = `${baseUrl}/mentions-legales`;
const ogImage = `${baseUrl}/assets/illustration-beer-glass.png`;
</script>

<template>
    <Head>
        <title>Mentions légales - Tapwise</title>
        <meta name="description" content="Mentions légales de Tapwise - Informations sur l'éditeur, l'hébergeur et les données personnelles." />
        <meta name="robots" content="index, follow" />
        <meta property="og:title" content="Mentions légales - Tapwise" />
        <meta property="og:description" content="Informations légales sur Tapwise, l'éditeur et l'hébergeur du site." />
        <meta property="og:type" content="article" />
        <meta property="og:url" :content="pageUrl" />
        <meta property="og:image" :content="ogImage" />
        <meta property="og:image:alt" content="Tapwise - Mentions légales" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Mentions légales - Tapwise" />
        <meta name="twitter:description" content="Informations légales sur Tapwise." />
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
                <h1 class="text-4xl font-bold text-gray-900">Mentions légales</h1>
                <p class="mt-2 text-sm text-gray-500">Dernière mise à jour : {{ new Date().toLocaleDateString('fr-FR') }}</p>

                <div class="mt-8 space-y-8 prose prose-gray max-w-none">
                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Éditeur du site</h2>
                        <div class="space-y-2 text-gray-700">
                            <p><strong>Raison sociale :</strong> Tapwise</p>
                            <p><strong>Forme juridique :</strong> Société par Actions Simplifiée (SAS)</p>
                            <p><strong>Siège social :</strong> [Adresse à compléter]</p>
                            <p><strong>SIRET :</strong> [Numéro à compléter]</p>
                            <p><strong>RCS :</strong> [Numéro à compléter]</p>
                            <p><strong>Directeur de publication :</strong> [Nom à compléter]</p>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Hébergement</h2>
                        <div class="space-y-2 text-gray-700">
                            <p>Le site est hébergé par :</p>
                            <p><strong>Hébergeur :</strong> [Nom de l'hébergeur à compléter]</p>
                            <p><strong>Adresse :</strong> [Adresse à compléter]</p>
                            <p><strong>Téléphone :</strong> [Numéro à compléter]</p>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Propriété intellectuelle</h2>
                        <p class="text-gray-700">
                            L'ensemble de ce site relève de la législation française et internationale sur le droit d'auteur et la propriété intellectuelle. 
                            Tous les droits de reproduction sont réservés, y compris pour les documents téléchargeables et les représentations iconographiques et photographiques.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Protection des données personnelles</h2>
                        <p class="text-gray-700 mb-4">
                            Conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi Informatique et Libertés, 
                            vous disposez d'un droit d'accès, de rectification, de suppression et d'opposition aux données personnelles vous concernant.
                        </p>
                        <p class="text-gray-700">
                            Pour exercer ces droits, vous pouvez nous contacter à l'adresse suivante : 
                            <Link href="/contact" class="text-indigo-600 hover:text-indigo-700">via notre formulaire de contact</Link>.
                        </p>
                        <p class="mt-4 text-gray-700">
                            Pour plus d'informations, consultez notre 
                            <Link href="/politique-de-confidentialite" class="text-indigo-600 hover:text-indigo-700">Politique de confidentialité</Link>.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Cookies</h2>
                        <p class="text-gray-700">
                            Ce site utilise des cookies pour améliorer l'expérience utilisateur. 
                            En continuant à naviguer sur ce site, vous acceptez l'utilisation de cookies.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Contact</h2>
                        <div class="space-y-3 text-gray-700">
                            <div class="flex items-center gap-3">
                                <Mail class="h-5 w-5 text-indigo-600" />
                                <span>Email : <a href="mailto:contact@tapwise.fr" class="text-indigo-600 hover:text-indigo-700">contact@tapwise.fr</a></span>
                            </div>
                            <div class="flex items-center gap-3">
                                <MapPin class="h-5 w-5 text-indigo-600" />
                                <span>Adresse : [Adresse à compléter]</span>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>
