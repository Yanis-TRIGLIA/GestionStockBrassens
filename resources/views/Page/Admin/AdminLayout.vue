<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <nav class="w-1/5 bg-gray-800 text-white p-4 h-full">
            <h1 class="text-xl font-bold mb-8">️👨🏻‍💻 Espace Administrateur</h1>
            <ul class="space-y-4">
                <li>
                    <router-link
                        to="/admin/produits"
                        class="block px-4 py-2 rounded hover:bg-gray-700"
                    >
                        Produits
                    </router-link>
                </li>
                <li>
                    <router-link
                        to="/admin/zones"
                        class="block px-4 py-2 rounded hover:bg-gray-700"
                    >
                        Zones
                    </router-link>
                </li>
                <li>
                    <router-link
                        to="/admin/agent"
                        class="block px-4 py-2 rounded hover:bg-gray-700"
                    >
                        Agents
                    </router-link>
                </li>
                <li>
                    <router-link
                        to="/admin/utilisateurs"
                        class="block px-4 py-2 rounded hover:bg-gray-700"
                    >
                        Utilisateurs
                    </router-link>
                </li>
                <li>
                    <router-link
                        to="/admin/categories"
                        class="block px-4 py-2 rounded hover:bg-gray-700"
                    >
                        Catégories
                    </router-link>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="w-4/5 p-8 bg-gray-100 h-full">
            <router-view />
        </main>
    </div>
</template>

<script>
export default {
    name: "AdminLayout"
}
</script>

<style scoped>
/* Styles spécifiques pour le layout */
</style>
