<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/user-password';
import { type BreadcrumbItem } from '@/types';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Paramètres du mot de passe',
        href: edit().url,
    },
];

const updateForm = {
    action: '/settings/password?_method=PUT',
    method: 'post',
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Paramètres du mot de passe" />

        <h1 class="sr-only">Paramètres du mot de passe</h1>

        <SettingsLayout>
            <div class="space-y-6">
                <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                    <Heading
                        variant="small"
                        title="Mettre à jour le mot de passe"
                        description="Veuillez utiliser un mot de passe long et aléatoire pour rester sécurisé"
                    />

                    <Form
                        v-bind="updateForm"
                        :options="{
                            preserveScroll: true,
                        }"
                        reset-on-success
                        :reset-on-error="[
                            'password',
                            'password_confirmation',
                            'current_password',
                        ]"
                        class="mt-6 space-y-6"
                        v-slot="{ errors, processing, recentlySuccessful }"
                    >
                        <div class="grid gap-2">
                            <Label for="current_password">Mot de passe actuel</Label>
                            <Input
                                id="current_password"
                                name="current_password"
                                type="password"
                                class="mt-1 block w-full border-amber-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />
                            <InputError :message="errors.current_password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password">Nouveau mot de passe</Label>
                            <Input
                                id="password"
                                name="password"
                                type="password"
                                class="mt-1 block w-full border-amber-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password_confirmation">Confirmer le mot de passe</Label>
                            <Input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                class="mt-1 block w-full border-amber-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />
                            <InputError :message="errors.password_confirmation" />
                        </div>

                        <div class="flex items-center gap-4">
                            <Button
                                :disabled="processing"
                                data-test="update-password-button"
                                class="bg-gradient-to-r from-amber-500 to-amber-800 text-white shadow-lg transition-all hover:shadow-xl"
                            >
                                Enregistrer
                            </Button>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p
                                    v-show="recentlySuccessful"
                                    class="text-sm text-neutral-600"
                                >
                                    Enregistré.
                                </p>
                            </Transition>
                        </div>
                    </Form>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
