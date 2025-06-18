// Se aparecer erro de importação do axios, execute no terminal do frontend:
// npm install axios
import axios, { AxiosInstance, AxiosResponse } from 'axios';
import type { Student, ApiResponse } from '@/types';

class ApiService {
  private api: AxiosInstance;

  constructor() {
    this.api = axios.create({
      baseURL: 'http://localhost:8080/api',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      // withCredentials não é necessário para autenticação via Bearer token
    });

    // Interceptor para response
    this.api.interceptors.response.use(
      (response: any) => response,
      (error: any) => {
        console.error('API Error:', error.response?.data || error.message);
        return Promise.reject(error);
      }
    );
  }

  setAuthHeader(token: string) {
    this.api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
  }

  removeAuthHeader() {
    delete this.api.defaults.headers.common['Authorization'];
  }

  async login(credentials: {email: string, password: string}): Promise<AxiosResponse<{access_token: string, user: any}>> {
    return this.api.post('/login', credentials);
  }

  // Listar todos os alunos
  async getStudents(): Promise<Student[]> {
    const response: AxiosResponse<ApiResponse<Student[]>> = await this.api.get('/alunos');
    return response.data.data;
  }

  // Buscar aluno por ID
  async getStudent(id: number): Promise<Student> {
    const response: AxiosResponse<ApiResponse<Student>> = await this.api.get(`/alunos/${id}`);
    return response.data.data;
  }

  // Criar novo aluno
  async createStudent(student: Omit<Student, 'id' | 'created_at' | 'updated_at'>): Promise<Student> {
    const response: AxiosResponse<ApiResponse<Student>> = await this.api.post('/alunos', student);
    return response.data.data;
  }

  // Atualizar aluno
  async updateStudent(id: number, student: Partial<Student>): Promise<Student> {
    const response: AxiosResponse<ApiResponse<Student>> = await this.api.put(`/alunos/${id}`, student);
    return response.data.data;
  }

  // Deletar aluno
  async deleteStudent(id: number): Promise<void> {
    await this.api.delete(`/alunos/${id}`);
  }

  // Atualizar status do aluno
  async updateStudentStatus(id: number, status: 'Pendente' | 'Aprovado' | 'Cancelado'): Promise<Student> {
    const response: AxiosResponse<ApiResponse<Student>> = await this.api.patch(`/alunos/${id}/status`, { status });
    return response.data.data;
  }
}

export const apiService = new ApiService();