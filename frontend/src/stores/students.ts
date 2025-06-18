import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { createStudent, getStudents, updateStudent, deleteStudent, updateStudentStatus, getStudent } from '@/services/api';
import type { Student, FilterOptions } from '@/types';

export const useStudentsStore = defineStore('students', () => {
  // Estado
  const students = ref<Student[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);
  const filters = ref<FilterOptions>({
    search: '',
    status: 'all',
    curso: ''
  });

  // Getters computados
  const filteredStudents = computed(() => {
    return students.value.filter(student => {
      const matchesSearch = !filters.value.search || 
        student.nome.toLowerCase().includes(filters.value.search.toLowerCase()) ||
        student.email.toLowerCase().includes(filters.value.search.toLowerCase()) ||
        student.curso.toLowerCase().includes(filters.value.search.toLowerCase());

      const matchesStatus = filters.value.status === 'all' || 
        student.status === filters.value.status;

      const matchesCurso = !filters.value.curso || 
        student.curso.toLowerCase().includes(filters.value.curso.toLowerCase());

      return matchesSearch && matchesStatus && matchesCurso;
    });
  });

  const totalStudents = computed(() => students.value.length);
  const approvedStudents = computed(() => students.value.filter(s => s.status === 'Aprovado').length);
  const cancelledStudents = computed(() => students.value.filter(s => s.status === 'Cancelado').length);

  const availableCourses = computed(() => {
    const courses = students.value.map(s => s.curso);
    return [...new Set(courses)].sort();
  });

  // Actions
  async function fetchStudents() {
    loading.value = true;
    error.value = null;
    try {
      const fetchedStudents = await getStudents();
      students.value = fetchedStudents.map(student => ({
        id: Number(student.id),
        nome: student.nome,
        email: student.email,
        cpf: student.cpf,
        data_nascimento: student.data_nascimento,
        turma: student.turma,
        telefone: student.telefone,
        curso: student.curso,
        status: student.status,
        created_at: student.created_at,
        updated_at: student.updated_at,
      }));
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao carregar alunos';
      console.error('Erro ao buscar alunos:', err);
    } finally {
      loading.value = false;
    }
  }

  async function createStudentAction(studentData: Omit<Student, 'id' | 'created_at' | 'updated_at'>) {
    loading.value = true;
    error.value = null;
    try {
      const newStudent = await createStudent(studentData);
      // Garante que o ID seja um número
      newStudent.id = Number(newStudent.id);
      students.value.push(newStudent);
      return newStudent;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao criar aluno';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function updateStudentAction(id: number, studentData: Partial<Student>) {
    loading.value = true;
    error.value = null;
    try {
      const updatedStudent = await updateStudent(id, studentData);
      // Garante que o ID seja um número
      updatedStudent.id = Number(updatedStudent.id);
      const index = students.value.findIndex(s => s.id === id);
      if (index !== -1) {
        students.value[index] = updatedStudent;
      }
      return updatedStudent;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao atualizar aluno';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function deleteStudentAction(id: number) {
    loading.value = true;
    error.value = null;
    try {
      await deleteStudent(id);
      students.value = students.value.filter(s => s.id !== id);
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao deletar aluno';
      throw err;
    } finally {
      loading.value = false;
    }
  }

  async function updateStudentStatusAction(id: number, status: 'Pendente' | 'Aprovado' | 'Cancelado') {
    try {
      const updatedStudent = await updateStudentStatus(id, status);
      // Garante que o ID seja um número
      updatedStudent.id = Number(updatedStudent.id);
      const index = students.value.findIndex(s => s.id === id);
      if (index !== -1) {
        students.value[index] = updatedStudent;
      }
      return updatedStudent;
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Erro ao atualizar status';
      throw err;
    }
  }

  async function fetchStudentById(id: number): Promise<Student | undefined> {
    const existingStudent = students.value.find(s => s.id === id);
    if (existingStudent) {
      return existingStudent;
    }

    try {
      const student = await getStudent(id);
      students.value.push(student); // Adiciona ao 'cache' do store
      return student;
    } catch (err) {
      error.value = `Erro ao buscar aluno com ID ${id}`;
      throw err;
    }
  }

  function setFilters(newFilters: Partial<FilterOptions>) {
    filters.value = { ...filters.value, ...newFilters };
  }

  function clearFilters() {
    filters.value = {
      search: '',
      status: 'all',
      curso: ''
    };
  }

  return {
    // Estado
    students,
    loading,
    error,
    filters,
    // Getters
    filteredStudents,
    totalStudents,
    approvedStudents,
    cancelledStudents,
    availableCourses,
    // Actions
    fetchStudents,
    fetchStudentById,
    createStudent: createStudentAction,
    updateStudent: updateStudentAction,
    deleteStudent: deleteStudentAction,
    updateStudentStatus: updateStudentStatusAction,
    setFilters,
    clearFilters
  };
});