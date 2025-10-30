<script setup>
import { ref, computed } from 'vue'; 
import { Head, Link, useForm } from '@inertiajs/vue3';
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
//import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import MyWalletLogo from '@/Components/MyWalletLogo.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};


// ...código da função submit...
const showPassword = ref(false);

const passwordFieldType = computed(() =>
    showPassword.value ? 'text' : 'password'
);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

</script>

<template>
    <Head title="Log in" />

    <AuthenticationCard>
        <template #logo>
            <Link :href="'/'"><MyWalletLogo /></Link>
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
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
                        autocomplete="current-password"
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

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-slate-400">Continuar Conectado</span>
                </label>
            </div>
            
            <div class="flex items-center justify-end mt-4">
                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-800">
                    Esqueceu a senha?
                </Link>

            <div class="flex items-center">
                <Link :href="route('register')" 
                    class="underline text-sm text-gray-600 dark:text-slate-400 hover:text-gray-900 dark:hover:text-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-slate-800 ms-4">
                    Não tem conta? Crie uma
                </Link>

            </div>
            
                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            </div>

        </form>
    </AuthenticationCard>
</template>