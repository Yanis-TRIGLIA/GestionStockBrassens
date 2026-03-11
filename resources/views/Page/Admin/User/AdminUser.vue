<template>
    <div>
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-800">🧑 Utilisateurs</h2>
                <p class="text-sm text-gray-500">Gestion des comptes — Vue administrateur</p>
            </div>
            <button @click="$router.push('/admin/utilisateur/create')"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition w-fit">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Créer un utilisateur
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">Total</p>
                <p class="text-2xl font-bold text-gray-800">{{ users.length }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">Admins</p>
                <p class="text-2xl font-bold text-yellow-600">{{ users.filter(u => u.is_admin).length }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">Résultats</p>
                <p class="text-2xl font-bold text-green-700">{{ usersFiltered.length }}</p>
            </div>
        </div>

        <!-- Recherche -->
        <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
            <div class="relative w-full md:w-1/3">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input v-model="recherche" type="text" placeholder="Rechercher par nom ou email..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <select v-model="filtreRole" class="w-full md:w-1/5 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tous les rôles</option>
                <option value="admin">Admins</option>
                <option value="user">Utilisateurs</option>
            </select>
            <span class="text-sm text-gray-500">{{ usersFiltered.length }} résultat(s)</span>
        </div>

        <!-- Mobile cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:hidden">
            <div v-for="user in usersFiltered" :key="user.id" class="bg-white p-4 shadow-md rounded-lg flex flex-col items-center text-center">
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                    <span class="text-2xl font-bold text-blue-600">{{ user.name.charAt(0).toUpperCase() }}</span>
                </div>
                <h4 class="font-semibold text-lg">{{ user.name }}</h4>
                <div class="text-sm text-gray-500 mb-1">{{ user.email }}</div>
                <span v-if="user.is_admin" class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full border border-yellow-200 mb-3">Admin</span>
                <span v-else class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full border mb-3">Utilisateur</span>
                <div class="flex gap-3">
                    <button @click="$router.push(`/admin/utilisateur/edit/${user.id}`)"
                        class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition text-sm">✏ Modifier</button>
                    <button v-if="!user.is_admin" @click="confirmDeletion(user.id)"
                        class="px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition text-sm">🗑 Supprimer</button>
                </div>
            </div>
        </div>

        <!-- Table desktop -->
        <div class="overflow-x-auto hidden md:block">
            <div class="mt-2 bg-white rounded-lg shadow-md p-4">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 uppercase tracking-wide border-b">
                            <th class="px-3 py-3">Nom</th>
                            <th class="px-3 py-3">Email</th>
                            <th class="px-3 py-3">Rôle</th>
                            <th class="px-3 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in usersFiltered" :key="user.id" class="border-b border-gray-50 hover:bg-gray-50 transition">
                            <td class="px-3 py-3 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-bold text-blue-600">{{ user.name.charAt(0).toUpperCase() }}</span>
                                    </div>
                                    <span class="font-medium text-gray-800">{{ user.name }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-3 align-middle text-sm text-gray-600">{{ user.email }}</td>
                            <td class="px-3 py-3 align-middle">
                                <span v-if="user.is_admin" class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full border border-yellow-200 font-medium">⭐ Admin</span>
                                <span v-else class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full border font-medium">Utilisateur</span>
                            </td>
                            <td class="px-3 py-3 text-center align-middle">
                                <div class="flex justify-center items-center gap-2">
                                    <button @click="$router.push(`/admin/utilisateur/edit/${user.id}`)"
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/><path d="M20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                        Modifier
                                    </button>
                                    <button v-if="!user.is_admin" @click="confirmDeletion(user.id)"
                                        class="flex items-center gap-1.5 px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                        Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="usersFiltered.length === 0">
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">Aucun utilisateur trouvé.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal suppression -->
        <div v-if="showConfirmationModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-xl shadow-xl max-w-sm w-full mx-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Supprimer l'utilisateur</h3>
                </div>
                <p class="text-gray-600 mb-6">Cette action est irréversible. Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
                <div class="flex justify-end gap-3">
                    <button @click="cancelDeletion"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Annuler</button>
                    <button @click="deleteUser"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Supprimer</button>
                </div>
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
            recherche: "",
            filtreRole: "",
            isFormVisible: false,
            isEditMode: false,
            currentUser: null,
            showConfirmationModal: false,
            userToDeleteId: null,
        };
    },
    computed: {
        usersFiltered() {
            let filtered = this.users;
            if (this.recherche) {
                const q = this.recherche.toLowerCase();
                filtered = filtered.filter(u => u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q));
            }
            if (this.filtreRole === 'admin') filtered = filtered.filter(u => u.is_admin);
            else if (this.filtreRole === 'user') filtered = filtered.filter(u => !u.is_admin);
            return filtered;
        },
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
        confirmDeletion(userId) {
            this.userToDeleteId = userId;
            this.showConfirmationModal = true;
        },
        cancelDeletion() {
            this.showConfirmationModal = false;
            this.userToDeleteId = null;
        },
        deleteUser() {
            axios
                .post(`/api/users/${this.userToDeleteId}/delete`, {}, { _method: 'DELETE' })
                .then(() => {
                    this.showSuccessToast("Utilisateur supprimé avec succès !");
                    this.fetchUsers();
                    this.showConfirmationModal = false;
                    this.userToDeleteId = null;
                })
                .catch(() => {
                    this.showErrorToast("Erreur lors de la suppression de l'utilisateur.");
                    this.showConfirmationModal = false;
                });
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
