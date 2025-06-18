<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex items-center space-x-3 mb-4">
          <BaseButton
            variant="outline"
            @click="$router.push('/')"
            class="!p-2"
          >
            <ArrowLeftIcon class="h-5 w-5" />
          </BaseButton>
          <div>
            <h1 class="text-3xl font-bold text-gray-900">
              {{ isEditing ? 'Editar Aluno' : 'Novo Aluno' }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">
              {{ isEditing ? 'Atualize as informações do aluno' : 'Adicione um novo aluno ao sistema' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Loading state quando carregando dados do aluno -->
      <div v-if="loadingStudent" class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        <div class="text-center">
          <div class="inline-flex items-center space-x-2">
            <svg class="animate-spin h-6 w-6 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-600">Carregando dados do aluno...</span>
          </div>
        </div>
      </div>

      <!-- Form -->
      <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form @submit.prevent="handleSubmit" class="p-8 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <BaseInput
              v-model="form.nome"
              label="Nome Completo"
              placeholder="Digite o nome completo"
              required
              :error="errors.nome"
              @blur="validateField('nome')"
            />

            <BaseInput
              v-model="form.cpf"
              label="CPF"
              placeholder="Digite o CPF (somente números)"
              required
              :error="errors.cpf"
              @blur="validateField('cpf')"
            />

            <BaseInput
              v-model="form.data_nascimento"
              label="Data de Nascimento"
              type="date"
              required
              :error="errors.data_nascimento"
              @blur="validateField('data_nascimento')"
            />
            
            <BaseInput
              v-model="form.turma"
              label="Turma"
              placeholder="Digite a turma"
              required
              :error="errors.turma"
              @blur="validateField('turma')"
            />

            <BaseInput
              v-model="form.email"
              label="E-mail"
              type="email"
              placeholder="Digite o e-mail"
              required
              :error="errors.email"
              @blur="validateField('email')"
            />

            <BaseInput
              v-model="form.telefone"
              label="Telefone"
              type="tel"
              placeholder="(11) 99999-9999"
              required
              :error="errors.telefone"
              @blur="validateField('telefone')"
            />

            <BaseInput
              v-model="form.curso"
              label="Curso"
              placeholder="Digite o curso"
              required
              :error="errors.curso"
              @blur="validateField('curso')"
            />

            <BaseSelect
              v-if="isGestor"
              v-model="form.status"
              label="Status"
              required
              :error="errors.status"
            >
              <option value="Pendente">Pendente</option>
              <option value="Aprovado">Aprovado</option>
              <option value="Cancelado">Cancelado</option>
            </BaseSelect>
          </div>

          <!-- Form Actions -->
          <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
            <BaseButton
              type="button"
              variant="outline"
              @click="$router.push('/')"
              :disabled="loading"
            >
              Cancelar
            </BaseButton>
            
            <BaseButton
              type="submit"
              :loading="loading"
              :disabled="!isFormValid"
            >
              {{ isEditing ? 'Atualizar' : 'Criar' }} Aluno
            </BaseButton>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

import { useStudentsStore } from '@/stores/students';
import { useNotificationsStore } from '@/stores/notifications';
import apiService from '@/services/api';
import { useAuthStore } from '@/stores/auth';

import BaseButton from '@/components/BaseButton.vue';
import BaseInput from '@/components/BaseInput.vue';
import BaseSelect from '@/components/BaseSelect.vue';

import type { Student, StudentFormData } from '@/types';

interface Props {
  id?: string;
}

const props = defineProps<Props>();
const router = useRouter();
const route = useRoute();
const studentsStore = useStudentsStore();
const notificationsStore = useNotificationsStore();
const authStore = useAuthStore();

// Estado
const loading = ref(false);
const loadingStudent = ref(false);

const form = reactive<StudentFormData>({
  nome: '',
  cpf: '',
  data_nascimento: '',
  turma: '',
  email: '',
  telefone: '',
  curso: '',
  status: 'Pendente'
});

const errors = reactive({
  nome: '',
  cpf: '',
  data_nascimento: '',
  turma: '',
  email: '',
  telefone: '',
  curso: '',
  status: ''
});

// Computed
const isEditing = computed(() => !!props.id || !!route.params.id);
const studentId = computed(() => Number(props.id || route.params.id as string));
const isGestor = computed(() => authStore.userProfile === 'Gestor');

const isFormValid = computed(() => {
  return form.nome && form.cpf && form.data_nascimento && form.turma && form.email && form.telefone && form.curso && form.status &&
         !errors.nome && !errors.cpf && !errors.data_nascimento && !errors.turma && !errors.email && !errors.telefone && !errors.curso && !errors.status;
});

// Métodos de validação
function validateField(field: keyof typeof errors) {
  switch (field) {
    case 'nome':
      errors.nome = !form.nome ? 'Nome é obrigatório' : 
                    form.nome.length < 2 ? 'Nome deve ter pelo menos 2 caracteres' : '';
      break;
    case 'cpf':
      const cpfRegex = /^\d{11}$/;
      errors.cpf = !form.cpf ? 'CPF é obrigatório' :
                   !cpfRegex.test(form.cpf) ? 'CPF deve ter 11 dígitos' : '';
      break;
    case 'data_nascimento':
      errors.data_nascimento = !form.data_nascimento ? 'Data de nascimento é obrigatória' : '';
      break;
    case 'turma':
      errors.turma = !form.turma ? 'Turma é obrigatória' : '';
      break;
    case 'email':
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      errors.email = !form.email ? 'E-mail é obrigatório' : 
                     !emailRegex.test(form.email) ? 'E-mail inválido' : '';
      break;
    case 'telefone':
      const phoneRegex = /^[\d\s\-\(\)\+]+$/;
      errors.telefone = !form.telefone ? 'Telefone é obrigatório' : 
                        !phoneRegex.test(form.telefone) ? 'Telefone inválido' : '';
      break;
    case 'curso':
      errors.curso = !form.curso ? 'Curso é obrigatório' : '';
      break;
    case 'status':
      errors.status = !form.status ? 'Status é obrigatório' : '';
      break;
  }
}

function validateForm() {
  Object.keys(errors).forEach(field => {
    validateField(field as keyof typeof errors);
  });
  return Object.values(errors).every(error => !error);
}

// Método de envio
async function handleSubmit() {
  if (!validateForm()) {
    notificationsStore.showNotification('Por favor, corrija os erros no formulário.', 'error');
    return;
  }
  
  loading.value = true;
  try {
    const studentData: Omit<Student, 'id' | 'created_at' | 'updated_at'> = { ...form };
    
    if (isEditing.value) {
      await studentsStore.updateStudent(studentId.value, studentData);
      notificationsStore.success('Aluno atualizado com sucesso!');
    } else {
      await studentsStore.createStudent(studentData);
      notificationsStore.success('Aluno criado com sucesso!');
    }
    router.push('/');
  } catch (error: any) {
    const message = error.response?.data?.message || 'Ocorreu um erro.';
    notificationsStore.error('Erro na Operação', message);
  } finally {
    loading.value = false;
  }
}

// Carregar dados do aluno ao montar
onMounted(async () => {
  if (isEditing.value && studentId.value) {
    loadingStudent.value = true;
    try {
      const student = await studentsStore.fetchStudentById(studentId.value);
      if (student) {
        Object.assign(form, {
          ...student,
          data_nascimento: student.data_nascimento.split('T')[0]
        });
      }
    } catch (err: any) {
      notificationsStore.error(
        'Erro ao carregar aluno',
        err.message || 'Não foi possível buscar os dados do aluno.'
      );
      router.push('/');
    } finally {
      loadingStudent.value = false;
    }
  }
});
</script>