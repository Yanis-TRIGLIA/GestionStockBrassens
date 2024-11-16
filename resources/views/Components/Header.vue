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
    <nav class="bg-white  sticky p-2 border-b border-gray-200  ">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img src="../../image/home/logo.png" class="h-8" alt="Logo" />
            </a>

            <!-- Menu principal -->
            <div class="hidden w-full md:block md:w-auto" id="navbar-default">
                <ul class="items-center font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-white dark:border-gray-700">
                    <li>
                        <a href="/liste_produit" class="block py-2 px-3 text-black bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:p-0 dark:text-black md:dark:text-blue-500" aria-current="page">
                            Les produits
                        </a>
                    </li>
                    <li>
                        <a href="#" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-black md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-black md:dark:hover:bg-transparent">
                            Tableau de sortie
                        </a>
                    </li>
                    <li>
                        <a href="#" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-black md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-black md:dark:hover:bg-transparent">
                            Services
                        </a>
                    </li>

                    <!-- Boutons ou nom de l'utilisateur -->
                    <li v-if="!isLoggedIn">
                        <button
                            type="button"
                            @click="$router.push('/connexion')"
                            class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl   font-medium rounded-lg text-sm px-5 py-2 text-center me-2 mb-2">
                            Connexion
                        </button>
                        <button
                            type="button"
                            class="text-white bg-gradient-to-r from-cyan-500 to-blue-500 hover:bg-gradient-to-bl    font-medium rounded-lg text-sm px-5 py-2 text-center me-2 mb-2">
                            Inscription
                        </button>
                    </li>

                    <!-- Afficher le nom de l'utilisateur connecté -->
                    <li v-if="isLoggedIn" class="flex items-center space-x-4">
                        <span class="text-gray-900 font-medium">
                            Bonjour, {{ user?.name || 'Utilisateur' }}
                        </span>
                        <button
                            @click="logout"
                            type="button"
                            class="text-white bg-red-500 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm px-5 py-2 text-center me-2 mb-2">
                            Déconnexion
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<style scoped>
</style>
