<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Header -->
      <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Sistema de Alunos</h1>
            <p class="mt-1 text-sm text-gray-600">Gerencie todos os alunos do sistema</p>
          </div>
          <div class="mt-4 sm:mt-0 flex items-center space-x-4">
            <div class="text-right">
              <p class="text-sm font-medium text-gray-800">{{ authStore.user?.name }}</p>
              <p class="text-xs text-gray-500">{{ authStore.user?.perfil }}</p>
            </div>
            <BaseButton @click="$router.push('/alunos/novo')">
              <PlusIcon class="h-5 w-5 mr-2" />
              Novo Aluno
            </BaseButton>
            <button @click="handleLogout" title="Sair" class="p-2 text-gray-500 hover:text-error-500 rounded-full hover:bg-gray-100 transition-colors">
              <ArrowRightOnRectangleIcon class="h-6 w-6" />
            </button>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <StudentsStats />

      <!-- Filters -->
      <div class="mb-6">
        <StudentsFilter />
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-flex items-center space-x-2">
          <svg class="animate-spin h-6 w-6 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <span class="text-gray-600">Carregando alunos...</span>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-error-50 border border-error-200 rounded-lg p-4 mb-6">
        <div class="flex">
          <div class="flex-shrink-0">
            <ExclamationTriangleIcon class="h-5 w-5 text-error-400" />
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-error-800">Erro ao carregar dados</h3>
            <p class="mt-1 text-sm text-error-700">{{ error }}</p>
            <div class="mt-3">
              <BaseButton variant="outline" size="sm" @click="fetchStudents">
                Tentar Novamente
              </BaseButton>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredStudents.length === 0 && totalStudents === 0" class="text-center py-12">
        <AcademicCapIcon class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum aluno encontrado</h3>
        <p class="mt-1 text-sm text-gray-500">Comece criando um novo aluno</p>
        <div class="mt-6">
          <BaseButton @click="$router.push('/alunos/novo')">
            <PlusIcon class="h-5 w-5 mr-2" />
            Novo Aluno
          </BaseButton>
        </div>
      </div>

      <!-- No Results State -->
      <div v-else-if="filteredStudents.length === 0" class="text-center py-12">
        <MagnifyingGlassIcon class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum resultado encontrado</h3>
        <p class="mt-1 text-sm text-gray-500">Tente ajustar os filtros ou termo de busca</p>
      </div>

      <!-- Students Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="student in filteredStudents"
          :key="student.id"
          class="animate-fade-in"
        >
          <StudentCard
            :student="student"
            :user-profile="authStore.userProfile || null"
            @edit="editStudent(student)"
            @delete="confirmDelete(student)"
            @toggle-status="toggleStudentStatus(student)"
          />
        </div>
      </div>

      <!-- Delete Confirmation Dialog -->
      <ConfirmDialog
        :show="showDeleteDialog"
        :title="deleteDialogTitle"
        :message="deleteDialogMessage"
        type="danger"
        confirm-text="Deletar"
        cancel-text="Cancelar"
        :loading="deletingStudent"
        @close="showDeleteDialog = false"
        @confirm="deleteStudent"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { 
  PlusIcon, 
  ExclamationTriangleIcon,
  AcademicCapIcon,
  MagnifyingGlassIcon,
  ArrowRightOnRectangleIcon
} from '@heroicons/vue/24/outline';

import { useStudentsStore } from '@/stores/students';
import { useNotificationsStore } from '@/stores/notifications';
import { useAuthStore } from '@/stores/auth';

import BaseButton from '@/components/BaseButton.vue';
import StudentCard from '@/components/StudentCard.vue';
import StudentsStats from '@/components/StudentsStats.vue';
import StudentsFilter from '@/components/StudentsFilter.vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';

import type { Student } from '@/types';

const router = useRouter();
const studentsStore = useStudentsStore();
const notificationsStore = useNotificationsStore();
const authStore = useAuthStore();

// Estado local
const showDeleteDialog = ref(false);
const studentToDelete = ref<Student | null>(null);
const deletingStudent = ref(false);

// Estados computados
const loading = computed(() => studentsStore.loading);
const error = computed(() => studentsStore.error);
const filteredStudents = computed(() => studentsStore.filteredStudents);
const totalStudents = computed(() => studentsStore.totalStudents);
const isGestor = computed(() => authStore.userProfile === 'Gestor');
const userProfile = computed(() => authStore.userProfile);

const deleteDialogTitle = computed(() => 
  studentToDelete.value ? `Deletar ${studentToDelete.value.nome}` : ''
);

const deleteDialogMessage = computed(() => 
  'Esta ação não pode ser desfeita. Todos os dados do aluno serão removidos permanentemente.'
);

// Métodos
function handleLogout() {
  authStore.logout();
  router.push('/login');
}

async function fetchStudents() {
  await studentsStore.fetchStudents();
}

function editStudent(student: Student) {
  if (!student || student.id === undefined || student.id === null) {
    notificationsStore.error('Erro de Dados', 'Não foi possível obter o ID do aluno para edição.');
    return;
  }
  
  const studentId = Number(student.id);

  if (isNaN(studentId) || studentId <= 0) {
    notificationsStore.error('ID de Aluno Inválido', `O ID "${student.id}" é inválido.`);
    return;
  }
  
  router.push(`/alunos/editar/${studentId}`);
}

function confirmDelete(student: Student) {
  if (student && student.id) {
    studentToDelete.value = student;
    showDeleteDialog.value = true;
  }
}

async function deleteStudent() {
  if (!studentToDelete.value?.id) return;
  
  deletingStudent.value = true;
  try {
    await studentsStore.deleteStudent(studentToDelete.value.id);
    notificationsStore.success(
      'Aluno deletado com sucesso',
      `${studentToDelete.value.nome} foi removido do sistema`
    );
    showDeleteDialog.value = false;
    studentToDelete.value = null;
  } catch (error) {
    notificationsStore.error(
      'Erro ao deletar aluno',
      'Tente novamente mais tarde'
    );
  } finally {
    deletingStudent.value = false;
  }
}

function toggleStudentStatus(student: Student) {
  console.log('Tentativa de alterar status para o aluno:', student.nome);
}

// Lifecycle
onMounted(() => {
  fetchStudents();
});
</script>