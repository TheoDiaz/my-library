import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { tap, catchError } from 'rxjs/operators';

export interface AddBookToLibraryDto {
  googleBooksId: string;
  status?: 'to_read' | 'reading' | 'read';
  startDate?: string;
  endDate?: string;
  rating?: number;
  comments?: string;
}

export interface AddBookToWishlistDto {
  googleBooksId: string;
}

export interface UpdateBookStatusDto {
  id: number;
  status: 'to_read' | 'reading' | 'read';
}

@Injectable({
  providedIn: 'root'
})
export class LibraryService {
  private apiUrl = `${environment.apiUrl}/api`;

  constructor(private http: HttpClient) {}

  getLibraryBooks(): Observable<any> {
    console.log('LibraryService - Récupération des livres de la bibliothèque');
    return this.http.get(`${this.apiUrl}/librarybooks`).pipe(
      tap(response => console.log('LibraryService - Réponse de la bibliothèque:', response)),
      catchError(error => {
        console.error('LibraryService - Erreur lors de la récupération:', error);
        throw error;
      })
    );
  }

  addBookToLibrary(book: AddBookToLibraryDto): Observable<any> {
    console.log('LibraryService - Ajout du livre à la bibliothèque:', book);
    return this.http.post(`${this.apiUrl}/library/add`, book).pipe(
      tap(response => console.log('LibraryService - Réponse de l\'ajout:', response)),
      catchError(error => {
        console.error('LibraryService - Erreur lors de l\'ajout:', error);
        throw error;
      })
    );
  }

  addBookToWishlist(book: AddBookToWishlistDto): Observable<any> {
    console.log('LibraryService - Ajout du livre à la wishlist:', book);
    return this.http.post(`${this.apiUrl}/wishlist/add`, book).pipe(
      tap(response => console.log('LibraryService - Réponse de l\'ajout à la wishlist:', response)),
      catchError(error => {
        console.error('LibraryService - Erreur lors de l\'ajout à la wishlist:', error);
        throw error;
      })
    );
  }

  updateBookStatus(update: UpdateBookStatusDto): Observable<any> {
    console.log('LibraryService - Mise à jour du statut:', update);
    return this.http.patch(`${this.apiUrl}/library/update-status/${update.id}`, { status: update.status }).pipe(
      tap(response => console.log('LibraryService - Réponse de la mise à jour:', response)),
      catchError(error => {
        console.error('LibraryService - Erreur lors de la mise à jour:', error);
        throw error;
      })
    );
  }

  removeBookFromLibrary(googleBooksId: string): Observable<any> {
    console.log('LibraryService - Suppression du livre de la bibliothèque:', googleBooksId);
    return this.http.delete(`${this.apiUrl}/library/remove/${googleBooksId}`).pipe(
      tap(response => console.log('LibraryService - Réponse de la suppression:', response)),
      catchError(error => {
        console.error('LibraryService - Erreur lors de la suppression:', error);
        throw error;
      })
    );
  }

  removeBookFromWishlist(googleBooksId: string): Observable<any> {
    console.log('LibraryService - Suppression du livre de la wishlist:', googleBooksId);
    return this.http.delete(`${this.apiUrl}/wishlist/remove/${googleBooksId}`).pipe(
      tap(response => console.log('LibraryService - Réponse de la suppression:', response)),
      catchError(error => {
        console.error('LibraryService - Erreur lors de la suppression:', error);
        throw error;
      })
    );
  }

  getWishlistBooks(): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/wishlist`).pipe(
      tap(response => console.log('LibraryService - Réponse de la wishlist:', response)),
      catchError(error => {
        console.error('LibraryService - Erreur lors de la récupération de la wishlist:', error);
        throw error;
      })
    );
  }
} 