import { Injectable } from '@angular/core';
import { Observable, forkJoin } from 'rxjs';
import { map } from 'rxjs/operators';
import { Book } from '../../search/models/book.interface';
import { ApiService } from 'src/app/core/services/api.service';

@Injectable({ providedIn: 'root' })
export class HomeDataService {
  constructor(private api: ApiService) {}

  getHomeData(): Observable<{ latestBooks: Book[]; recommendedBooks: Book[]; dealsBooks: Book[] }> {
    // À adapter selon les endpoints réels. Ici, on utilise la recherche avec des requêtes différentes.
    return forkJoin({
      latestBooks: this.api.searchBooks('nouveautés').pipe(map((res: any) => res)),
      recommendedBooks: this.api.searchBooks('recommandé').pipe(map((res: any) => res)),
      dealsBooks: this.api.searchBooks('deals').pipe(map((res: any) => res)),
    });
  }
} 