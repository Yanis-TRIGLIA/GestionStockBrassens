import { createApp } from 'vue';
import { createWebHistory, createRouter } from 'vue-router';

// Importation des pages
import Produits from '../views/Page/ProductPage.vue';
import Produit from '../views/Page/Product.vue';
import Header from '../views/Components/Header.vue';
import Login from '../views/Page/Login.vue';
import Home from '../views/Page/Home.vue';
import TableauSortie from '../views/Page/TableauSortie.vue';
import TableauProduits from '../views/Page/TableauProduit.vue';

// Importation des pages Admin
import AdminLayout from '../views/Page/Admin/AdminLayout.vue';

import AdminDashboard from '../views/Page/Admin/AdminDashboard.vue';

import AdminProduits from '../views/Page/Admin/Produit/AdminProduit.vue';
import CreateProduit from '../views/Page/Admin/Produit/CreateProduit.vue';
import EditProduit from '../views/Page/Admin/Produit/EditProduit.vue';

import AdminZones from '../views/Page/Admin/Zone/AdminZone.vue';
import EditZones from '../views/Page/Admin/Zone/EditZone.vue';
import CreateZones from '../views/Page/Admin/Zone/CreateZone.vue';

import AdminAgent from '../views/Page/Admin/Agents/AdminAgent.vue';
import CreateAgent from '../views/Page/Admin/Agents/CreateAgent.vue';

// Componenet
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from "primevue/column";
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
    {
        path: '/tableau_produit',
        component: TableauProduits,
        meta: { requiresAuth: true },
    },
    {
        path: "/admin",
        component: AdminLayout,
        children: [
            { path: "", component: AdminDashboard },
            { path: "produits", component: AdminProduits },
            {
                path: 'produits/create',
                component: CreateProduit,
            },
            {
                path: 'produits/edit/:id',
                component: EditProduit,
            },
            { path: "zones", component: AdminZones },
            {
                path: 'zones/create',
                component: CreateZones,
            },
            {
                path: 'zones/edit/:id',
                component: EditZones,
            },
            { path: "agent", component: AdminAgent },
            {
                path: 'agent/create',
                component: CreateAgent,
            },

        ],
    },
];

// Configuration du routeur
const router = createRouter({
    history: createWebHistory(),
    routes,
});

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

const App = {
    components: { Header },
    template: `
        <div>
            <Header />
            <router-view />
        </div>
    `,
};

const app = createApp(App);

app.use(PrimeVue);
app.component('Button', Button);
app.component('DataTable', DataTable);
app.component('Column', Column);

// Utiliser le routeur
app.use(router);

// Monter l'application
app.mount('#app');
