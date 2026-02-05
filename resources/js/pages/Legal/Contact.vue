<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Mail, Send, MapPin, Phone } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
});

const submit = () => {
    // TODO: Implement contact form submission
    alert('Formulaire de contact à implémenter');
};
</script>

<template>
    <Head>
        <title>Contact - Tapwise</title>
        <meta name="description" content="Contactez l'équipe Tapwise pour toute question ou demande d'information." />
        <meta name="robots" content="index, follow" />
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50">
        <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
            <Link href="/" class="mb-8 inline-flex items-center gap-2 text-sm font-medium text-indigo-600 transition-colors hover:text-indigo-700">
                <ArrowLeft class="h-4 w-4" />
                Retour à l'accueil
            </Link>

            <div class="grid gap-8 lg:grid-cols-3">
                <!-- Contact Info -->
                <div class="lg:col-span-1">
                    <h1 class="text-4xl font-bold text-gray-900">Contactez-nous</h1>
                    <p class="mt-4 text-gray-600">
                        Une question ? Une demande ? Notre équipe est là pour vous aider.
                    </p>

                    <div class="mt-8 space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                                <Mail class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Email</h3>
                                <a href="mailto:contact@tapwise.fr" class="mt-1 text-sm text-gray-600 hover:text-indigo-600">
                                    contact@tapwise.fr
                                </a>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                                <Phone class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Téléphone</h3>
                                <p class="mt-1 text-sm text-gray-600">[Numéro à compléter]</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                                <MapPin class="h-5 w-5" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Adresse</h3>
                                <p class="mt-1 text-sm text-gray-600">[Adresse à compléter]</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lg:col-span-2">
                    <div class="rounded-xl bg-white p-8 shadow-sm ring-1 ring-gray-200">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet *</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="Jean Dupont"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="jean@example.com"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sujet *</label>
                                <input
                                    v-model="form.subject"
                                    type="text"
                                    required
                                    class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="Question sur..."
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                                <textarea
                                    v-model="form.message"
                                    rows="6"
                                    required
                                    class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="Votre message..."
                                />
                            </div>

                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <input type="checkbox" required class="h-4 w-4 rounded border-gray-300" />
                                <span>
                                    J'accepte que mes données soient utilisées pour répondre à ma demande. 
                                    <Link href="/politique-de-confidentialite" class="text-indigo-600 hover:text-indigo-700">
                                        En savoir plus
                                    </Link>
                                </span>
                            </div>

                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all hover:shadow-xl disabled:opacity-50"
                                :disabled="form.processing"
                            >
                                <Send class="h-4 w-4" />
                                <span v-if="form.processing">Envoi en cours...</span>
                                <span v-else>Envoyer le message</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
