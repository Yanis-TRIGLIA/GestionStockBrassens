<template class="">
    <div class="min-h-screen  py-6 px-4 sm:px-6 lg:px-8 bg-blue-400">
      <!-- En-tête de la page -->
      <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <div class="text-center">
          <h1 class="text-3xl font-bold text-gray-800">Mon Profil</h1>
          <p class="text-lg text-gray-500">Gérer vos informations personnelles</p>
        </div>
  
        <!-- Affichage des informations de l'utilisateur -->
        <div class="mt-8 flex flex-col sm:flex-row justify-between">
          <div class="sm:w-1/3 mb-6 sm:mb-0">
            <!-- Photo de Profil -->
            <div class="flex justify-center">
              <img
                v-if="user.photo_profil"
                :src="`${baseUrl}/storage/${user.photo_profil}`"
                alt="Photo de Profil"
                class="w-32 h-32 rounded-full object-cover"
              />
              <img
                v-else
                src="https://via.placeholder.com/150"
                alt="Photo de Profil"
                class="w-32 h-32 rounded-full object-cover"
              />
            </div>
          </div>
  
          <!-- Informations Utilisateur -->
          <div class="sm:w-2/3">
            <form @submit.prevent="updateProfile">
              <div class="space-y-4">
                <div>
                  <label for="name" class="block text-gray-700">Nom</label>
                  <input
                    v-model="user.name"
                    id="name"
                    type="text"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md"
                    required
                  />
                </div>
  
                <div>
                  <label for="email" class="block text-gray-700">Email</label>
                  <input
                    v-model="user.email"
                    id="email"
                    type="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md"
                    required
                  />
                </div>
  
                <div>
                  <label for="password" class="block text-gray-700">Mot de passe</label>
                  <input
                    v-model="password"
                    id="password"
                    type="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md"
                    placeholder="Laissez vide pour ne pas changer"
                  />
                </div>
  
                <div>
                  <label for="photo" class="block text-gray-700">Changer la photo de profil</label>
                  <input
                    ref="photoInput"
                    type="file"
                    @change="handleFileChange"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md"
                  />
                </div>
  
                <div class="flex justify-between items-center mt-4">
                  <button
                    type="submit"
                    :disabled="isUpdating"
                    class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 disabled:bg-gray-400"
                  >
                    <span v-if="isUpdating">Mise à jour...</span>
                    <span v-else>Mettre à jour</span>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
  
      <!-- Bouton de déconnexion -->
      <div class="max-w-4xl mx-auto mt-8">
        <button
          @click="logout"
          class="w-full px-6 py-2 bg-red-500 text-white rounded-md hover:bg-red-600"
        >
          Se déconnecter
        </button>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "../../../axios.config.js";
  import { ref, onMounted } from 'vue';
  
  export default {
    name: 'UserPage',
    setup() {
      const user = ref({
        name: '',
        email: '',
        photo_profil: null,
      });
      const password = ref('');
      const isUpdating = ref(false);
      const photoFile = ref(null);
  
      const fetchUserData = async () => {
        try {
          const response = await axios.get('/api/user');
          user.value = response.data;
        } catch (error) {
          console.error('Erreur lors de la récupération des données utilisateur', error);
        }
      };
  
      const updateProfile = async () => {
        isUpdating.value = true;
        const formData = new FormData();
        formData.append('name', user.value.name);
        formData.append('email', user.value.email);
        if (password.value) formData.append('password', password.value);
        if (photoFile.value) formData.append('photo_profil', photoFile.value);
  
        try {
          await axios.post(`/api/users/${user.value.id}`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });
          alert('Profil mis à jour avec succès');
          fetchUserData();
        } catch (error) {
          console.error('Erreur lors de la mise à jour du profil', error);
          alert('Erreur lors de la mise à jour');
        } finally {
          isUpdating.value = false;
        }
      };
  
      const handleFileChange = (event) => {
        photoFile.value = event.target.files[0];
      };
  
      const logout = async () => {
        try {
          await axios.post('/api/logout');
          window.location.href = '/login'; // Rediriger vers la page de login
        } catch (error) {
          console.error('Erreur lors de la déconnexion', error);
        }
      };
  
      onMounted(() => {
        fetchUserData();
      });
  
      return {
        user,
        baseUrl: import.meta.env.VITE_APP_URL,
        password,
        isUpdating,
        photoFile,
        updateProfile,
        handleFileChange,
        logout,
      };
    },
  };
  </script>
  
  <style scoped>
  /* Styles personnalisés */
  </style>
  