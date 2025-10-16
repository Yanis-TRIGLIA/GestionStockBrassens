<template>
    <div class="relative h-screen bg-cover bg-center bg-fixed ">
        <!-- Image de fond avec effet de flou -->
        <div class="absolute inset-0 bg-image-blur"></div>

        <!-- Contenu au premier plan -->
        <div class="relative flex justify-center items-center h-full">
            <div class="bg-white mx-4 p-8 rounded shadow-md w-full md:w-1/2 lg:w-1/3">
                <h1 class="text-3xl font-bold mb-8 text-center">Connexion</h1>
                <form @submit.prevent="login">
                    <div class="mb-4">
                        <label class="block font-semibold text-gray-700 mb-2" for="email">
                            Email
                        </label>
                        <input
                            v-model="email"
                            class="border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="email"
                            type="email"
                            placeholder="Entrée votre email"
                        />
                    </div>
                    <div class="mb-4">
                        <label class="block font-semibold text-gray-700 mb-2" for="password">
                            Mot de passe
                        </label>
                        <input
                            v-model="password"
                            class="border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline"
                            id="password"
                            type="password"
                            placeholder="Entrée votre mot de passe"
                        />
                    </div>
                    <div class="mb-6">
                        <button
                            class="bg-blue-500 justify-center flex w-full hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            type="submit"
                        >
                            Connexion
                        </button>
                    </div>
                    <div v-if="errorMessage" class="text-red-500 text-sm">
                        {{ errorMessage }}
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Login",
    data() {
        return {
            email: "",
            password: "",
            errorMessage: "",
        };
    },
    mounted() {
        // Vérifier si un token est présent lors du montage du composant
        const token = this.getToken();
        if (token) {
            // Si un token est trouvé, rediriger directement vers la page d'accueil
            this.$router.push('/');
        }
    },
    methods: {
        // Fonction pour gérer le stockage avec fallback
        setToken(token) {
            try {
                localStorage.setItem("auth_token", token);
                localStorage.setItem(
                    "auth_token_expiration",
                    Date.now() + 7 * 24 * 60 * 60 * 1000 // 7 jours
                );
                return true;
            } catch (e) {
                // Fallback si localStorage n'est pas disponible (iPhone en navigation privée)
                console.warn("localStorage non disponible, utilisation de sessionStorage");
                try {
                    sessionStorage.setItem("auth_token", token);
                    sessionStorage.setItem(
                        "auth_token_expiration",
                        Date.now() + 7 * 24 * 60 * 60 * 1000
                    );
                    return true;
                } catch (e2) {
                    // Dernière option : cookie
                    console.warn("sessionStorage non disponible, utilisation de cookies");
                    document.cookie = `auth_token=${token}; max-age=${7 * 24 * 60 * 60}; path=/; SameSite=Strict`;
                    return true;
                }
            }
        },

        // Fonction pour récupérer le token avec fallback
        getToken() {
            try {
                return localStorage.getItem("auth_token");
            } catch (e) {
                try {
                    return sessionStorage.getItem("auth_token");
                } catch (e2) {
                    // Lire depuis les cookies
                    const match = document.cookie.match(/auth_token=([^;]+)/);
                    return match ? match[1] : null;
                }
            }
        },

        async login() {
            try {
                // Envoi des informations de connexion à l'API
                const response = await axios.post("/api/login", {
                    email: this.email,
                    password: this.password,
                });

                // Stockage du token avec fallback
                const token = response.data.access_token;
                this.setToken(token);

                // Configuration du header axios pour les futures requêtes
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

                // Redirection vers la page d'accueil avec rechargement complet
                window.location.href = '/';

            } catch (error) {
                // Gérer les erreurs de connexion
                this.errorMessage =
                    error.response?.data?.message || "Erreur lors de la connexion";
            }
        },
    },
};
</script>

<style scoped>
.bg-image-blur {
    background-image: url('https://ds.static.rtbf.be/article/image/370x208/5/d/0/b98a3773ecf715751d3cf0fb6dcba424-1634905771.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    filter: blur(8px);
    z-index: -1;
}

.relative {
    position: relative;
    z-index: 1;
}
</style>