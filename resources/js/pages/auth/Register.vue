<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { QrCode } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';

const barName = ref('');
const barSlug = ref('');

const generateSlug = () => {
    if (barName.value) {
        barSlug.value = barName.value
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
};

const previewUrl = computed(() => {
    if (!barSlug.value) return '';
    return `https://app.tapwise.fr/b/${barSlug.value}`;
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-white">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-2xl">
                <!-- Logo et titre -->
                <div class="mb-10 text-center">
                    <div class="mb-6 flex justify-center">
                        <AppLogo class="h-14 w-14 text-white" />
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">Créer un compte bar</h1>
                    <p class="mt-3 text-sm text-gray-600">
                        Remplissez les informations ci-dessous pour créer votre compte
                    </p>
                </div>

                <Head title="Inscription" />

                <!-- Formulaire -->
                <div class="rounded-2xl bg-white p-8 shadow-lg border border-gray-100">
                    <Form
                        v-bind="store.form()"
                        :reset-on-success="['password', 'password_confirmation', 'bar_name', 'bar_slug']"
                        v-slot="{ errors, processing }"
                        class="flex flex-col gap-8"
                    >
                        <!-- Informations personnelles -->
                        <div class="space-y-6">
                            <div>
                                <h3 class="mb-4 text-lg font-semibold text-gray-900">Informations personnelles</h3>
                                <div class="grid gap-4">
                                    <div class="grid gap-2">
                                        <Label for="name" class="text-sm font-medium text-gray-700">
                                            Nom complet <span class="text-red-500">*</span>
                                        </Label>
                                        <Input
                                            id="name"
                                            type="text"
                                            required
                                            autofocus
                                            :tabindex="1"
                                            autocomplete="name"
                                            name="name"
                                            placeholder="Jean Dupont"
                                            class="h-11"
                                        />
                                        <InputError :message="errors.name" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="email" class="text-sm font-medium text-gray-700">
                                            Adresse email <span class="text-red-500">*</span>
                                        </Label>
                                        <Input
                                            id="email"
                                            type="email"
                                            required
                                            :tabindex="2"
                                            autocomplete="email"
                                            name="email"
                                            placeholder="email@example.com"
                                            class="h-11"
                                        />
                                        <InputError :message="errors.email" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="password" class="text-sm font-medium text-gray-700">
                                            Mot de passe <span class="text-red-500">*</span>
                                        </Label>
                                        <Input
                                            id="password"
                                            type="password"
                                            required
                                            :tabindex="3"
                                            autocomplete="new-password"
                                            name="password"
                                            placeholder="••••••••"
                                            class="h-11"
                                        />
                                        <InputError :message="errors.password" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="password_confirmation" class="text-sm font-medium text-gray-700">
                                            Confirmer le mot de passe <span class="text-red-500">*</span>
                                        </Label>
                                        <Input
                                            id="password_confirmation"
                                            type="password"
                                            required
                                            :tabindex="4"
                                            autocomplete="new-password"
                                            name="password_confirmation"
                                            placeholder="••••••••"
                                            class="h-11"
                                        />
                                        <InputError :message="errors.password_confirmation" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations du bar -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="mb-4 text-lg font-semibold text-gray-900">Informations du bar</h3>
                            <div class="grid gap-4">
                                <div class="grid gap-2">
                                    <Label for="bar_name" class="text-sm font-medium text-gray-700">
                                        Nom du bar <span class="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        id="bar_name"
                                        v-model="barName"
                                        type="text"
                                        required
                                        :tabindex="5"
                                        name="bar_name"
                                        placeholder="Mon Bar"
                                        class="h-11"
                                        @input="generateSlug"
                                    />
                                    <InputError :message="errors.bar_name" />
                                    <p class="text-xs text-gray-500">
                                        Le nom de votre établissement
                                    </p>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="bar_slug" class="text-sm font-medium text-gray-700">
                                        Identifiant unique (slug) <span class="text-red-500">*</span>
                                    </Label>
                                    <div class="flex gap-2">
                                        <div class="flex-1">
                                            <Input
                                                id="bar_slug"
                                                v-model="barSlug"
                                                type="text"
                                                required
                                                :tabindex="6"
                                                name="bar_slug"
                                                placeholder="mon-bar"
                                                pattern="[a-z0-9-]+"
                                                class="h-11"
                                            />
                                            <InputError :message="errors.bar_slug" />
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        Utilisé dans l'URL publique. Seulement des lettres minuscules, chiffres et tirets.
                                    </p>
                                    <div v-if="previewUrl" class="mt-2 rounded-lg bg-gray-50 border border-gray-200 p-3 text-xs">
                                        <span class="text-gray-600">URL publique :</span>
                                        <span class="ml-2 font-mono text-gray-900">{{ previewUrl }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <Button
                            type="submit"
                            class="mt-4 h-11 w-full bg-gradient-to-r from-amber-500 to-amber-800 hover:from-amber-600 hover:to-orange-700 text-white font-semibold shadow-lg transition-all hover:shadow-xl"
                            tabindex="7"
                            :disabled="processing"
                            data-test="register-user-button"
                        >
                            <Spinner v-if="processing" />
                            <span v-if="!processing">Créer mon compte</span>
                        </Button>

                        <div class="pt-4 text-center text-sm text-gray-600 border-t border-gray-100">
                            Vous avez déjà un compte ?
                            <TextLink
                                :href="login()"
                                class="dark:text-amber-500 text-amber-500 dark:hover:text-amber-800 hover:text-amber-800 font-medium ml-1"
                                :tabindex="8"
                            >
                                Se connecter
                            </TextLink>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>
