import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable, from, switchMap, of } from 'rxjs';
import { environment } from '../../../environments/environment';
import { ApiService } from './api.service';
import { StorageService } from './storage.service';
import { Preferences } from '@capacitor/preferences';

@Injectable({
  providedIn: 'root'
})
export class NYTService {
  private readonly CACHE_KEY = 'nyt_bestsellers_cache';
  private readonly CACHE_DURATION = 24 * 60 * 60 * 1000; // 24 heures en millisecondes

  constructor(
    private http: HttpClient,
    private apiService: ApiService,
    private storage: StorageService
  ) {}

  getBestSellers(forceRefresh: boolean = false): Observable<any> {
    return from(this.storage.getToken()).pipe(
      switchMap(token => {
        if (!forceRefresh) {
          return from(this.getCachedBestSellers()).pipe(
            switchMap(cachedData => {
              if (cachedData) {
                console.log('[NYTService] Utilisation des données en cache');
                return of(cachedData);
              }
              return this.fetchBestSellers(token);
            })
          );
        }
        return this.fetchBestSellers(token);
      })
    );
  }

  private async getCachedBestSellers(): Promise<any> {
    try {
      const { value } = await Preferences.get({ key: this.CACHE_KEY });
      if (!value) return null;

      const cachedData = JSON.parse(value);
      const now = new Date().getTime();

      if (now - cachedData.timestamp < this.CACHE_DURATION) {
        return cachedData.data;
      }
      return null;
    } catch (error) {
      console.error('[NYTService] Erreur lors de la récupération du cache:', error);
      return null;
    }
  }

  private async cacheBestSellers(data: any): Promise<void> {
    try {
      const cacheData = {
        data,
        timestamp: new Date().getTime()
      };
      await Preferences.set({
        key: this.CACHE_KEY,
        value: JSON.stringify(cacheData)
      });
    } catch (error) {
      console.error('[NYTService] Erreur lors de la mise en cache:', error);
    }
  }

  private fetchBestSellers(token: string | null): Observable<any> {
    const headers = new HttpHeaders({
      'Authorization': token ? `Bearer ${token}` : '',
      'Content-Type': 'application/json'
    });

    return this.http.get(`${environment.apiUrl}/api/nyt/bestsellers`, { headers }).pipe(
      switchMap(response => {
        if (response && 'status' in response && response.status === 'success') {
          return from(this.cacheBestSellers(response)).pipe(
            switchMap(() => of(response))
          );
        }
        return of(response);
      })
    );
  }

  getBookDetails(isbn: string): Observable<any> {
    return from(this.storage.getToken()).pipe(
      switchMap(token => {
        const headers = new HttpHeaders({
          'Authorization': token ? `Bearer ${token}` : '',
          'Content-Type': 'application/json'
        });
        return this.http.get(`${environment.apiUrl}/api/nyt/book/${isbn}`, { headers });
      })
    );
  }
} 