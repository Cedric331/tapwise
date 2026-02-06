<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { update } from '@/routes/password';
import { QrCode, KeyRound } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
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
                            <KeyRound class="h-8 w-8 text-amber-800" />
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">Réinitialiser le mot de passe</h1>
                    <p class="mt-3 text-sm text-gray-600">
                        Veuillez entrer votre nouveau mot de passe ci-dessous
                    </p>
                </div>

                <Head title="Réinitialiser le mot de passe" />

                <!-- Formulaire -->
                <div class="rounded-2xl bg-white p-8 shadow-lg border border-gray-100">
                    <Form
                        v-bind="update.form()"
                        :transform="(data) => ({ ...data, token, email })"
                        :reset-on-success="['password', 'password_confirmation']"
                        v-slot="{ errors, processing }"
                        class="space-y-6"
                    >
                        <div class="grid gap-2">
                            <Label for="email" class="text-sm font-medium text-gray-700">Email</Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                autocomplete="email"
                                v-model="inputEmail"
                                class="h-11 bg-gray-50"
                                readonly
                            />
                            <InputError :message="errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password" class="text-sm font-medium text-gray-700">Nouveau mot de passe</Label>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                autocomplete="new-password"
                                class="h-11"
                                autofocus
                                placeholder="••••••••"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password_confirmation" class="text-sm font-medium text-gray-700">
                                Confirmer le mot de passe
                            </Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                autocomplete="new-password"
                                class="h-11"
                                placeholder="••••••••"
                            />
                            <InputError :message="errors.password_confirmation" />
                        </div>

                        <Button
                            type="submit"
                            class="mt-4 w-full h-11 bg-gradient-to-r from-amber-500 to-amber-800 hover:from-amber-600 hover:to-orange-700 text-white font-semibold shadow-lg transition-all hover:shadow-xl"
                            :disabled="processing"
                            data-test="reset-password-button"
                        >
                            <Spinner v-if="processing" />
                            <span v-if="!processing">Réinitialiser le mot de passe</span>
                        </Button>
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>
