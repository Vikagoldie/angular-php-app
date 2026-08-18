import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { API_BASE_URL } from './api-config';
import { toFormBody, formUrlEncodedHeaders } from './http-helpers';
import { UserModel } from '../models/userModel';

export interface ProfileFormData {
  firstname: string;
  lastname: string;
  email: string;
  password?: string;
}

interface SaveResponse {
  success: boolean;
  errors?: string[];
}

@Injectable({ providedIn: 'root' })
export class UserService {
  constructor(private http: HttpClient) {}

  getUsers(): Observable<UserModel[]> {
    return this.http.get<UserModel[]>(`${API_BASE_URL}/RestAPI.php`);
  }

  getById(id: number): Observable<UserModel> {
    return this.http.get<UserModel>(`${API_BASE_URL}/RestAPI.php`, { params: { id } });
  }

  updateProfile(id: number, data: ProfileFormData): Observable<SaveResponse> {
    return this.http.put<SaveResponse>(
      `${API_BASE_URL}/RestAPI.php?id=${id}`,
      toFormBody(data),
      formUrlEncodedHeaders()
    );
  }

  delete(id: number): Observable<SaveResponse> {
    return this.http.delete<SaveResponse>(`${API_BASE_URL}/RestAPI.php?id=${id}`);
  }
}
