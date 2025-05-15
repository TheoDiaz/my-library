import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { environment } from '../../../environments/environment';
import { StorageService } from './storage.service';
import { BehaviorSubject, Observable, tap, from } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = environment.apiUrl;
  private isAuthenticatedSubject = new BehaviorSubject<boolean>(false);
  public isAuthenticated$ = this.isAuthenticatedSubject.asObservable();
  private isAuthReadySubject = new BehaviorSubject<boolean>(false);
  public isAuthReady$ = this.isAuthReadySubject.asObservable();

  constructor(
    private http: HttpClient,
    private storage: StorageService,
    private router: Router
  ) {
    this.initializeAuthState();
  }

  private async initializeAuthState() {
    const token = await this.storage.getToken();
    this.isAuthenticatedSubject.next(!!token);
    this.isAuthReadySubject.next(true);
  }

  login(email: string, password: string): Observable<{ token: string }> {
    return this.http.post<{ token: string }>(`${this.apiUrl}/api/login_check`, {
      email,
      password
    }).pipe(
      tap(response => {
        this.setToken(response.token);
      })
    );
  }

  async setToken(token: string) {
    await this.storage.setToken(token);
    this.isAuthenticatedSubject.next(true);
  }

  async logout() {
    await this.storage.removeToken();
    this.isAuthenticatedSubject.next(false);
    this.router.navigate(['/auth/login']);
  }

  isAuthenticated(): boolean {
    return this.isAuthenticatedSubject.value;
  }

  // Pour l'inscription (register)
  register(data: { email: string; password: string; username?: string }) {
    return this.http.post(`${this.apiUrl}/api/register`, data);
  }
} 