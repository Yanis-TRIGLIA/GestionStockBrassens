<script>
import axios from 'axios';

export default {
    name: "Header",
    data() {
        return {
            isLoggedIn: false,
            user: null,
        };
    },
    mounted() {
        const token = localStorage.getItem('auth_token');

        if (token) {
            this.isLoggedIn = true;
            this.fetchUserData(token);
        }
    },
    methods: {
        async fetchUserData(token) {
            try {
                const response = await axios.get('/api/user', {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                this.user = response.data;
            } catch (error) {
                console.error('Erreur lors de la récupération des données utilisateur:', error);
                this.isLoggedIn = false;
                localStorage.removeItem('auth_token');
            }
        },
        logout() {
            localStorage.removeItem('auth_token');
            this.isLoggedIn = false;
            this.user = null;
            this.$router.push('/connexion');
        },
    },
};
</script>

<template>
    <nav class="bg-white sticky top-0 z-50 p-2 border-b border-gray-200 shadow-lg">
        <div class="w-full  flex flex-wrap items-center justify-between mx-auto p-4">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-10 rtl:space-x-reverse justify-start">
                <img src="../../image/home/logo.png" class="h-8 md:h-10" alt="Logo" />
            </a>

            <!-- Menu principal -->
            <div class="hidden w-full md:flex md:w-auto justify-end" id="navbar-default">
                <ul class="items-center font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-white dark:border-gray-700">
                    <li>
                        <a 
                            :class="{'text-blue-700': $route.path === '/liste_produit', 'text-black': $route.path !== '/liste_produit'}"
                            href="/liste_produit" 
                            class="block py-2 px-3 rounded md:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-black  transition duration-300 hover:bg-blue-100 md:hover:bg-transparent">
                            Les produits
                        </a>
                    </li>
                    <li>
                        <a 
                            :class="{'text-blue-700': $route.path === '/sortie_produit', 'text-gray-900': $route.path !== '/sortie_produit'}"
                            href="/sortie_produit" 
                            class="block py-2 px-3 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-black md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-black md:dark:hover:bg-transparent">
                            Tableau de sortie
                        </a>
                    </li>
                    <li>
                        <a 
                            :class="{'text-blue-700': $route.path === '/tableau_produit', 'text-gray-900': $route.path !== '/tableau_produit'}"
                            href="/tableau_produit" 
                            class="block py-2 px-3 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-black md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-black md:dark:hover:bg-transparent">
                            Tableau de produit
                        </a>
                    </li>

                    <!-- Boutons ou nom de l'utilisateur -->
                    <li v-if="!isLoggedIn">
                        <button
                            type="button"
                            @click="$router.push('/connexion')"
                            class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl font-medium rounded-lg text-sm px-5 py-2 text-center me-2 mb-2 transition duration-300 transform hover:scale-105">
                            Connexion
                        </button>
                    </li>

                    <!-- Afficher le nom de l'utilisateur connecté -->
                    <li v-if="isLoggedIn" class="flex items-center space-x-4">
                        <a href="/admin" class=" md:hover:text-blue-700 transition duration-300">Espace Administrateur</a>
                        <a href="/page_compte" class="text-gray-900 font-medium md:hover:text-blue-700">
                            👋 Bonjour, {{ user?.name || 'Utilisateur' }}
                        </a>

                        <button
                            @click="logout"
                            type="button"
                            class="text-white bg-red-500 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm px-5 py-2 text-center me-2 mb-2 transition duration-300 transform hover:scale-105">
                            Déconnexion
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Menu mobile toggle -->
            <button id="mobile-menu-button" class="block md:hidden text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Menu mobile -->
        <div class="md:hidden hidden" id="mobile-menu">
            <ul class="space-y-4 p-4">
                <li>
                    <a 
                        :class="{'text-blue-700': $route.path === '/liste_produit', 'text-black': $route.path !== '/liste_produit'}"
                        href="/liste_produit" 
                        class="block py-2 px-3 rounded md:bg-transparent md:text-blue-700 md:p-0 dark:text-black ">
                        Les produits
                    </a>
                </li>
                <li>
                    <a 
                        :class="{'text-blue-700': $route.path === '/sortie_produit', 'text-gray-900': $route.path !== '/sortie_produit'}"
                        href="/sortie_produit" 
                        class="block py-2 px-3 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-black md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-black">
                        Tableau de sortie
                    </a>
                </li>
                <li>
                    <a 
                        :class="{'text-blue-700': $route.path === '/tableau_produit', 'text-gray-900': $route.path !== '/tableau_produit'}"
                        href="/tableau_produit" 
                        class="block py-2 px-3 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-black md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-black">
                        Tableau de produit
                    </a>
                </li>
                <li v-if="!isLoggedIn">
                    <button
                        type="button"
                        @click="$router.push('/connexion')"
                        class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl font-medium rounded-lg text-sm px-5 py-2 text-center me-2 mb-2">
                        Connexion
                    </button>
                </li>
                <li v-if="isLoggedIn" class="flex flex-col items-center space-y-4">
                    <a href="/admin" class="text-blue-600 hover:text-blue-800 transition duration-300">Espace Administrateur</a>
                    <span class="text-gray-900 font-medium">
                        👋 Bonjour, {{ user?.name || 'Utilisateur' }}
                    </span>
                    <button
                        @click="logout"
                        type="button"
                        class="text-white bg-red-500 hover:bg-red-600 font-medium rounded-lg text-sm px-5 py-2 text-center me-2 mb-2">
                        Déconnexion
                    </button>
                </li>
            </ul>
        </div>
    </nav>
</template>

<style scoped>
#mobile-menu {
    display: none;
}

#mobile-menu-button.active + #mobile-menu {
    display: block;
}
</style>
