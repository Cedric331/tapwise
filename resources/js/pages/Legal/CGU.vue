<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, FileText, AlertCircle } from 'lucide-vue-next';
</script>

<template>
    <Head>
        <title>Conditions générales d'utilisation - Tapwise</title>
        <meta name="description" content="Conditions générales d'utilisation de Tapwise - CGU de la plateforme." />
        <meta name="robots" content="index, follow" />
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
                        <FileText class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900">Conditions générales d'utilisation</h1>
                        <p class="mt-1 text-sm text-gray-500">CGU de la plateforme Tapwise</p>
                    </div>
                </div>

                <p class="mb-8 text-sm text-gray-600">
                    <strong>Dernière mise à jour :</strong> {{ new Date().toLocaleDateString('fr-FR') }}
                </p>

                <div class="space-y-8 prose prose-gray max-w-none">
                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. Objet</h2>
                        <p class="text-gray-700">
                            Les présentes Conditions Générales d'Utilisation (CGU) ont pour objet de définir les conditions et modalités 
                            d'utilisation de la plateforme Tapwise, ainsi que les droits et obligations des parties dans ce cadre.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Acceptation des CGU</h2>
                        <p class="text-gray-700">
                            L'utilisation de la plateforme Tapwise implique l'acceptation pleine et entière des présentes CGU. 
                            En créant un compte, vous reconnaissez avoir lu, compris et accepté ces conditions.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Description du service</h2>
                        <p class="text-gray-700 mb-4">
                            Tapwise est une plateforme SaaS permettant aux établissements (bars, restaurants) de :
                        </p>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                            <li>Gérer un catalogue de bières</li>
                            <li>Générer un QR code personnalisé</li>
                            <li>Offrir à leurs clients un système de recommandation de bières basé sur leurs préférences</li>
                            <li>Personnaliser l'apparence de leur interface publique</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Inscription et compte utilisateur</h2>
                        <div class="space-y-4 text-gray-700">
                            <p>
                                Pour utiliser les services de Tapwise, vous devez créer un compte en fournissant des informations exactes et à jour.
                            </p>
                            <p>
                                Vous êtes responsable de la confidentialité de vos identifiants de connexion et de toutes les activités 
                                effectuées sous votre compte.
                            </p>
                            <p>
                                Vous vous engagez à ne pas partager vos identifiants avec des tiers et à nous informer immédiatement 
                                de toute utilisation non autorisée de votre compte.
                            </p>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Tarification et facturation</h2>
                        <div class="space-y-4 text-gray-700">
                            <p>
                                L'abonnement à Tapwise est facturé mensuellement au prix de 29€ TTC (tarif fondateur).
                            </p>
                            <p>
                                Le paiement est effectué par carte bancaire ou autre moyen de paiement accepté. 
                                L'abonnement est renouvelé automatiquement chaque mois.
                            </p>
                            <p>
                                Vous pouvez résilier votre abonnement à tout moment depuis votre espace client. 
                                La résiliation prend effet à la fin de la période en cours.
                            </p>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Obligations de l'utilisateur</h2>
                        <p class="text-gray-700 mb-4">Vous vous engagez à :</p>
                        <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                            <li>Utiliser la plateforme conformément à sa destination</li>
                            <li>Ne pas utiliser la plateforme à des fins illégales ou frauduleuses</li>
                            <li>Respecter les droits de propriété intellectuelle</li>
                            <li>Ne pas tenter de contourner les mesures de sécurité</li>
                            <li>Fournir des informations exactes et à jour</li>
                            <li>Respecter les droits des tiers</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Disponibilité du service</h2>
                        <p class="text-gray-700">
                            Nous nous efforçons d'assurer une disponibilité du service 24/7, mais ne pouvons garantir une disponibilité 
                            ininterrompue. Des interruptions peuvent survenir pour maintenance, mise à jour ou cas de force majeure.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. Propriété intellectuelle</h2>
                        <p class="text-gray-700">
                            La plateforme Tapwise, son contenu, sa structure et ses fonctionnalités sont protégés par le droit de la propriété 
                            intellectuelle. Toute reproduction, même partielle, est interdite sans autorisation préalable.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">9. Responsabilité</h2>
                        <div class="space-y-4 text-gray-700">
                            <p>
                                Tapwise ne saurait être tenu responsable des dommages directs ou indirects résultant de l'utilisation 
                                ou de l'impossibilité d'utiliser la plateforme.
                            </p>
                            <p>
                                L'utilisateur est seul responsable du contenu qu'il publie sur la plateforme (bières, logos, textes).
                            </p>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">10. Résiliation</h2>
                        <p class="text-gray-700">
                            Nous nous réservons le droit de suspendre ou résilier votre compte en cas de violation des présentes CGU, 
                            sans préavis ni remboursement.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">11. Modification des CGU</h2>
                        <p class="text-gray-700">
                            Nous nous réservons le droit de modifier les présentes CGU à tout moment. 
                            Les modifications entrent en vigueur dès leur publication. Votre utilisation continue de la plateforme 
                            vaut acceptation des nouvelles conditions.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">12. Droit applicable et juridiction</h2>
                        <p class="text-gray-700">
                            Les présentes CGU sont régies par le droit français. 
                            En cas de litige, et après tentative de résolution amiable, les tribunaux français seront seuls compétents.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-4">13. Contact</h2>
                        <p class="text-gray-700">
                            Pour toute question concernant les présentes CGU, vous pouvez nous contacter à : 
                            <a href="mailto:contact@tapwise.fr" class="text-indigo-600 hover:text-indigo-700">contact@tapwise.fr</a>
                            ou via notre <Link href="/contact" class="text-indigo-600 hover:text-indigo-700">formulaire de contact</Link>.
                        </p>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>

