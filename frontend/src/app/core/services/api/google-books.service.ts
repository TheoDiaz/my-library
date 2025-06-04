import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
import { environment } from '../../../../environments/environment';
import { BaseApiService } from './base-api.service';
import { Book, BookSearchParams } from '../../models/book.model';

@Injectable({
  providedIn: 'root'
})
export class GoogleBooksService extends BaseApiService {
  private readonly googleBooksApiUrl = 'https://www.googleapis.com/books/v1/volumes';

  constructor(http: HttpClient) {
    super(http);
  }

  searchBooks(params: BookSearchParams): Observable<Book[]> {
    const { query, lang = 'fr', maxResults = 20 } = params;
    const url = `${this.googleBooksApiUrl}?q=${encodeURIComponent(query)}&langRestrict=${lang}&maxResults=${maxResults}`;
    
    return this.http.get<any>(url).pipe(
      map(response => this.mapGoogleBooksResponse(response)),
      catchError(error => {
        console.error('Erreur lors de la recherche Google Books:', error);
        throw error;
      })
    );
  }

  getBookDetails(id: string): Observable<Book> {
    return this.http.get<any>(`${this.googleBooksApiUrl}/${id}`).pipe(
      map(response => this.mapGoogleBookToBook(response)),
      catchError(error => {
        console.error('Erreur lors de la récupération des détails du livre:', error);
        throw error;
      })
    );
  }

  private mapGoogleBooksResponse(response: any): Book[] {
    if (!response.items) {
      return [];
    }
    return response.items.map((item: any) => this.mapGoogleBookToBook(item));
  }

  private mapGoogleBookToBook(googleBook: any): Book {
    const volumeInfo = googleBook.volumeInfo || {};
    return {
      id: googleBook.id,
      title: volumeInfo.title || '',
      authors: volumeInfo.authors || [],
      description: volumeInfo.description || '',
      publishedDate: volumeInfo.publishedDate,
      publisher: volumeInfo.publisher,
      imageLinks: volumeInfo.imageLinks || {},
      language: volumeInfo.language,
      pageCount: volumeInfo.pageCount,
      categories: volumeInfo.categories || [],
      averageRating: volumeInfo.averageRating,
      ratingsCount: volumeInfo.ratingsCount
    };
  }
} 