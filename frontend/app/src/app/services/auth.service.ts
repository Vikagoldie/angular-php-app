import { Injectable, computed, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { API_BASE_URL } from './api-config';
import { toFormBody, formUrlEncodedHeaders } from './http-helpers';

export interface CurrentUser {
  user_id: number;
  role: string;
  firstname: string;
}

interface MeResponse {
  success: boolean;
  loggedIn: boolean;
  user?: CurrentUser;
}

interface LoginResponse {
  success: boolean;
  errors?: string[];
  user?: {
    user_id: number;
    firstname: string;
    lastname: string;
    email: string;
    role_name: string;
  };
}

interface SimpleResponse {
  success: boolean;
  errors?: string[];
}

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly currentUserSignal = signal<CurrentUser | null>(null);

  readonly currentUser = this.currentUserSignal.asReadonly();
  readonly isLoggedIn = computed(() => this.currentUserSignal() !== null);
  readonly isAdmin = computed(() => this.currentUserSignal()?.role === 'admin');

  constructor(private http: HttpClient) {}

  loadCurrentUser(): Observable<MeResponse> {
    return this.http.get<MeResponse>(`${API_BASE_URL}/AuthAPI.php?action=me`).pipe(
      tap((response) => this.currentUserSignal.set(response.user ?? null))
    );
  }

  login(email: string, password: string): Observable<LoginResponse> {
    return this.http
      .post<LoginResponse>(
        `${API_BASE_URL}/AuthAPI.php?action=login`,
        toFormBody({ email, password }),
        formUrlEncodedHeaders()
      )
      .pipe(
        tap((response) => {
          if (response.success && response.user) {
            this.currentUserSignal.set({
              user_id: response.user.user_id,
              role: response.user.role_name,
              firstname: response.user.firstname,
            });
          }
        })
      );
  }

  register(data: { firstname: string; lastname: string; email: string; password: string }): Observable<SimpleResponse> {
    return this.http.post<SimpleResponse>(
      `${API_BASE_URL}/AuthAPI.php?action=register`,
      toFormBody(data),
      formUrlEncodedHeaders()
    );
  }

  logout(): Observable<SimpleResponse> {
    return this.http
      .post<SimpleResponse>(`${API_BASE_URL}/AuthAPI.php?action=logout`, '')
      .pipe(tap(() => this.currentUserSignal.set(null)));
  }
}
