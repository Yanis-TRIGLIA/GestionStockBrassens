<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">Modifier l'utilisateur</h2>
        <FormulaireUser :user="user" :isEditMode="true" @submit="updateUser" />
    </div>
</template>

<script>
import FormulaireUser from "./FormulaireUser.vue";
import axios from "../../../../../axios.config.js";
import Toastify from "toastify-js";

export default {
    name: "EditUser",
    components: { FormulaireUser },
    data() {
        return {
            user: null, // Stocke les données de l'utilisateur à modifier
        };
    },
    methods: {
        fetchUser() {
            const userId = this.$route.params.id; // Récupérer l'ID de l'utilisateur à partir de l'URL
            axios
                .get(`/api/users/${userId}`)
                .then((response) => {
                    this.user = response.data;
                })
                .catch(() => {
                    this.showErrorToast("Erreur lors de la récupération des données de l'utilisateur.");
                });
        },
        updateUser(formData) {
            const userId = this.$route.params.id;

            axios
                .post(`/api/users/${userId}`, formData)
                .then(() => {
                    this.showSuccessToast("Utilisateur modifié avec succès !");
                    this.$router.push("/admin/utilisateurs"); // Rediriger vers la liste des utilisateurs
                })
                .catch(() => {
                    this.showErrorToast("Erreur lors de la modification de l'utilisateur.");
                });
        },
        showSuccessToast(message) {
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#4CAF50",
                stopOnFocus: true,
            }).showToast();
        },
        showErrorToast(message) {
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#F44336",
                stopOnFocus: true,
            }).showToast();
        },
    },
    mounted() {
        this.fetchUser(); // Récupérer les données de l'utilisateur lors du montage
    },
};
</script>

<style scoped>
/* Ajoutez vos styles ici */
</style>
