import { defineStore } from 'pinia';
import { login, setAuthHeader, removeAuthHeader } from '@/services/api';
import type { User } from '@/types';

interface AuthState {
  user: User | null;
  token: string | null;
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: JSON.parse(localStorage.getItem('user') || 'null'),
    token: localStorage.getItem('token'),
  }),
  getters: {
    isAuthenticated: (state) => !!state.token && !!state.user,
    userProfile: (state) => state.user?.perfil,
  },
  actions: {
    async login(credentials: { email: string; password: string }) {
      const response = await login(credentials);
      const { access_token, user } = response.data;
      
      this.token = access_token;
      this.user = user;

      localStorage.setItem('token', access_token);
      localStorage.setItem('user', JSON.stringify(user));

      setAuthHeader(access_token);
    },
    logout() {
      this.token = null;
      this.user = null;
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      removeAuthHeader();
    },
    initializeAuth() {
        if (this.token) {
            setAuthHeader(this.token);
        }
    }
  },
}); 