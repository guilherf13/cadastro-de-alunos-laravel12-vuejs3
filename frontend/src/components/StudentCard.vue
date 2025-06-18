<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
    <div class="p-6">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <div class="flex items-center space-x-2">
            <h3 class="text-lg font-semibold text-gray-900">{{ student.nome }}</h3>
            <StatusBadge :status="student.status" @click="canChangeStatus ? $emit('toggle-status') : null" :class="{'cursor-pointer hover:opacity-80': canChangeStatus}" />
          </div>
          
          <div class="mt-2 space-y-1">
            <p class="text-sm text-gray-600 flex items-center">
              <EnvelopeIcon class="h-4 w-4 mr-2 text-gray-400" />
              {{ student.email }}
            </p>
            <p class="text-sm text-gray-600 flex items-center">
              <PhoneIcon class="h-4 w-4 mr-2 text-gray-400" />
              {{ student.telefone }}
            </p>
            <p class="text-sm text-gray-600 flex items-center">
              <AcademicCapIcon class="h-4 w-4 mr-2 text-gray-400" />
              {{ student.curso }}
            </p>
          </div>

          <div v-if="student.created_at" class="mt-3 text-xs text-gray-400">
            Criado em {{ formatDate(student.created_at) }}
          </div>
        </div>

        <div v-if="canEdit || canDelete" class="flex items-center space-x-2 ml-4">
          <button
            v-if="canEdit"
            @click="$emit('edit')"
            class="p-2 text-gray-400 hover:text-primary-500 hover:bg-gray-50 rounded-lg transition-colors"
            title="Editar aluno"
          >
            <PencilIcon class="h-4 w-4" />
          </button>
          
          <button
            v-if="canDelete"
            @click="$emit('delete')"
            class="p-2 text-gray-400 hover:text-error-500 hover:bg-gray-50 rounded-lg transition-colors"
            title="Deletar aluno"
          >
            <TrashIcon class="h-4 w-4" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { 
  EnvelopeIcon, 
  PhoneIcon, 
  AcademicCapIcon,
  PencilIcon,
  TrashIcon
} from '@heroicons/vue/24/outline';
import type { Student } from '@/types';
import StatusBadge from './StatusBadge.vue';
import { computed } from 'vue';

interface Props {
  student: Student;
  userProfile: 'Gestor' | 'Funcionario' | null;
}

const props = defineProps<Props>();

const canEdit = computed(() => props.userProfile === 'Gestor' || props.userProfile === 'Funcionario');
const canDelete = computed(() => props.userProfile === 'Gestor');
const canChangeStatus = computed(() => props.userProfile === 'Gestor');

defineEmits<{
  (e: 'edit'): void;
  (e: 'delete'): void;
  (e: 'toggle-status'): void;
}>();

function formatDate(dateString: string) {
  return new Date(dateString).toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
}

function toggleStatus() {
  // Emit para o componente pai
  // O componente pai irá lidar com a lógica de alterar o status
}
</script>