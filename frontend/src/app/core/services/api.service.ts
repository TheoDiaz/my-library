import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, from } from 'rxjs';
import { switchMap } from 'rxjs/operators';
import { environment } from '../../../environments/environment';
import { StorageService } from './storage.service';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private apiUrl = environment.apiUrl;

  constructor(
    private http: HttpClient,
    private storageService: StorageService
  ) {}

  private async getHeaders(): Promise<HttpHeaders> {
    const token = await this.storageService.getToken();
    return new HttpHeaders({
      'Content-Type': 'application/json',
      ...(token ? { Authorization: `Bearer ${token}` } : {})
    });
  }

  // Recherche de livres
  searchBooks(query: string): Observable<any> {
    return from(this.getHeaders()).pipe(
      switchMap(headers => 
        this.http.get(`${this.apiUrl}/api/openlibrary/search`, {
          params: { q: query },
          headers
        })
      )
    );
  }

  // Liste des livres
  getBooks(): Observable<any> {
    return from(this.getHeaders()).pipe(
      switchMap(headers => 
        this.http.get(`${this.apiUrl}/api/books`, { headers })
      )
    );
  }

  // Détails d'un livre
  getBook(id: string): Observable<any> {
    return from(this.getHeaders()).pipe(
      switchMap(headers => 
        this.http.get(`${this.apiUrl}/api/books/${id}`, { headers })
      )
    );
  }

  // Ajout d'un livre
  addBook(book: any): Observable<any> {
    return from(this.getHeaders()).pipe(
      switchMap(headers => 
        this.http.post(`${this.apiUrl}/api/books`, book, { headers })
      )
    );
  }

  // Suppression d'un livre
  deleteBook(id: string): Observable<any> {
    return from(this.getHeaders()).pipe(
      switchMap(headers => 
        this.http.delete(`${this.apiUrl}/api/books/${id}`, { headers })
      )
    );
  }
} 