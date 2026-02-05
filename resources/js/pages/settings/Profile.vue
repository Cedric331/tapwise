<script setup lang="ts">
import { Form, Head, Link, usePage } from '@inertiajs/vue3';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import { type BreadcrumbItem } from '@/types';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Paramètres du profil',
        href: edit().url,
    },
];

const page = usePage();
const user = page.props.auth.user;

const updateForm = {
    action: '/settings/profile?_method=PATCH',
    method: 'post',
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Paramètres du profil" />

        <h1 class="sr-only">Paramètres du profil</h1>

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <div class="rounded-3xl border border-amber-100 bg-white p-6 shadow-sm">
                    <Heading
                        variant="small"
                        title="Informations du profil"
                        description="Mettre à jour votre nom et votre adresse email"
                    />

                    <Form
                        v-bind="updateForm"
                        class="mt-6 space-y-6"
                        v-slot="{ errors, processing, recentlySuccessful }"
                    >
                        <div class="grid gap-2">
                            <Label for="name">Nom</Label>
                            <Input
                                id="name"
                                class="mt-1 block w-full border-amber-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                                name="name"
                                :default-value="user.name"
                                required
                                autocomplete="name"
                                placeholder="Nom complet"
                            />
                            <InputError class="mt-2" :message="errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Adresse email</Label>
                            <Input
                                id="email"
                                type="email"
                                class="mt-1 block w-full border-amber-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                                name="email"
                                :default-value="user.email"
                                required
                                autocomplete="username"
                                placeholder="email@example.com"
                            />
                            <InputError class="mt-2" :message="errors.email" />
                        </div>

                        <div v-if="mustVerifyEmail && !user.email_verified_at">
                            <p class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                Votre adresse email n'est pas vérifiée.
                                <Link
                                    :href="send()"
                                    as="button"
                                    class="ml-1 font-medium text-amber-900 underline decoration-amber-400 underline-offset-4 transition-colors hover:decoration-amber-600"
                                >
                                    Renvoyer l'email de vérification.
                                </Link>
                            </p>

                            <div
                                v-if="status === 'verification-link-sent'"
                                class="mt-2 text-sm font-medium text-green-600"
                            >
                                Un nouveau lien de vérification a été envoyé à votre adresse email
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button
                                :disabled="processing"
                                data-test="update-profile-button"
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

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
