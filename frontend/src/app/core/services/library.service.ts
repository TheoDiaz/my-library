import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { tap, catchError } from 'rxjs/operators';

export interface AddBookToLibraryDto {
  googleBooksId: string;
  startDate?: string;
  endDate?: string;
  rating?: number;
  comments?: string;
}

@Injectable({
  providedIn: 'root'
})
export class LibraryService {
  private apiUrl = `${environment.apiUrl}/api/librarybooks`;

  constructor(private http: HttpClient) {}

  getLibraryBooks(): Observable<any> {
    console.log('LibraryService - Récupération des livres de la bibliothèque');
    return this.http.get(this.apiUrl).pipe(
      tap(response => console.log('LibraryService - Réponse de la bibliothèque:', response)),
      catchError(error => {
        console.error('LibraryService - Erreur lors de la récupération:', error);
        throw error;
      })
    );
  }

  addBookToLibrary(book: AddBookToLibraryDto): Observable<any> {
    console.log('LibraryService - Ajout du livre à la bibliothèque:', book);
    return this.http.post(`${this.apiUrl}/add`, book).pipe(
      tap(response => console.log('LibraryService - Réponse de l\'ajout:', response)),
      catchError(error => {
        console.error('LibraryService - Erreur lors de l\'ajout:', error);
        throw error;
      })
    );
  }
} 