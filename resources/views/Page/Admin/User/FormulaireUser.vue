<template>
    <form @submit.prevent="submitForm">
        <div class="mb-4">
            <label for="name" class="block text-gray-700">Nom</label>
            <input v-model="formData.name" type="text" id="name" class="border rounded w-full py-2 px-3"
                placeholder="Nom de l'utilisateur" />
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700">Email</label>
            <input v-model="formData.email" type="email" id="email" class="border rounded w-full py-2 px-3"
                placeholder="Email de l'utilisateur" />
        </div>

        <div class="mb-4">
            <label for="password" class="block text-gray-700">Mot de passe</label>
            <input v-model="formData.password" type="password" id="password" class="border rounded w-full py-2 px-3"
                placeholder="Mot de passe" />
        </div>

        <div class="mb-4">
            <label for="is_admin" class="block text-gray-700">Est-ce un administrateur ?</label>
            <input type="checkbox" id="is_admin" v-model="formData.is_admin" class="border rounded py-2 px-3 ml-2" />
            <span class="text-gray-600 ml-2">Cochez si cet utilisateur est administrateur.</span>
        </div>

        <!-- Zone de dépôt d'image (Dropzone) -->
        <div class="w-[400px] mb-5 relative border-2 border-gray-300 border-dashed rounded-lg p-6" id="dropzone">
            <input type="file" class="absolute inset-0 w-full h-full opacity-0 z-50" @change="handleFileUpload" accept="image/*" />
            <div class="text-center">
                <img class="mx-auto h-12 w-12" src="https://www.svgrepo.com/show/357902/image-upload.svg" alt="">
                <h3 class="mt-2 text-sm font-medium text-gray-900">
                    <label for="file-upload" class="relative cursor-pointer">
                        <span>Ajouter votre image de profil</span>
 
                        <input id="file-upload" name="file-upload" type="file" class="sr-only" @change="handleFileUpload">
                    </label>
                </h3>
                <p class="mt-1 text-xs text-gray-500">
                    PNG, JPG 10MB
                </p>
            </div>
            <img v-if="imagePreview" :src="imagePreview" class="mt-4 mx-auto max-h-40" id="preview" />
        </div>

        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
            Enregistrer
        </button>
    </form>
</template>

<script>
export default {
    name: "FormulaireUser",
    props: {
        user: {
            type: Object,
            default: () => ({}),
        },
        isEditMode: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            formData: {
                name: "",
                email: "",
                password: "",
                is_admin: false,
                photo_profil: null, // Ajout du champ pour l'image
            },
            imagePreview: null, // Pour afficher l'aperçu de l'image
        };
    },
    watch: {
        user: {
            immediate: true,
            handler(newUser) {
                if (newUser) {
                    this.formData = { ...newUser, photo_profil: null };
                }
            },
        },
    },
    methods: {
        handleFileUpload(event) {
            // Récupérer le fichier téléchargé
            const file = event.target.files[0];
            if (file) {
                // Ajouter le fichier à formData
                this.formData.photo_profil = file;
                
                // Générer un aperçu de l'image (si c'est une image)
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result; // L'aperçu de l'image
                };
                reader.readAsDataURL(file); // Lire l'image comme une URL base64
            }
        },
        submitForm() {
            const formData = new FormData();
            formData.append("name", this.formData.name);
            formData.append("email", this.formData.email);
            formData.append("password", this.formData.password);

            formData.append("is_admin", this.formData.is_admin !== undefined ? this.formData.is_admin : false);

            if (this.formData.photo_profil) {
                formData.append("photo_profil", this.formData.photo_profil);
            }

            console.log('Données envoyées:', formData);

            this.$emit("submit", formData);
        },
    },
};
</script>

<style scoped>
/* Ajoutez vos styles ici */
</style>
