<script setup>
import { Link } from '@inertiajs/vue3';
import MyWalletLogo from '@/Components/MyWalletLogo.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <div class="relative min-h-screen flex flex-col bg-gradient-to-br from-[#FDF6F0] via-white to-[#E7ECFF] dark:from-[#181F2A] dark:via-[#181D27] dark:to-[#202237]">
        <!-- HEADER -->
        <header class="absolute top-0 left-0 w-full z-20 px-4 py-6 sm:px-8">
            
                <div class="flex justify-center lg:col-start-2">
                    <MyWalletLogo color="text-black dark:text-white"/>
                </div>
            
        </header>

        <!-- HERO SECTION -->
        <section class="flex-1 flex items-center justify-center text-center px-4">
            <div class="w-full max-w-2xl mt-32 sm:mt-40">
                <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight animate-fadein">
                    Organize suas <span class="text-[#FF2D20]">finanças</span> de forma simples e poderosa
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-gray-600 dark:text-gray-300 font-medium animate-fadein delay-100">
                    Controle seu orçamento, acompanhe despesas, alcance seus objetivos.  
                    Simples, intuitivo, para você dominar sua vida financeira!
                </p>
                <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center animate-fadein delay-200">
                    <Link
                        v-if="!$page.props.auth.user && canRegister"
                        :href="route('register')"
                        class="inline-block px-8 py-3 rounded-xl bg-[#FF2D20] text-white font-bold text-lg shadow-lg shadow-[#FF2D2040]/10 hover:bg-[#d5251a] transition"
                    >
                        Criar Conta
                    </Link>
                    <Link
                        :href="route($page.props.auth.user ? 'dashboard' : 'login')"
                        class="inline-block px-8 py-3 rounded-xl bg-white/70 text-[#FF2D20] border-2 border-[#FF2D20] font-bold text-lg shadow hover:bg-[#FF2D20] hover:text-white hover:border-[#FF2D20] transition dark:bg-white/10 dark:text-white dark:border-white/20 dark:hover:bg-[#FF2D20] dark:hover:text-white"
                    >
                        {{ $page.props.auth.user ? 'Ir para o Dashboard' : 'Entrar' }}
                    </Link>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="w-full text-center py-6 text-sm text-gray-500 dark:text-gray-400">
            © {{ new Date().getFullYear() }} MyWallet. Todos os direitos reservados.
        </footer>
    </div>
</template>

<style scoped>
@keyframes fadein {
  0% { opacity: 0; transform: translateY(24px);}
  100% { opacity: 1; transform: none;}
}
.animate-fadein { animation: fadein 1s cubic-bezier(.6,.05,.1,1) both;}
.animate-fadein.delay-100 { animation-delay: .1s; }
.animate-fadein.delay-200 { animation-delay: .2s; }
</style>
