import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class BookEventsService {
  private libraryUpdateSubject = new BehaviorSubject<void>(undefined);
  private wishlistUpdateSubject = new BehaviorSubject<void>(undefined);

  libraryUpdate$ = this.libraryUpdateSubject.asObservable();
  wishlistUpdate$ = this.wishlistUpdateSubject.asObservable();

  triggerLibraryUpdate() {
    this.libraryUpdateSubject.next();
  }

  triggerWishlistUpdate() {
    this.wishlistUpdateSubject.next();
  }
} 