<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <nav class="w-1/5 bg-gray-800 text-white p-4 h-full flex flex-col">
            <h1 class="text-xl font-bold mb-8 flex items-center space-x-2">
                <span>👨🏻‍💻</span>
                <span>Espace Administrateur</span>
            </h1>
            <ul class="space-y-4 flex-grow">
                <li>
                    <router-link
                        to="/admin/produits"
                        class="flex items-center px-4 py-2 rounded hover:bg-gray-700 transition-colors"
                    >
                        <span class="text-lg mr-3">📦</span>
                        <span>Produits</span>
                    </router-link>
                </li>
                <li>
                    <router-link
                        to="/admin/zones"
                        class="flex items-center px-4 py-2 rounded hover:bg-gray-700 transition-colors"
                    >
                        <span class="text-lg mr-3">🗺️</span>
                        <span>Zones</span>
                    </router-link>
                </li>
                <li>
                    <router-link
                        to="/admin/agent"
                        class="flex items-center px-4 py-2 rounded hover:bg-gray-700 transition-colors"
                    >
                        <span class="text-lg mr-3">👮‍♂️</span>
                        <span>Agents</span>
                    </router-link>
                </li>
                <li>
                    <router-link
                        to="/admin/utilisateurs"
                        class="flex items-center px-4 py-2 rounded hover:bg-gray-700 transition-colors"
                    >
                        <span class="text-lg mr-3">👥</span>
                        <span>Utilisateurs</span>
                    </router-link>
                </li>
                <li>
                    <router-link
                        to="/admin/categories"
                        class="flex items-center px-4 py-2 rounded hover:bg-gray-700 transition-colors"
                    >
                        <span class="text-lg mr-3">📂</span>
                        <span>Catégories</span>
                    </router-link>
                </li>
            </ul>
            <div class="mt-auto">
                <router-link
                    to="/logout"
                    class="flex items-center px-4 py-2 text-red-500 hover:bg-gray-700 hover:text-white transition-colors"
                >
                    <span class="text-lg mr-3">🚪</span>
                    <span>Déconnexion</span>
                </router-link>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="w-4/5 p-8 bg-gray-100 h-full overflow-y-auto">
            <router-view />
        </main>
    </div>
</template>

<script>
export default {
    name: "AdminLayout",
};
</script>

<style scoped>
/* Effets supplémentaires */
nav ul li a:hover {
    background-color: #4a5568; /* Gris foncé pour le survol */
}
</style>
