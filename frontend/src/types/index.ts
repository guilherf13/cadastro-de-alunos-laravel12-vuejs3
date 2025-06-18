export interface User {
  id?: number;
  name: string;
  email: string;
  perfil: 'Gestor' | 'Funcionario';
}

export interface Student {
  id?: number;
  nome: string;
  cpf: string;
  data_nascimento: string;
  turma: string;
  email: string;
  telefone: string;
  curso: string;
  status: 'Pendente' | 'Aprovado' | 'Cancelado';
  created_at?: string;
  updated_at?: string;
}

export interface ApiResponse<T> {
  data: T;
  message?: string;
  status: boolean;
}

export interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
}

export interface StudentFormData {
  nome: string;
  cpf: string;
  data_nascimento: string;
  turma: string;
  email: string;
  telefone: string;
  curso: string;
  status: 'Pendente' | 'Aprovado' | 'Cancelado';
}

export interface FilterOptions {
  search: string;
  status: 'all' | 'Pendente' | 'Aprovado' | 'Cancelado';
  curso: string;
}