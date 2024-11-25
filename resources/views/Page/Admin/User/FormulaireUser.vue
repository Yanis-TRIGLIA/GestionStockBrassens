<template>
    <form @submit.prevent="submitForm">
        <div class="mb-4">
            <label for="name" class="block text-gray-700">Nom</label>
            <input
                v-model="formData.name"
                type="text"
                id="name"
                class="border rounded w-full py-2 px-3"
                placeholder="Nom de l'utilisateur"
            />
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700">Email</label>
            <input
                v-model="formData.email"
                type="email"
                id="email"
                class="border rounded w-full py-2 px-3"
                placeholder="Email de l'utilisateur"
            />
        </div>

        <div class="mb-4">
            <label for="password" class="block text-gray-700">Mot de passe</label>
            <input
                v-model="formData.password"
                type="password"
                id="password"
                class="border rounded w-full py-2 px-3"
                placeholder="Mot de passe"
            />
        </div>

        <div class="mb-4">
            <label for="is_admin" class="block text-gray-700">Est-ce un administrateur ?</label>
            <input
                type="checkbox"
                id="is_admin"
                v-model="formData.is_admin"
                class="border rounded py-2 px-3"
            />
            <span class="text-gray-600">Cochez si cet utilisateur est administrateur.</span>
        </div>

        <button
            type="submit"
            class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700"
        >
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
                name: "",       // Nom de l'utilisateur
                email: "",      // Email de l'utilisateur
                password: "",   // Mot de passe de l'utilisateur
                is_admin: false // Si l'utilisateur est administrateur
            },
        };
    },
    watch: {
        user: {
            immediate: true,
            handler(newUser) {
                if (newUser) {
                    this.formData = { ...newUser };
                }
            },
        },
    },
    methods: {
        submitForm() {
            const formData = new FormData();
            formData.append("name", this.formData.name);
            formData.append("email", this.formData.email);
            formData.append("password", this.formData.password);
            formData.append("is_admin", this.formData.is_admin);

            this.$emit("submit", formData);
        },
    },
};
</script>
