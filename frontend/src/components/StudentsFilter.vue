<template>
  <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <h2 class="text-lg font-semibold text-gray-900">Filtros</h2>
      <BaseButton
        v-if="hasActiveFilters"
        variant="outline"
        size="sm"
        @click="clearFilters"
      >
        Limpar Filtros
      </BaseButton>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <BaseInput
        v-model="localFilters.search"
        label="Buscar"
        placeholder="Nome, email ou curso..."
        @input="updateFilters"
      />

      <BaseSelect
        v-model="localFilters.status"
        label="Status"
        @change="updateFilters"
      >
        <option value="all">Todos</option>
        <option value="Pendente">Pendente</option>
        <option value="Aprovado">Aprovado</option>
        <option value="Cancelado">Cancelado</option>
      </BaseSelect>

      <BaseSelect
        v-model="localFilters.curso"
        label="Curso"
        @change="updateFilters"
      >
        <option value="">Todos os cursos</option>
        <option v-for="curso in availableCourses" :key="curso" :value="curso">
          {{ curso }}
        </option>
      </BaseSelect>
    </div>

    <div class="flex items-center justify-between pt-4 border-t border-gray-200">
      <div class="text-sm text-gray-600">
        {{ resultText }}
      </div>
      
      <div v-if="hasActiveFilters" class="text-sm text-gray-500">
        {{ activeFiltersCount }} filtro{{ activeFiltersCount > 1 ? 's' : '' }} ativo{{ activeFiltersCount > 1 ? 's' : '' }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useStudentsStore } from '@/stores/students';
import BaseInput from './BaseInput.vue';
import BaseSelect from './BaseSelect.vue';
import BaseButton from './BaseButton.vue';
import type { FilterOptions } from '@/types';

const studentsStore = useStudentsStore();

const localFilters = ref<FilterOptions>({ ...studentsStore.filters });

const availableCourses = computed(() => studentsStore.availableCourses);

const hasActiveFilters = computed(() => {
  return localFilters.value.search !== '' || 
         localFilters.value.status !== 'all' || 
         localFilters.value.curso !== '';
});

const activeFiltersCount = computed(() => {
  let count = 0;
  if (localFilters.value.search) count++;
  if (localFilters.value.status !== 'all') count++;
  if (localFilters.value.curso) count++;
  return count;
});

const resultText = computed(() => {
  const total = studentsStore.filteredStudents.length;
  const totalStudents = studentsStore.totalStudents;
  
  if (total === totalStudents) {
    return `${total} aluno${total !== 1 ? 's' : ''}`;
  }
  
  return `${total} de ${totalStudents} aluno${totalStudents !== 1 ? 's' : ''}`;
});

function updateFilters() {
  studentsStore.setFilters(localFilters.value);
}

function clearFilters() {
  localFilters.value = {
    search: '',
    status: 'all',
    curso: ''
  };
  studentsStore.clearFilters();
}

// Sincronizar com mudanças externas
watch(() => studentsStore.filters, (newFilters) => {
  localFilters.value = { ...newFilters };
}, { deep: true });
</script>