<script setup>
import { ref, nextTick } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import MyWalletLogo from '@/Components/MyWalletLogo.vue';

// Props
defineProps({
    title: String,
});

// Sidebar state
const sidebarOpen = ref(false);
const showProfileDropdown = ref(false);

// Dropdown close ao clicar fora
function onClickAwayProfile(e) {
    if (!e.target.closest('#profile-menu-btn') && !e.target.closest('#profile-dropdown')) {
        showProfileDropdown.value = false;
    }
}
if (typeof window !== "undefined") {
    window.addEventListener('click', onClickAwayProfile);
}

const switchToTeam = (team) => {
    router.put(route('current-team.update'), { team_id: team.id }, { preserveState: false });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>

        <Head :title="title" />
        <Banner />

        <!-- Mobile overlay -->
        <transition name="fade">
            <div v-if="sidebarOpen" class="fixed inset-0 z-30 bg-black bg-opacity-40 md:hidden"
                @click="sidebarOpen = false" />
        </transition>

        <div class="flex h-screen bg-gray-100 overflow-hidden">
            <!-- Sidebar -->
            <aside :class="[
                'flex flex-col w-72 max-w-full h-screen bg-white shadow-xl fixed md:static z-40 transition-all duration-300',
                sidebarOpen ? 'left-0' : '-left-80 md:left-0'
            ]">
                <!-- Top (Logo e título) -->
                <div class="flex items-center gap-4 px-6 py-5 border-b">
                    <MyWalletLogo/>
                    <!-- Fechar no mobile -->
                    <button class="ml-auto md:hidden" @click="sidebarOpen = false">
                        <svg class="h-7 w-7 text-gray-400 hover:text-gray-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <!-- Navegação principal -->
                <nav class="flex-1 overflow-y-auto px-4 py-6">
                    <ul class="space-y-2">
                        <li>
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="w-full">
                                <span class="flex items-center gap-3 py-2 px-3 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        :stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                    </svg>
                                    Dashboard
                                </span>
                            </NavLink>
                        </li>
                        <li>
                            <NavLink :href="route('lancamentos.index')" :active="route().current('lancamentos.index')"
                                class="w-full">
                                <span class="flex items-center gap-3 py-2 px-3 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                    </svg>

                                    
                                    Lançamentos
                                </span>
                            </NavLink>
                        </li>
                        <li>
                            <NavLink :href="route('categorias.index')" :active="route().current('categorias.index')" class="w-full">
                                <span class="flex items-center gap-3 py-2 px-3 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                                    </svg>

                                    Categorias
                                </span>
                            </NavLink>
                        </li>
                        <li>
                            <NavLink :href="route('metas.index')" :active="route().current('metas.index')" class="w-full">
                                <span class="flex items-center gap-3 py-2 px-3 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
                                    </svg>

                                    Metas
                                </span>
                            </NavLink>
                        </li>
                        <li>
                            <NavLink :href="route('orcamentos.index')" :active="route().current('orcamentos.index')" class="w-full">
                                <span class="flex items-center gap-3 py-2 px-3 rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    Orçamentos
                                </span>
                            </NavLink>
                        </li>


                        <!-- Adicione mais NavLinks aqui -->
                        <li v-if="$page.props.jetstream.hasTeamFeatures" class="pt-4 border-t">
                            <div class="text-xs text-gray-400 px-3 pb-2">Teams</div>
                            <NavLink :href="route('teams.show', $page.props.auth.user.current_team)">
                                <span class="flex items-center gap-3 py-2 px-3 rounded-xl">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"
                                            fill="none" />
                                    </svg>
                                    {{ $page.props.auth.user.current_team.name }}
                                </span>
                            </NavLink>
                            <NavLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">
                                <span class="flex items-center gap-3 py-2 px-3 rounded-xl">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Create New Team
                                </span>
                            </NavLink>
                        </li>
                    </ul>
                </nav>

                <!-- Rodapé: perfil do usuário e dropdown -->
                <div class="relative px-4 pt-2 pb-5 mt-auto">
                    <button id="profile-menu-btn" @click.stop="showProfileDropdown = !showProfileDropdown"
                        class="w-full flex items-center gap-3 py-2 px-3 rounded-xl text-gray-700 hover:bg-gray-100 transition font-medium focus:outline-none">
                        <img class="h-9 w-9 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
                            :alt="$page.props.auth.user.name" />
                        <span class="truncate">{{ $page.props.auth.user.name }}</span>
                        <svg class="ml-auto h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path 
                                stroke-linecap="round" 
                                stroke-linejoin="round" 
                                stroke-width="2"
                                :d="showProfileDropdown 
                                    ? 'M19 9l-7 7-7-7'  // seta para baixo
                                    : 'M19 15l-7-7-7 7'" 
                            />
                        </svg>
                    </button>
                    <!-- Dropdown abrindo para cima -->
                    <transition name="fade-up">
                        <div v-if="showProfileDropdown" id="profile-dropdown"
                            class="absolute left-0 right-0 bottom-full mb-2 z-50 rounded-xl bg-gray-100 shadow-lg py-2">
                            <DropdownLink :href="route('profile.show')">Perfil do Usuário</DropdownLink>
                            <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                                API Tokens</DropdownLink>
                            <div class="border-t mx-2 my-2"></div>
                            <form @submit.prevent="logout">
                                <DropdownLink as="button" class="w-full text-left">Log Out</DropdownLink>
                            </form>
                        </div>
                    </transition>
                </div>
            </aside>

            <!-- Conteúdo principal -->
            <div class="flex-1 flex flex-col min-w-0 h-screen">
                <!-- Header mobile -->
                <header class="flex items-center h-16 px-4 bg-white shadow md:hidden">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <span class="ml-4 font-semibold text-lg text-gray-700">{{ title }}</span>
                </header>
                <!-- Desktop Page Heading -->
                <header v-if="$slots.header" class="hidden md:block">
  <div class="max-w-7xl mx-auto py-5 px-6">
    <div class="flex items-center justify-between">
      <slot name="header" />
      <slot name="header-actions" />
    </div>
  </div>
</header>
                <main class="flex-1 p-4 md:p-8 overflow-auto">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity .2s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.fade-up-enter-active,
.fade-up-leave-active {
    transition: opacity .2s, transform .2s;
}

.fade-up-enter-from,
.fade-up-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
</style>
