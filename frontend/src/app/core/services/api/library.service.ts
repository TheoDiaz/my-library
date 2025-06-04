import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
import { BaseApiService } from './base-api.service';
import { Book, LibraryBookInfo } from '../../models/book.model';

@Injectable({
  providedIn: 'root'
})
export class LibraryService extends BaseApiService {
  constructor(http: HttpClient) {
    super(http);
  }

  getLibraryBooks(): Observable<Book[]> {
    return this.get<Book[]>('/api/library/books');
  }

  addBookToLibrary(bookId: string): Observable<Book> {
    return this.post<Book>('/api/library/books', { bookId });
  }

  removeBookFromLibrary(bookId: string): Observable<void> {
    return this.delete<void>(`/api/library/books/${bookId}`);
  }

  getBookLibraryInfo(bookId: string): Observable<LibraryBookInfo> {
    return this.get<LibraryBookInfo>(`/api/library/book-link/${bookId}`);
  }

  updateBookStatus(bookId: string, status: string): Observable<Book> {
    return this.put<Book>(`/api/library/books/${bookId}/status`, { status });
  }

  getReadingHistory(): Observable<Book[]> {
    return this.get<Book[]>('/api/library/history');
  }

  getFavorites(): Observable<Book[]> {
    return this.get<Book[]>('/api/library/favorites');
  }

  addToFavorites(bookId: string): Observable<void> {
    return this.post<void>(`/api/library/favorites/${bookId}`, {});
  }

  removeFromFavorites(bookId: string): Observable<void> {
    return this.delete<void>(`/api/library/favorites/${bookId}`);
  }
} 