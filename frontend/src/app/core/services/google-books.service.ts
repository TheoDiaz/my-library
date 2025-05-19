import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { Book } from '../../features/search/models/book.interface';
import { environment } from '../../../environments/environment';

@Injectable({ providedIn: 'root' })
export class GoogleBooksService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  searchBooks(query: string, lang: string = 'fr', maxResults: number = 20): Observable<Book[]> {
    console.log('GoogleBooksService - URL de base:', this.apiUrl);
    return this.http.get<any>(`${this.apiUrl}/api/googlebooks/search`, {
      params: {
        q: query,
        maxResults: maxResults.toString(),
        lang
      }
    }).pipe(
      map(response => {
        console.log('GoogleBooksService - Réponse brute:', response);
        if (Array.isArray(response)) {
          return response.map(book => this.mapGoogleBook(book));
        }
        return [];
      })
    );
  }

  getBookDetails(id: string): Observable<Book> {
    console.log('GoogleBooksService - Récupération des détails du livre:', id);
    return this.http.get<any>(`${this.apiUrl}/api/googlebooks/details/${id}`).pipe(
      map(response => {
        console.log('GoogleBooksService - Réponse brute des détails:', response);
        return this.mapGoogleBook(response);
      })
    );
  }

  private mapGoogleBook(googleBook: any): Book {
    return {
      id: googleBook.id,
      title: googleBook.title,
      author: googleBook.authors?.[0] || 'Auteur inconnu',
      description: googleBook.description || 'Pas de description disponible',
      cover: googleBook.cover
        ? (googleBook.cover.startsWith('http') ? googleBook.cover : `${this.apiUrl}${googleBook.cover}`)
        : null,
      first_publish_year: googleBook.publishedDate ? new Date(googleBook.publishedDate).getFullYear() : null,
      edition_count: 1,
      publisher: googleBook.publisher || '',
      pageCount: googleBook.pageCount || null,
      language: googleBook.language || '',
      categories: googleBook.categories || [],
      isbn: googleBook.industryIdentifiers?.[0]?.identifier || null,
      publishedDate: googleBook.publishedDate,
      previewLink: googleBook.previewLink,
      infoLink: googleBook.infoLink,
      industryIdentifiers: googleBook.industryIdentifiers
    };
  }
} 