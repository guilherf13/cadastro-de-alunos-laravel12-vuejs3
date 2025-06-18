<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
            <UsersIcon class="h-6 w-6 text-primary-600" />
          </div>
        </div>
        <div class="ml-4">
          <p class="text-sm font-medium text-gray-600">Total de Alunos</p>
          <p class="text-2xl font-bold text-gray-900">{{ totalStudents }}</p>
        </div>
      </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div class="w-12 h-12 bg-success-100 rounded-lg flex items-center justify-center">
            <CheckCircleIcon class="h-6 w-6 text-success-600" />
          </div>
        </div>
        <div class="ml-4">
          <p class="text-sm font-medium text-gray-600">Alunos Ativos</p>
          <p class="text-2xl font-bold text-gray-900">{{ activeStudents }}</p>
        </div>
      </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
            <XCircleIcon class="h-6 w-6 text-gray-600" />
          </div>
        </div>
        <div class="ml-4">
          <p class="text-sm font-medium text-gray-600">Alunos Inativos</p>
          <p class="text-2xl font-bold text-gray-900">{{ inactiveStudents }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { UsersIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline';
import { useStudentsStore } from '@/stores/students';

const studentsStore = useStudentsStore();

const totalStudents = computed(() => studentsStore.totalStudents);
const activeStudents = computed(() => studentsStore.activeStudents);
const inactiveStudents = computed(() => studentsStore.inactiveStudents);
</script>