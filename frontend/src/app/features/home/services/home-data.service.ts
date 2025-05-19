import { Injectable } from '@angular/core';
import { Observable, forkJoin } from 'rxjs';
import { map } from 'rxjs/operators';
import { Book } from '../../search/models/book.interface';
import { ApiService } from 'src/app/core/services/api.service';

@Injectable({ providedIn: 'root' })
export class HomeDataService {
  private homeDataCache: { latestBooks: Book[]; recommendedBooks: Book[]; dealsBooks: Book[] } | null = null;

  constructor(private api: ApiService) {}

  getHomeData(): Observable<{ latestBooks: Book[]; recommendedBooks: Book[]; dealsBooks: Book[] }> {
    if (this.homeDataCache) {
      return new Observable(observer => {
        observer.next(this.homeDataCache!);
        observer.complete();
      });
    }
    return forkJoin({
      latestBooks: this.api.searchBooks('nouveautés').pipe(map((res: any) => res)),
      recommendedBooks: this.api.searchBooks('recommandé').pipe(map((res: any) => res)),
      dealsBooks: this.api.searchBooks('deals').pipe(map((res: any) => res)),
    }).pipe(
      map(data => {
        this.homeDataCache = data;
        return data;
      })
    );
  }

  refreshHomeData(): Observable<{ latestBooks: Book[]; recommendedBooks: Book[]; dealsBooks: Book[] }> {
    return forkJoin({
      latestBooks: this.api.searchBooks('nouveautés').pipe(map((res: any) => res)),
      recommendedBooks: this.api.searchBooks('recommandé').pipe(map((res: any) => res)),
      dealsBooks: this.api.searchBooks('deals').pipe(map((res: any) => res)),
    }).pipe(
      map(data => {
        this.homeDataCache = data;
        return data;
      })
    );
  }
} 