<script>
import axios from 'axios';

export default {
    name: "Header",
    data() {
        return {
            isLoggedIn: false,
            user: null,
            panierCount: 0, // Nombre de produits dans le panier
            isMobileMenuOpen: false, // État du menu mobile
        };
    },
    mounted() {
        const token = localStorage.getItem('auth_token');

        if (token) {
            this.isLoggedIn = true;
            this.fetchUserData(token);
            this.fetchPanierCount();
        }
    },
    methods: {
        async fetchUserData(token) {
            try {
                const response = await axios.get('/api/user', {
                    headers: { Authorization: `Bearer ${token}` },
                });
                this.user = response.data;
            } catch (error) {
                console.error('Erreur récupération utilisateur:', error);
                this.isLoggedIn = false;
                localStorage.removeItem('auth_token');
            }
        },
        async fetchPanierCount() {
            const token = localStorage.getItem("auth_token");
            try {
                axios.get("/api/panier", {
                    headers: { Authorization: `Bearer ${token}` }
                })
                .then(response => {
                    this.panierCount = response.data.produits.length;
                });
            } catch (error) {
                console.error('Erreur récupération panier:', error);
            }
        },
        logout() {
            localStorage.removeItem('auth_token');
            this.isLoggedIn = false;
            this.user = null;
            this.$router.push('/connexion');
        },
        toggleMobileMenu() {
            this.isMobileMenuOpen = !this.isMobileMenuOpen;
        }
    }
};
</script>

<template>
    <nav class="bg-white sticky top-0 z-50 p-4 border-b shadow-lg">
        
        <div class="flex justify-between items-center w-full mx-auto">
            <!-- Logo -->
            <a href="/" class="flex items-center">
                <img src="../../image/home/logo.png" class="h-10" alt="Logo" />
            </a>

            <!-- Menu principal (Desktop) -->
            <div class="hidden md:flex md:items-center md:space-x-6">
                <a href="/liste_produit" class="hover:text-blue-600 transition">Les produits</a>
                <a href="/sortie_produit" class="hover:text-blue-600 transition">Tableau de sortie</a>
                <a href="/tableau_produit" class="hover:text-blue-600 transition">Tableau de produit</a>

                <!-- Connexion / Déconnexion -->
                <button v-if="!isLoggedIn" @click="$router.push('/connexion')"
                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">Connexion</button>
                    <a v-if="isLoggedIn && user.is_admin" href="/admin" class="hover:text-blue-600 transition">Espace Admin</a>
                <div v-if="isLoggedIn" class="flex items-center space-x-4">
                    

                    <!-- Icône Panier -->
                    <a href="/panier" class="relative flex items-center">
                        <svg class="w-6 h-6 text-gray-700 hover:text-blue-600 transition" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 3h2l.4 2m4.2 10h8.8M7 13h10l4-8H5.4M9 17a1 1 0 11-2 0 1 1 0 012 0zm8 0a1 1 0 11-2 0 1 1 0 012 0z" />
                        </svg>
                        <span v-if="panierCount > 0"
                            class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full px-2 py-1">
                            {{ panierCount }}
                        </span>
                    </a>
                    <a href="/page_compte" class="font-medium">👋 Bonjour, {{ user?.name || 'Utilisateur' }}</a>

                    <button @click="logout"
                        class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">Déconnexion</button>
                </div>
            </div>

            <!-- Bouton menu mobile -->
            <button @click="toggleMobileMenu" class="md:hidden text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Overlay et Menu Mobile -->
        <div v-if="isMobileMenuOpen" class="fixed inset-0 bg-black bg-opacity-50 z-40" @click="toggleMobileMenu"></div>
        <div :class="{'translate-x-0': isMobileMenuOpen, 'translate-x-full': !isMobileMenuOpen}" 
            class="fixed top-0 right-0 w-64 h-full bg-white shadow-lg p-6 transform transition-transform duration-300 ease-in-out z-50">
            <!-- Bouton Fermer -->
            <button @click="toggleMobileMenu" class="absolute top-4 right-4 text-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="flex flex-col space-y-4 mt-10">
                <a href="/liste_produit" class="hover:text-blue-600 transition">Les produits</a>
                <a href="/sortie_produit" class="hover:text-blue-600 transition">Tableau de sortie</a>
                <a href="/tableau_produit" class="hover:text-blue-600 transition">Tableau de produit</a>

                <!-- Panier -->
                <a href="/panier" class="flex items-center space-x-2 hover:text-blue-600 transition">
                    <span>Panier</span>
                    <span v-if="panierCount > 0" class="bg-red-500 text-white text-xs font-bold rounded-full px-2 py-1">
                        {{ panierCount }}
                    </span>
                </a>

                <!-- Connexion / Déconnexion -->
                <button v-if="!isLoggedIn" @click="$router.push('/connexion')"
                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition w-full">Connexion</button>
                <div v-if="isLoggedIn" class="space-y-4">
                    <a href="/admin" class="hover:text-blue-600 transition">Espace Admin</a>
                    <div>
                    <a href="/page_compte" class="font-medium">👋 Bonjour, {{ user?.name || 'Utilisateur' }}</a></div>
                    <button @click="logout"
                        class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition w-full">Déconnexion</button>
                </div>
            </div>
        </div>
    </nav>
</template>


<style scoped>
#mobile-menu {
    display: none;
}

#mobile-menu-button.active+#mobile-menu {
    display: block;
}
</style>
