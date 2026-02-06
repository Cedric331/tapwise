<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/password/confirm';
import { QrCode, Shield } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-white">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-md">
                <!-- Logo et titre -->
                <div class="mb-10 text-center">
                    <div class="mb-6 flex justify-center">
                        <AppLogo class="h-14 w-14 text-white" />
                    </div>
                    <div class="mb-4 flex justify-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-amber-100">
                            <Shield class="h-8 w-8 text-amber-800" />
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">Confirmer votre mot de passe</h1>
                    <p class="mt-3 text-sm text-gray-600">
                        Il s'agit d'une zone sécurisée de l'application. Veuillez confirmer votre mot de passe avant de continuer.
                    </p>
                </div>

                <Head title="Confirmer le mot de passe" />

                <!-- Formulaire -->
                <div class="rounded-2xl bg-white p-8 shadow-lg border border-gray-100">
                    <Form
                        v-bind="store.form()"
                        reset-on-success
                        v-slot="{ errors, processing }"
                        class="space-y-6"
                    >
                        <div class="grid gap-2">
                            <Label for="password" class="text-sm font-medium text-gray-700">Mot de passe</Label>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                class="h-11"
                                required
                                autocomplete="current-password"
                                autofocus
                                placeholder="••••••••"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <Button
                            type="submit"
                            class="w-full h-11 bg-gradient-to-r dark:from-amber-500 dark:to-amber-800 from-amber-500 to-amber-800 hover:from-amber-600 hover:to-orange-700 text-white font-semibold shadow-lg transition-all hover:shadow-xl"
                            :disabled="processing"
                            data-test="confirm-password-button"
                        >
                            <Spinner v-if="processing" />
                            <span v-if="!processing">Confirmer le mot de passe</span>
                        </Button>
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>
