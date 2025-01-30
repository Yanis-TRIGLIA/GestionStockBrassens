<template>
    <div class="flex h-screen">
        <!-- Sidebar avec animation -->
        <nav
            :class="{
                'translate-x-0': isSidebarOpen,
                '-translate-x-full': !isSidebarOpen
            }"
            class="fixed md:relative md:translate-x-0 w-64 bg-gray-800 text-white p-4 h-full flex flex-col transition-transform duration-300 ease-in-out z-50"
        >
            <!-- Titre -->
            <h1 class="text-xl font-bold mb-8 flex items-center space-x-2">
                <span>👨🏻‍💻</span>
                <span>Espace Administrateur</span>
            </h1>

            <!-- Liens du menu -->
            <ul class="space-y-4 flex-grow">
                <li v-for="(item, index) in menuItems" :key="index">
                    <router-link
                        :to="item.link"
                        class="flex items-center px-4 py-2 rounded hover:bg-gray-700 transition-colors"
                        @click="closeSidebarOnMobile"
                    >
                        <span class="text-lg mr-3">{{ item.icon }}</span>
                        <span>{{ item.name }}</span>
                    </router-link>
                </li>
            </ul>

            <!-- Bouton Déconnexion -->
            <div class="mt-auto">
                <router-link
                    to="/logout"
                    class="flex items-center px-4 py-2 text-red-500 hover:bg-gray-700 hover:text-white transition-colors"
                    @click="closeSidebarOnMobile"
                >
                    <span class="text-lg mr-3">🚪</span>
                    <span>Déconnexion</span>
                </router-link>
            </div>
        </nav>

        <!-- Overlay (fond noir semi-transparent en mobile) -->
        <div
            v-if="isSidebarOpen"
            class="fixed inset-0 bg-black bg-opacity-50 md:hidden z-40"
            @click="toggleSidebar"
        ></div>

        <!-- Flèche pour ouvrir le menu (s'affiche seulement si le menu est fermé) -->
        <button
            v-if="!isSidebarOpen"
            @click="toggleSidebar"
            class="fixed left-0 top-1/2 transform -translate-y-1/2 bg-gray-800 text-white p-2 rounded-r-lg z-50 md:hidden"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
        </button>

        <!-- Main Content -->
        <main class="flex-1 p-8 bg-gray-100 h-full overflow-y-auto">
            <router-view />
        </main>
    </div>
</template>

<script>
export default {
    name: "AdminLayout",
    data() {
        return {
            isSidebarOpen: false, // Gestion du menu mobile
            menuItems: [
                { name: "Produits", link: "/admin/produits", icon: "📦" },
                { name: "Zones", link: "/admin/zones", icon: "🗺️" },
                { name: "Agents", link: "/admin/agent", icon: "👮‍♂️" },
                { name: "Utilisateurs", link: "/admin/utilisateurs", icon: "👥" },
                { name: "Catégories", link: "/admin/categories", icon: "📂" },
            ]
        };
    },
    methods: {
        toggleSidebar() {
            this.isSidebarOpen = !this.isSidebarOpen;
        },
        closeSidebarOnMobile() {
            if (window.innerWidth < 768) {
                this.isSidebarOpen = false;
            }
        }
    }
};
</script>

<style scoped>
/* Empêche la sidebar de prendre tout l'écran sur mobile */
@media (max-width: 767px) {
    nav {
        width: 16rem; /* 64px * 4 = 256px */
    }
}
</style>
