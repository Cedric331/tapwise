<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { logout } from '@/routes';
import { send } from '@/routes/verification';
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
                    <h1 class="text-3xl font-bold text-gray-900">Vérification de l'email</h1>
                    <p class="mt-3 text-sm text-gray-600">
                        Veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer.
                    </p>
                </div>

                <Head title="Vérification de l'email" />

                <!-- Message de succès -->
                <div
                    v-if="status === 'verification-link-sent'"
                    class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4 text-center text-sm font-medium text-green-800"
                >
                    Un nouveau lien de vérification a été envoyé à l'adresse email que vous avez fournie lors de l'inscription.
                </div>

                <!-- Formulaire -->
                <div class="rounded-2xl bg-white p-8 shadow-lg border border-gray-100">
                    <Form
                        v-bind="send.form()"
                        class="space-y-6 text-center"
                        v-slot="{ processing }"
                    >
                        <Button 
                            :disabled="processing" 
                            class="w-full h-11 bg-gradient-to-r from-amber-500 to-amber-800 hover:from-amber-600 hover:to-orange-700 text-white font-semibold shadow-lg transition-all hover:shadow-xl"
                        >
                            <Spinner v-if="processing" />
                            <span v-if="!processing">Renvoyer l'email de vérification</span>
                        </Button>

                        <div class="pt-4 border-t border-gray-100">
                            <TextLink
                                :href="logout()"
                                as="button"
                                class="text-sm text-gray-600 hover:text-gray-900 font-medium"
                            >
                                Se déconnecter
                            </TextLink>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>
