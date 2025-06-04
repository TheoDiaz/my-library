import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, from } from 'rxjs';
import { switchMap } from 'rxjs/operators';
import { environment } from '../../../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class BaseApiService {
  protected apiUrl = environment.apiUrl;

  constructor(protected http: HttpClient) {}

  protected async getHeaders(): Promise<HttpHeaders> {
    return new HttpHeaders({
      'Content-Type': 'application/json'
    });
  }

  protected get<T>(endpoint: string): Observable<T> {
    return from(this.getHeaders()).pipe(
      switchMap(headers => 
        this.http.get<T>(`${this.apiUrl}${endpoint}`, { headers })
      )
    );
  }

  protected post<T>(endpoint: string, data: any): Observable<T> {
    return from(this.getHeaders()).pipe(
      switchMap(headers => 
        this.http.post<T>(`${this.apiUrl}${endpoint}`, data, { headers })
      )
    );
  }

  protected put<T>(endpoint: string, data: any): Observable<T> {
    return from(this.getHeaders()).pipe(
      switchMap(headers => 
        this.http.put<T>(`${this.apiUrl}${endpoint}`, data, { headers })
      )
    );
  }

  protected delete<T>(endpoint: string): Observable<T> {
    return from(this.getHeaders()).pipe(
      switchMap(headers => 
        this.http.delete<T>(`${this.apiUrl}${endpoint}`, { headers })
      )
    );
  }
} 