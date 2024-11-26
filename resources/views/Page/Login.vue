<template>
    <div class="relative h-screen bg-cover bg-center bg-fixed">
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
        // Vérifier si un token est présent dans le localStorage lors du montage du composant
        const token = localStorage.getItem("auth_token");
        if (token) {
            // Si un token est trouvé, rediriger directement vers la page d'accueil
            this.$router.push('/');
        }
    },
    methods: {
        async login() {
            try {
                // Envoi des informations de connexion à l'API
                const response = await axios.post("/api/login", {
                    email: this.email,
                    password: this.password,
                });

                // Stockage du token dans le localStorage
                const token = response.data.access_token;
                localStorage.setItem("auth_token", token);
                localStorage.setItem(
                    "auth_token_expiration",
                    Date.now() + 7 * 24 * 60 * 60 * 1000 // 7 jours
                );

                // Rafraîchissement de la page pour mettre à jour le header
                window.location.reload();

                // Redirection vers la page d'accueil
                this.$router.push("/");

            } catch (error) {
                // Gérer les erreurs de connexion
                this.errorMessage =
                    error.response.data.message || "Erreur lors de la connexion";
            }
        },
    },
};
</script>

<style scoped>
.bg-image-blur {
    background-image: url('https://ds.static.rtbf.be/article/image/370x208/5/d/0/b98a3773ecf715751d3cf0fb6dcba424-1634905771.jpg'); /* Remplacez par le chemin de votre image locale */
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
