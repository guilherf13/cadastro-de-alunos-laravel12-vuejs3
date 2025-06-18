import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import StudentsView from '../views/StudentsView.vue';
import StudentFormView from '../views/StudentFormView.vue';
import LoginView from '../views/LoginView.vue';
import { useAuthStore } from '../stores/auth';

const routes: Array<RouteRecordRaw> = [
  {
    path: '/login',
    name: 'login',
    component: LoginView
  },
  {
    path: '/',
    name: 'students',
    component: StudentsView,
    meta: { requiresAuth: true }
  },
  {
    path: '/alunos/novo',
    name: 'student-new',
    component: StudentFormView,
    meta: { requiresAuth: true }
  },
  {
    path: '/alunos/editar/:id',
    name: 'student-edit',
    component: StudentFormView,
    props: true,
    meta: { requiresAuth: true }
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
});

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);

  if (requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' });
  } else {
    next();
  }
});

export default router;