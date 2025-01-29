<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">🧑 Liste des utilisateurs</h2>
        <button @click="$router.push('/admin/utilisateur/create')"
            class="mb-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Créer un utilisateur
        </button>

        <table class="min-w-full border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border border-gray-300 px-4 py-2">Nom</th>
                    <th class="border border-gray-300 px-4 py-2">Email</th>
                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td class="border border-gray-300 px-4 py-2">{{ user.name }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ user.email }}</td>
                    <td class="border border-gray-300 px-4 py-2 flex space-x-4">
                        <button @click="$router.push(`/admin/utilisateur/edit/${user.id}`)"
                            class="text-blue-600 hover:underline">
                            Modifier
                        </button>
                        <button @click="supprimerUser(user.id)" v-if="!user.is_admin"
                            class="text-red-600 hover:underline">
                            Supprimer
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>


    </div>
</template>

<script>
import axios from "../../../../../axios.config.js";
import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";
import FormulaireUser from "./FormulaireUser.vue";

export default {
    name: "AdminUser",
    components: { FormulaireUser },
    data() {
        return {
            users: [],
            isFormVisible: false,
            isEditMode: false,
            currentUser: null,
        };
    },
    methods: {
        openCreateForm() {
            this.isFormVisible = true;
            this.isEditMode = false;
            this.currentUser = {
                name: "",
                email: "",
                password: "",
                is_admin: false,
            };
        },
        openEditForm(user) {
            this.isFormVisible = true;
            this.isEditMode = true;
            this.currentUser = { ...user };
            this.currentUser.password = ""; // On ne montre pas le mot de passe
        },
        closeForm() {
            this.isFormVisible = false;
            this.currentUser = null;
        },
        handleFormSubmit(formData) {
            if (this.isEditMode) {
                axios
                    .put(`/api/users/${formData.id}`, formData)
                    .then(() => {
                        this.showSuccessToast("Utilisateur modifié avec succès !");
                        this.fetchUsers();
                        this.closeForm();
                    })
                    .catch(() => {
                        this.showErrorToast("Erreur lors de la modification de l'utilisateur.");
                    });
            } else {
                axios
                    .post("/api/users", formData)
                    .then(() => {
                        this.showSuccessToast("Utilisateur créé avec succès !");
                        this.fetchUsers();
                        this.closeForm();
                    })
                    .catch(() => {
                        this.showErrorToast("Erreur lors de la création de l'utilisateur.");
                    });
            }
        },
        supprimerUser(id) {
            if (confirm("Êtes-vous sûr de vouloir supprimer cet utilisateur ?")) {
                axios
                    .post(`/api/users/${id}/delete`, {}, {
                        _method: 'DELETE'
                    })
                    .then(() => {
                        this.showSuccessToast("Utilisateur supprimé avec succès !");
                        this.fetchUsers();
                    })
                    .catch(() => {
                        this.showErrorToast("Erreur lors de la suppression de l'utilisateur.");
                    });
            }
        },
        fetchUsers() {
            axios
                .get("/api/users")
                .then((response) => {
                    this.users = response.data;
                })
                .catch((error) => {
                    console.error("Erreur lors de la récupération des utilisateurs:", error);
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
        this.fetchUsers();
    },
};
</script>

<style scoped>
/* Ajoutez vos styles ici */
</style>
