<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { email } from '@/routes/password';
import { QrCode, Mail } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-white">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-md">
                <!-- Logo et titre -->
                <div class="mb-10 text-center">
                    <div class="mb-6 flex justify-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-amber-800 shadow-md">
                            <QrCode class="h-7 w-7 text-white" />
                        </div>
                    </div>
                    <div class="mb-4 flex justify-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-amber-100">
                            <Mail class="h-8 w-8 text-amber-800" />
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">Mot de passe oublié</h1>
                    <p class="mt-3 text-sm text-gray-600">
                        Entrez votre email pour recevoir un lien de réinitialisation
                    </p>
                </div>

                <Head title="Mot de passe oublié" />

                <!-- Message de statut -->
                <div
                    v-if="status"
                    class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4 text-center text-sm font-medium text-green-800"
                >
                    {{ status }}
                </div>

                <!-- Formulaire -->
                <div class="rounded-2xl bg-white p-8 shadow-lg border border-gray-100">
                    <Form v-bind="email.form()" v-slot="{ errors, processing }" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="email" class="text-sm font-medium text-gray-700">Adresse email</Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                autocomplete="off"
                                autofocus
                                placeholder="email@example.com"
                                class="h-11"
                            />
                            <InputError :message="errors.email" />
                        </div>

                        <Button
                            type="submit"
                            class="w-full h-11 bg-gradient-to-r from-amber-500 to-amber-800 hover:from-amber-600 hover:to-orange-700 text-white font-semibold shadow-lg transition-all hover:shadow-xl"
                            :disabled="processing"
                            data-test="email-password-reset-link-button"
                        >
                            <Spinner v-if="processing" />
                            <span v-if="!processing">Envoyer le lien de réinitialisation</span>
                        </Button>

                        <div class="pt-4 text-center text-sm text-gray-600 border-t border-gray-100">
                            <span>Ou, retournez à la page de</span>
                            <TextLink 
                                :href="login()" 
                                class="dark:text-amber-500 text-amber-500 dark:hover:text-amber-800 hover:text-amber-800 font-medium ml-1"
                            >
                                Connexion
                            </TextLink>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>
