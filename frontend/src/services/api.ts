// Se aparecer erro de importação do axios, execute no terminal do frontend:
// npm install axios
import axios, { type AxiosRequestConfig, type AxiosResponse } from 'axios';
import type { Student, ApiResponse } from '@/types';

const apiService = axios.create({
  baseURL: 'http://localhost:8080/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

apiService.interceptors.request.use((config: AxiosRequestConfig) => {
  return config;
});

apiService.interceptors.response.use(
  (response) => response,
  (error) => {
    console.error('Erro na resposta da API:', error.response?.data || error.message);
    if (error.response?.status === 401) {
      // Poderia despachar uma ação de logout aqui
      console.error('Sessão expirada ou inválida. Faça o login novamente.');
    }
    return Promise.reject(error);
  }
);

// --- Funções do Serviço ---

export function setAuthHeader(token: string) {
  apiService.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

export function removeAuthHeader() {
  delete apiService.defaults.headers.common['Authorization'];
}

export async function login(credentials: {email: string, password: string}) {
    return apiService.post('/login', credentials);
}

export async function getStudents(): Promise<Student[]> {
  const response: AxiosResponse<ApiResponse<Student[]>> = await apiService.get('/alunos');
  return response.data.data;
}

export async function getStudent(id: number): Promise<Student> {
  const response: AxiosResponse<ApiResponse<Student>> = await apiService.get(`/alunos/${id}`);
  return response.data.data;
}

export async function createStudent(student: Omit<Student, 'id' | 'created_at' | 'updated_at'>): Promise<Student> {
  const response: AxiosResponse<ApiResponse<Student>> = await apiService.post('/alunos', student);
  return response.data.data;
}

export async function updateStudent(id: number, student: Partial<Student>): Promise<Student> {
  const response: AxiosResponse<ApiResponse<Student>> = await apiService.put(`/alunos/${id}`, student);
  return response.data.data;
}

export async function deleteStudent(id: number): Promise<void> {
  await apiService.delete(`/alunos/${id}`);
}

export async function updateStudentStatus(id: number, status: 'Pendente' | 'Aprovado' | 'Cancelado'): Promise<Student> {
  const response: AxiosResponse<ApiResponse<Student>> = await apiService.patch(`/alunos/${id}/status`, { status });
  return response.data.data;
}