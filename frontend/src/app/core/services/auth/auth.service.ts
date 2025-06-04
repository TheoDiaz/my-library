import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, BehaviorSubject, from } from 'rxjs';
import { tap, catchError, switchMap, map } from 'rxjs/operators';
import { BaseApiService } from '../api/base-api.service';
import { StorageService } from '../storage/storage.service';
import { environment } from '../../../../environments/environment';

export interface User {
  id: number;
  email: string;
  name: string;
  role: string;
}

export interface AuthResponse {
  user: User;
  token: string;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService extends BaseApiService {
  private currentUserSubject = new BehaviorSubject<User | null>(null);
  public currentUser$ = this.currentUserSubject.asObservable();

  constructor(
    http: HttpClient,
    private storageService: StorageService
  ) {
    super(http);
    this.loadStoredUser();
  }

  private async loadStoredUser(): Promise<void> {
    const token = await this.storageService.get(environment.storageKeys.token);
    if (token) {
      this.getCurrentUser().subscribe();
    }
  }

  login(email: string, password: string): Observable<AuthResponse> {
    return this.post<AuthResponse>('/api/auth/login', { email, password }).pipe(
      tap(response => {
        this.storageService.set(environment.storageKeys.token, response.token);
        this.currentUserSubject.next(response.user);
      })
    );
  }

  register(name: string, email: string, password: string): Observable<AuthResponse> {
    return this.post<AuthResponse>('/api/auth/register', { name, email, password }).pipe(
      tap(response => {
        this.storageService.set(environment.storageKeys.token, response.token);
        this.currentUserSubject.next(response.user);
      })
    );
  }

  logout(): void {
    this.storageService.remove(environment.storageKeys.token);
    this.currentUserSubject.next(null);
  }

  getCurrentUser(): Observable<User> {
    return this.get<User>('/api/auth/me').pipe(
      tap(user => this.currentUserSubject.next(user))
    );
  }

  isAuthenticated(): Observable<boolean> {
    return from(this.storageService.get(environment.storageKeys.token)).pipe(
      switchMap(token => {
        if (!token) {
          return from([false]);
        }
        return this.getCurrentUser().pipe(
          map(() => true),
          catchError(() => from([false]))
        );
      })
    );
  }
} 