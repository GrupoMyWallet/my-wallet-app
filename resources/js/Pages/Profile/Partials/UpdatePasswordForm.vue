<script setup>
import { ref } from 'vue';
import { computed } from 'vue'; // Adicione este import
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline'; // Adicione este import
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});




// ADICIONE ESTE BLOCO DE CÓDIGO AQUI
const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showPasswordConfirmation = ref(false);

const currentPasswordFieldType = computed(() =>
    showCurrentPassword.value ? 'text' : 'password'
);

const newPasswordFieldType = computed(() =>
    showNewPassword.value ? 'text' : 'password'
);

const passwordConfirmationFieldType = computed(() =>
    showPasswordConfirmation.value ? 'text' : 'password'
);

const toggleCurrentPasswordVisibility = () => {
    showCurrentPassword.value = !showCurrentPassword.value;
};

const toggleNewPasswordVisibility = () => {
    showNewPassword.value = !showNewPassword.value;
};

const togglePasswordConfirmationVisibility = () => {
    showPasswordConfirmation.value = !showPasswordConfirmation.value;
};
// FIM DO BLOCO A SER ADICIONADO















const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <FormSection @submitted="updatePassword">
        <template #title>
            Mudar Senha
        </template>

        <template #description>
            Proteja sua conta com uma senha longa e aleatória para ter mais segurança.
        </template>

        <template #form>
           <div class="col-span-6 sm:col-span-4">
    <InputLabel for="current_password" value="Senha atual" />
    <div class="relative">
        <TextInput
            id="current_password"
            ref="currentPasswordInput"
            v-model="form.current_password"
            :type="currentPasswordFieldType"
            class="mt-1 block w-full pr-10"
            autocomplete="current-password"
        />
        <InputError :message="form.errors.current_password" class="mt-2" />
        <button
            type="button"
            @click="toggleCurrentPasswordVisibility"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
        >
            <EyeSlashIcon v-if="currentPasswordFieldType === 'password'" class="w-5 h-5" />
            <EyeIcon v-else class="w-5 h-5" />
        </button>
    </div>
</div>
            <div class="col-span-6 sm:col-span-4">
    <InputLabel for="password" value="Nova Senha" />
    <div class="relative">
        <TextInput
            id="password"
            ref="passwordInput"
            v-model="form.password"
            :type="newPasswordFieldType"
            class="mt-1 block w-full pr-10"
            autocomplete="new-password"
        />
        <InputError :message="form.errors.password" class="mt-2" />
        <button
            type="button"
            @click="toggleNewPasswordVisibility"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
        >
            <EyeSlashIcon v-if="newPasswordFieldType === 'password'" class="w-5 h-5" />
            <EyeIcon v-else class="w-5 h-5" />
        </button>
    </div>
</div>
         <div class="col-span-6 sm:col-span-4">
    <InputLabel for="password_confirmation" value="Confirmar Senha" />
    <div class="relative">
        <TextInput
            id="password_confirmation"
            v-model="form.password_confirmation"
            :type="passwordConfirmationFieldType"
            class="mt-1 block w-full pr-10"
            autocomplete="new-password"
        />
        <InputError :message="form.errors.password_confirmation" class="mt-2" />
        <button
            type="button"
            @click="togglePasswordConfirmationVisibility"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
        >
            <EyeSlashIcon v-if="passwordConfirmationFieldType === 'password'" class="w-5 h-5" />
            <EyeIcon v-else class="w-5 h-5" />
        </button>
    </div>
</div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Salva.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Salvar
            </PrimaryButton>
        </template>
    </FormSection>
</template>
