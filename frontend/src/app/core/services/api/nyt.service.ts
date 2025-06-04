import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map, catchError } from 'rxjs/operators';
import { environment } from '../../../../environments/environment';
import { BaseApiService } from './base-api.service';

export interface NYTBestSeller {
  rank: number;
  title: string;
  author: string;
  description: string;
  isbn: string;
  amazon_product_url: string;
  book_image: string;
}

@Injectable({
  providedIn: 'root'
})
export class NYTService extends BaseApiService {
  private readonly nytApiUrl = 'https://api.nytimes.com/svc/books/v3';
  private readonly apiKey = environment.nytApiKey;

  constructor(http: HttpClient) {
    super(http);
  }

  getBestSellers(): Observable<NYTBestSeller[]> {
    const url = `${this.nytApiUrl}/lists/current/hardcover-fiction.json?api-key=${this.apiKey}`;
    
    return this.http.get<any>(url).pipe(
      map(response => this.mapNYTResponse(response)),
      catchError(error => {
        console.error('Erreur lors de la récupération des best-sellers NYT:', error);
        throw error;
      })
    );
  }

  private mapNYTResponse(response: any): NYTBestSeller[] {
    if (!response.results?.books) {
      return [];
    }

    return response.results.books.map((book: any) => ({
      rank: book.rank,
      title: book.title,
      author: book.author,
      description: book.description,
      isbn: book.primary_isbn13,
      amazon_product_url: book.amazon_product_url,
      book_image: book.book_image
    }));
  }
} 