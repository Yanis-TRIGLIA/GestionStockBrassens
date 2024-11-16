import { createApp } from 'vue';
import { createWebHistory, createRouter } from 'vue-router';

// Importation des composants
import Produits from '../views/Page/ProductPage.vue';
import Produit from '../views/Page/Product.vue'
import Header from '../views/Components/Header.vue';
import Login from '../views/Page/Login.vue';
import Home from '../views/Page/Home.vue';


const routes = [
    {
        path: '/',
        component: Home,
        meta: { requiresAuth: false },
    },
    { path: '/connexion', component: Login },
    { path: '/liste_produit', component: Produits },
    { path: '/prod/', component: Produit },
];

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

const app = createApp(App).use(router);

app.mount('#app');
