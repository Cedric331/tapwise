<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    InputOTP,
    InputOTPGroup,
    InputOTPSlot,
} from '@/components/ui/input-otp';
import { store } from '@/routes/two-factor/login';
import type { TwoFactorConfigContent } from '@/types';
import { QrCode, Shield } from 'lucide-vue-next';

const authConfigContent = computed<TwoFactorConfigContent>(() => {
    if (showRecoveryInput.value) {
        return {
            title: 'Code de récupération',
            description:
                'Veuillez confirmer l\'accès à votre compte en entrant l\'un de vos codes de récupération d\'urgence.',
            buttonText: 'se connecter avec un code d\'authentification',
        };
    }

    return {
        title: 'Code d\'authentification',
        description:
            'Entrez le code d\'authentification fourni par votre application d\'authentification.',
        buttonText: 'se connecter avec un code de récupération',
    };
});

const showRecoveryInput = ref<boolean>(false);

const toggleRecoveryMode = (clearErrors: () => void): void => {
    showRecoveryInput.value = !showRecoveryInput.value;
    clearErrors();
    code.value = '';
};

const code = ref<string>('');
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
                            <Shield class="h-8 w-8 text-amber-800" />
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ authConfigContent.title }}</h1>
                    <p class="mt-3 text-sm text-gray-600">
                        {{ authConfigContent.description }}
                    </p>
                </div>

                <Head title="Authentification à deux facteurs" />

                <!-- Formulaire -->
                <div class="rounded-2xl bg-white p-8 shadow-lg border border-gray-100">
                    <div class="space-y-6">
                        <template v-if="!showRecoveryInput">
                            <Form
                                v-bind="store.form()"
                                class="space-y-6"
                                reset-on-error
                                @error="code = ''"
                                #default="{ errors, processing, clearErrors }"
                            >
                                <input type="hidden" name="code" :value="code" />
                                <div
                                    class="flex flex-col items-center justify-center space-y-4 text-center"
                                >
                                    <div class="flex w-full items-center justify-center">
                                        <InputOTP
                                            id="otp"
                                            v-model="code"
                                            :maxlength="6"
                                            :disabled="processing"
                                            autofocus
                                        >
                                            <InputOTPGroup>
                                                <InputOTPSlot
                                                    v-for="index in 6"
                                                    :key="index"
                                                    :index="index - 1"
                                                />
                                            </InputOTPGroup>
                                        </InputOTP>
                                    </div>
                                    <InputError :message="errors.code" />
                                </div>
                                <Button 
                                    type="submit" 
                                    class="w-full h-11 bg-gradient-to-r from-amber-500 to-amber-800 hover:from-amber-600 hover:to-orange-700 text-white font-semibold shadow-lg transition-all hover:shadow-xl"
                                    :disabled="processing"
                                >
                                    <Spinner v-if="processing" />
                                    <span v-if="!processing">Continuer</span>
                                </Button>
                                <div class="pt-4 text-center text-sm text-gray-600 border-t border-gray-100">
                                    <span>Ou vous pouvez </span>
                                    <button
                                        type="button"
                                        class="text-amber-800 hover:text-amber-800 font-medium underline underline-offset-4"
                                        @click="() => toggleRecoveryMode(clearErrors)"
                                    >
                                        {{ authConfigContent.buttonText }}
                                    </button>
                                </div>
                            </Form>
                        </template>

                        <template v-else>
                            <Form
                                v-bind="store.form()"
                                class="space-y-6"
                                reset-on-error
                                #default="{ errors, processing, clearErrors }"
                            >
                                <div class="grid gap-2">
                                    <label for="recovery_code" class="text-sm font-medium text-gray-700">
                                        Code de récupération
                                    </label>
                                    <Input
                                        id="recovery_code"
                                        name="recovery_code"
                                        type="text"
                                        placeholder="Entrez le code de récupération"
                                        :autofocus="showRecoveryInput"
                                        required
                                        class="h-11"
                                    />
                                    <InputError :message="errors.recovery_code" />
                                </div>
                                <Button 
                                    type="submit" 
                                    class="w-full h-11 bg-gradient-to-r from-amber-500 to-amber-800 hover:from-amber-600 hover:to-orange-700 text-white font-semibold shadow-lg transition-all hover:shadow-xl"
                                    :disabled="processing"
                                >
                                    <Spinner v-if="processing" />
                                    <span v-if="!processing">Continuer</span>
                                </Button>

                                <div class="pt-4 text-center text-sm text-gray-600 border-t border-gray-100">
                                    <span>Ou vous pouvez </span>
                                    <button
                                        type="button"
                                        class="text-amber-800 hover:text-amber-800 font-medium underline underline-offset-4"
                                        @click="() => toggleRecoveryMode(clearErrors)"
                                    >
                                        {{ authConfigContent.buttonText }}
                                    </button>
                                </div>
                            </Form>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
