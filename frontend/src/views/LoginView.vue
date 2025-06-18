<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-50">
    <div class="w-full max-w-md p-8 space-y-8 bg-white rounded-lg shadow-md">
      <div>
        <h2 class="text-3xl font-extrabold text-center text-gray-900">
          Acessar sua conta
        </h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div class="space-y-4 rounded-md shadow-sm">
          <BaseInput
            v-model="email"
            label="E-mail"
            type="email"
            placeholder="seu@email.com"
            required
            :error="errors.email"
          />
          <BaseInput
            v-model="password"
            label="Senha"
            type="password"
            placeholder="Sua senha"
            required
            :error="errors.password"
          />
        </div>

        <div v-if="errors.api" class="text-sm text-red-600">
          {{ errors.api }}
        </div>

        <div>
          <BaseButton
            type="submit"
            class="w-full"
            :loading="loading"
          >
            Entrar
          </BaseButton>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import BaseInput from '@/components/BaseInput.vue';
import BaseButton from '@/components/BaseButton.vue';
import { useAuthStore } from '@/stores/auth';

const email = ref('gestor@test.com');
const password = ref('password');
const loading = ref(false);
const errors = ref({ email: '', password: '', api: '' });

const authStore = useAuthStore();
const router = useRouter();

const handleLogin = async () => {
  loading.value = true;
  errors.value.api = '';
  try {
    await authStore.login({ email: email.value, password: password.value });
    router.push('/');
  } catch (error: any) {
    errors.value.api = error.response?.data?.message || 'Ocorreu um erro ao tentar fazer login.';
  } finally {
    loading.value = false;
  }
};
</script> 