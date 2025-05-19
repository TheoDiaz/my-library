import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, from } from 'rxjs';
import { switchMap, tap, catchError } from 'rxjs/operators';
import { environment } from '../../../environments/environment';
import { GoogleBooksService } from './google-books.service';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private apiUrl = environment.apiUrl;

  constructor(
    private http: HttpClient,
    private googleBooksService: GoogleBooksService
  ) {}

  private async getHeaders(): Promise<{ [key: string]: string }> {
    return {
      'Content-Type': 'application/json'
    };
  }

  // Recherche de livres
  searchBooks(query: string, lang: string = 'fr', maxResults: number = 20): Observable<any> {
    console.log('ApiService - Appel à searchBooks avec query:', query);
    return this.googleBooksService.searchBooks(query, lang, maxResults).pipe(
      tap(response => console.log('ApiService - Réponse de Google Books:', response)),
      catchError(error => {
        console.error('ApiService - Erreur lors de la recherche:', error);
        throw error;
      })
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

  // Détail d'un livre
  getBookDetails(id: string): Observable<any> {
    // On tente d'abord de récupérer le livre dans la BDD locale
    return this.http.get(`${this.apiUrl}/api/books/${id}`).pipe(
      catchError(err => {
        // Si le livre n'est pas trouvé en BDD (404), fallback sur Google Books
        if (err.status === 404) {
          return this.googleBooksService.getBookDetails(id);
        }
        // Sinon, propager l'erreur
        throw err;
      })
    );
  }
} 