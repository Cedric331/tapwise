<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { QrCode } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
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
                    <h1 class="text-3xl font-bold text-gray-900">Connexion</h1>
                    <p class="mt-3 text-sm text-gray-600">
                        Entrez votre email et mot de passe pour vous connecter
                    </p>
                </div>

                <Head title="Connexion" />

                <!-- Message de statut -->
                <div
                    v-if="status"
                    class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4 text-center text-sm font-medium text-green-800"
                >
                    {{ status }}
                </div>

                <!-- Formulaire -->
                <div class="rounded-2xl bg-white p-8 shadow-lg border border-gray-100">
                    <Form
                        v-bind="store.form()"
                        :reset-on-success="['password']"
                        v-slot="{ errors, processing }"
                        class="flex flex-col gap-6"
                    >
                        <div class="grid gap-6">
                            <div class="grid gap-2">
                                <Label for="email" class="text-sm font-medium text-gray-700">Adresse email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    autofocus
                                    :tabindex="1"
                                    autocomplete="email"
                                    placeholder="email@example.com"
                                    class="h-11"
                                />
                                <InputError :message="errors.email" />
                            </div>

                            <div class="grid gap-2">
                                <div class="flex items-center justify-between">
                                    <Label for="password" class="text-sm font-medium text-gray-700">Mot de passe</Label>
                                    <TextLink
                                        v-if="canResetPassword"
                                        :href="request()"
                                        class="text-sm dark:text-amber-500 text-amber-500 dark:hover:text-amber-800 hover:text-amber-800 font-medium"
                                        :tabindex="5"
                                    >
                                        Mot de passe oublié ?
                                    </TextLink>
                                </div>
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    :tabindex="2"
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="h-11"
                                />
                                <InputError :message="errors.password" />
                            </div>

                            <div class="flex items-center">
                                <Label for="remember" class="flex items-center space-x-3 cursor-pointer">
                                    <Checkbox id="remember" name="remember" :tabindex="3" />
                                    <span class="text-sm text-gray-700">Se souvenir de moi</span>
                                </Label>
                            </div>

                            <Button
                                type="submit"
                                class="mt-2 h-11 w-full bg-gradient-to-r from-amber-500 to-amber-800 hover:from-amber-600 hover:to-orange-700 text-white font-semibold shadow-lg transition-all hover:shadow-xl"
                                :tabindex="4"
                                :disabled="processing"
                                data-test="login-button"
                            >
                                <Spinner v-if="processing" />
                                <span v-if="!processing">Se connecter</span>
                            </Button>
                        </div>

                        <div
                            v-if="canRegister"
                            class="pt-4 text-center text-sm text-gray-600 border-t border-gray-100"
                        >
                            Vous n'avez pas de compte ?
                            <TextLink 
                                :href="register()" 
                                class="dark:text-amber-500 text-amber-500 dark:hover:text-amber-800 hover:text-amber-800 font-medium ml-1"
                                :tabindex="5"
                            >
                                Créer un compte
                            </TextLink>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>
