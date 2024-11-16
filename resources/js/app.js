import { createApp } from 'vue';
import { createWebHistory, createRouter } from 'vue-router';

// Importation des composants
import Produits from '../views/Page/ProductPage.vue';
import Produit from '../views/Page/Product.vue';
import Header from '../views/Components/Header.vue';
import Login from '../views/Page/Login.vue';
import Home from '../views/Page/Home.vue';
import TableauSortie from '../views/Page/TableauSortie.vue';

import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import PrimeVue from 'primevue/config';

// Définir les routes
const routes = [
    {
        path: '/',
        component: Home,
        meta: { requiresAuth: false },
    },
    {
        path: '/connexion',
        component: Login,
        meta: { requiresAuth: false },
    },
    {
        path: '/liste_produit',
        component: Produits,
        meta: { requiresAuth: true },
    },
    {
        path: '/prod/:id',
        component: Produit,
        meta: { requiresAuth: true },
    },
    {
        path: '/sortie_produit',
        component: TableauSortie,
        meta: { requiresAuth: true },
    },
];

// Configuration du routeur
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Gérer la vérification d'authentification avant chaque changement de route
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('auth_token');
    const tokenExpiration = localStorage.getItem('auth_token_expiration');
    const isLoggedIn = token && Date.now() < tokenExpiration;

    if (to.meta.requiresAuth && !isLoggedIn) {
        next('/connexion');
    } else {
        next();
    }
});

// Définir le composant principal
const App = {
    components: { Header },
    template: `
        <div>
            <Header />
            <router-view />
        </div>
    `,
};

// Créer l'application Vue
const app = createApp(App);

// Utiliser PrimeVue et enregistrer les composants
app.use(PrimeVue); // Initialiser PrimeVue
app.component('Button', Button); // Enregistrer le composant Button
app.component('DataTable', DataTable); // Enregistrer le composant DataTable
app.component('Column', Column); // Enregistrer le composant Column

// Utiliser le routeur
app.use(router);

// Monter l'application
app.mount('#app');
