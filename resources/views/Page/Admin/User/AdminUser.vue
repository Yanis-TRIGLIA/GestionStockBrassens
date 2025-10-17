<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">🧑 Liste des utilisateurs</h2>
        <button @click="$router.push('/admin/utilisateur/create')"
            class="mb-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Créer un utilisateur
        </button>
        <!-- Mobile cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:hidden">
            <div v-for="user in users" :key="user.id" class="bg-white p-4 shadow-md rounded-lg flex flex-col items-center text-center">
                <h4 class="font-semibold text-lg mb-2">{{ user.name }}</h4>
                <div class="text-sm text-gray-600 mb-3">{{ user.email }}</div>
                <div class="flex gap-3">
                    <button @click="$router.push(`/admin/utilisateur/edit/${user.id}`)" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" />
                            <path d="M20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                        </svg>
                        <span class="text-sm">Modifier</span>
                    </button>
                    <button v-if="!user.is_admin" @click="supprimerUser(user.id)" class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="3 6 5 6 21 6" />
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                            <path d="M10 11v6M14 11v6" />
                            <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" />
                        </svg>
                        <span class="text-sm">Supprimer</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto hidden md:block">
            <div class="mt-2 bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-700">Utilisateurs</h3>
                    <span class="text-sm text-gray-500">Total : {{ users.length }}</span>
                </div>
                <table class="w-full table-auto">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 border-b pb-2">
                            <th class="px-3 py-2">Nom</th>
                            <th class="px-3 py-2">Email</th>
                            <th class="px-3 py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id" class="transition transform hover:-translate-y-0.5 hover:shadow-lg">
                            <td class="px-3 py-3">{{ user.name }}</td>
                            <td class="px-3 py-3">{{ user.email }}</td>
                            <td class="px-3 py-3 text-center">
                                <div class="flex justify-center items-center gap-3">
                                    <button @click="$router.push(`/admin/utilisateur/edit/${user.id}`)" class="flex items-center gap-2 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow" aria-label="Modifier">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z" />
                                            <path d="M20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                        </svg>
                                        <span class="text-sm">Modifier</span>
                                    </button>
                                    <button v-if="!user.is_admin" @click="supprimerUser(user.id)" class="flex items-center gap-2 px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow" aria-label="Supprimer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <polyline points="3 6 5 6 21 6" />
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                            <path d="M10 11v6M14 11v6" />
                                            <path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" />
                                        </svg>
                                        <span class="text-sm">Supprimer</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">Aucun utilisateur trouvé.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


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
