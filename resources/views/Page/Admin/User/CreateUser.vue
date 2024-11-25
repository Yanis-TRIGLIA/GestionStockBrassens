<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">Créer un nouvel utilisateur</h2>
        <FormulaireUser @submit="createUser" />
    </div>
</template>

<script>
import FormulaireUser from "./FormulaireUser.vue";
import axios from "../../../../../axios.config.js";
import Toastify from "toastify-js";

export default {
    name: "CreateUser",
    components: { FormulaireUser },
    methods: {
        createUser(formData) {
            const dataToSend = {
                name: formData.get("name"),
                email: formData.get("email"),
                password: formData.get("password"),
                is_admin: JSON.parse(formData.get("is_admin"))

            };

            axios
                .post("/api/register", dataToSend)
                .then(() => {
                    this.showSuccessToast("Utilisateur créé avec succès !");
                    this.$router.push("/admin/utilisateurs");
                })
                .catch((error) => {
                    // Gestion des erreurs de validation côté serveur
                    const errorMessages = error.response.data.errors;
                    if (errorMessages) {
                        this.showErrorToast(
                            `Erreur : ${Object.values(errorMessages).join(", ")}`
                        );
                    } else {
                        this.showErrorToast("Erreur lors de la création de l'utilisateur.");
                    }
                });
        },

        // Fonction pour afficher les notifications de succès
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

        // Fonction pour afficher les notifications d'erreur
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
};
</script>

<style scoped>
/* Ajoutez vos styles ici */
</style>
