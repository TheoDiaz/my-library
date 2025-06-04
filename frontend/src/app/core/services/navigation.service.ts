import { Injectable } from '@angular/core';
import { Router, NavigationEnd } from '@angular/router';
import { filter } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class NavigationService {
  private previousUrl: string = '';
  private currentUrl: string = '';

  constructor(private router: Router) {
    this.router.events.pipe(
      filter(event => event instanceof NavigationEnd)
    ).subscribe((event: any) => {
      this.previousUrl = this.currentUrl;
      this.currentUrl = event.url;
      console.log('NavigationService - Previous URL:', this.previousUrl);
      console.log('NavigationService - Current URL:', this.currentUrl);
    });
  }

  getPreviousUrl(): string {
    return this.previousUrl;
  }

  getCurrentUrl(): string {
    return this.currentUrl;
  }
} 