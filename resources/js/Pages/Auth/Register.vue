<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue'; 
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import MyWalletLogo from '@/Components/MyWalletLogo.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};



// ADICIONE ESTE BLOCO DE CÓDIGO AQUI
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const passwordFieldType = computed(() =>
    showPassword.value ? 'text' : 'password'
);

const passwordConfirmationFieldType = computed(() =>
    showPasswordConfirmation.value ? 'text' : 'password'
);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const togglePasswordConfirmationVisibility = () => {
    showPasswordConfirmation.value = !showPasswordConfirmation.value;
};













</script>

<template>
    <Head title="Register" />

    <AuthenticationCard>
        <template #logo>
            <Link href="/"><MyWalletLogo /></Link>
        </template>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nome" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
    <InputLabel for="password" value="Senha" />
    <div class="relative">
        <TextInput
            id="password"
            v-model="form.password"
            :type="passwordFieldType"
            class="mt-1 block w-full pr-10"
            required
            autocomplete="new-password"
        />
        <InputError class="mt-2" :message="form.errors.password" />
        <button
            type="button"
            @click="togglePasswordVisibility"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-400 hover:text-gray-600 dark:hover:text-slate-300"
        >
            <EyeSlashIcon v-if="passwordFieldType === 'password'" class="w-5 h-5" />
            <EyeIcon v-else class="w-5 h-5" />
        </button>
    </div>
</div>
            <div class="mt-4">
    <InputLabel for="password_confirmation" value="Confirmar Senha" />
    <div class="relative">
        <TextInput
            id="password_confirmation"
            v-model="form.password_confirmation"
            :type="passwordConfirmationFieldType"
            class="mt-1 block w-full pr-10"
            required
            autocomplete="new-password"
        />
        <InputError class="mt-2" :message="form.errors.password_confirmation" />
        <button
            type="button"
            @click="togglePasswordConfirmationVisibility"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-slate-400 hover:text-gray-600 dark:hover:text-slate-300"
        >
            <EyeSlashIcon v-if="passwordConfirmationFieldType === 'password'" class="w-5 h-5" />
            <EyeIcon v-else class="w-5 h-5" />
        </button>
    </div>
</div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <InputLabel for="terms">
                    <div class="flex items-center">
                        <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                        <div class="ms-2">
                            I agree to the <a target="_blank" :href="route('terms.show')" class="underline text-sm text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-800">Terms of Service</a> and <a target="_blank" :href="route('policy.show')" class="underline text-sm text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-800">Privacy Policy</a>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.terms" />
                </InputLabel>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-800">
                    Já possui uma conta?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Registrar
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>