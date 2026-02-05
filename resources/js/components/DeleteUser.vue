<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const passwordInput = useTemplateRef('passwordInput');
const deleteForm = {
    action: '/settings/profile?_method=DELETE',
    method: 'post',
};
</script>

<template>
    <div class="space-y-6">
        <Heading
            variant="small"
            title="Supprimer le compte"
            description="Supprimer votre compte et toutes ses ressources"
        />
        <div
            class="space-y-4 rounded-3xl border border-red-100 bg-red-50 p-6"
        >
            <div class="relative space-y-0.5 text-red-700">
                <p class="font-semibold">Attention</p>
                <p class="text-sm">
                    Veuillez procéder avec prudence, cela ne peut pas être annulé.
                </p>
            </div>
            <Dialog>
                <DialogTrigger as-child>
                    <Button variant="destructive" data-test="delete-user-button"
                        >Supprimer le compte</Button
                    >
                </DialogTrigger>
                <DialogContent>
                    <Form
                        v-bind="deleteForm"
                        reset-on-success
                        @error="() => passwordInput?.$el?.focus()"
                        :options="{
                            preserveScroll: true,
                        }"
                        class="space-y-6"
                        v-slot="{ errors, processing, reset, clearErrors }"
                    >
                        <DialogHeader class="space-y-3">
                            <DialogTitle
                                >Êtes-vous sûr de vouloir supprimer votre
                                compte?</DialogTitle
                            >
                            <DialogDescription>
                                Une fois votre compte supprimé, tous ses
                                ressources et données seront également
                                supprimés. Veuillez entrer votre mot de passe pour confirmer que vous souhaitez supprimer votre compte.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="password" class="sr-only"
                                >Mot de passe</Label
                            >
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                ref="passwordInput"
                                placeholder="••••••••"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <DialogFooter class="gap-2">
                            <DialogClose as-child>
                                <Button
                                    variant="secondary"
                                    @click="
                                        () => {
                                            clearErrors();
                                            reset();
                                        }
                                    "
                                >
                                    Annuler
                                </Button>
                            </DialogClose>

                            <Button
                                type="submit"
                                variant="destructive"
                                :disabled="processing"
                                data-test="confirm-delete-user-button"
                            >
                                Supprimer le compte
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template>
