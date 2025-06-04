import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { FormsModule, ReactiveFormsModule, FormControl } from '@angular/forms';
import { RouterModule, Router, NavigationEnd } from '@angular/router';
import { debounceTime, distinctUntilChanged, Subject, takeUntil, filter, merge, first } from 'rxjs';
import { Book } from '../../models/book.interface';
import { LibraryService } from '../../../../core/services/library.service';
import { BookEventsService } from '../../../../core/services/book-events.service';
import { RouterLink } from '@angular/router';
import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-search',
  templateUrl: './search.page.html',
  styleUrls: ['./search.page.scss'],
  standalone: true,
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
  imports: [
    CommonModule,
    IonicModule,
    FormsModule,
    ReactiveFormsModule,
    RouterLink
  ]
})
export class SearchPage implements OnInit, OnDestroy {
  private destroy$ = new Subject<void>();
  private isInitialized = false;

  searchControl = new FormControl('');
  selectedTab: 'library' | 'wishlist' = 'library';

  libraryBooks: any[] = [];
  wishlistBooks: any[] = [];
  filteredLibraryBooks: any[] = [];
  filteredWishlistBooks: any[] = [];

  loadingLibrary = false;
  loadingWishlist = false;
  errorLibrary: string | null = null;
  errorWishlist: string | null = null;
  private apiUrl = environment.apiUrl;

  constructor(
    private libraryService: LibraryService, 
    private router: Router,
    private bookEvents: BookEventsService
  ) {
    // Écouter les changements de route
    this.router.events.pipe(
      takeUntil(this.destroy$),
      filter(event => event instanceof NavigationEnd),
      filter(() => this.isInitialized) // Ne recharger que si déjà initialisé
    ).subscribe((event: any) => {
      if (event.url.includes('/search')) {
        this.loadData();
      }
    });
  }

  ngOnInit() {
    // Chargement initial unique
    if (!this.isInitialized) {
      this.loadData();
      this.isInitialized = true;
    }

    // S'abonner aux événements de mise à jour
    merge(
      this.bookEvents.libraryUpdate$,
      this.bookEvents.wishlistUpdate$
    ).pipe(
      takeUntil(this.destroy$)
    ).subscribe(() => {
      this.loadData();
    });

    this.searchControl.valueChanges
      .pipe(
        takeUntil(this.destroy$),
        debounceTime(200),
        distinctUntilChanged()
      )
      .subscribe(query => {
        this.filterBooks(query || '');
      });
  }

  ngOnDestroy() {
    this.destroy$.next();
    this.destroy$.complete();
  }

  ionViewWillEnter() {
    this.loadData();
  }

  private loadData() {
    console.log('Chargement des données...');
    this.fetchLibraryBooks();
    this.fetchWishlistBooks();
  }

  fetchLibraryBooks() {
    if (this.loadingLibrary) return; // Éviter les appels simultanés
    
    this.loadingLibrary = true;
    this.libraryService.getLibraryBooks()
      .pipe(
        takeUntil(this.destroy$),
        first() // S'assurer qu'on ne reçoit qu'une seule réponse
      )
      .subscribe({
        next: (results: any) => {
          this.libraryBooks = results.member || [];
          this.filteredLibraryBooks = this.libraryBooks;
          this.loadingLibrary = false;
        },
        error: (err: any) => {
          console.error('Erreur bibliothèque:', err);
          this.errorLibrary = 'Erreur lors du chargement de la bibliothèque';
          this.loadingLibrary = false;
        }
      });
  }

  fetchWishlistBooks() {
    if (this.loadingWishlist) return; // Éviter les appels simultanés
    
    this.loadingWishlist = true;
    this.libraryService.getWishlistBooks()
      .pipe(
        takeUntil(this.destroy$),
        first() // S'assurer qu'on ne reçoit qu'une seule réponse
      )
      .subscribe({
        next: (results: any) => {
          this.wishlistBooks = results.member || [];
          this.filteredWishlistBooks = this.wishlistBooks;
          this.loadingWishlist = false;
        },
        error: (err: any) => {
          console.error('Erreur wishlist:', err);
          this.errorWishlist = 'Erreur lors du chargement de la wishlist';
          this.loadingWishlist = false;
        }
      });
  }

  filterBooks(query: string) {
    const lower = query.toLowerCase();
    if (!query) {
      this.filteredLibraryBooks = this.libraryBooks;
      this.filteredWishlistBooks = this.wishlistBooks;
      return;
    }
    this.filteredLibraryBooks = this.libraryBooks.filter(lb => {
      const book = lb.book || lb;
      return (
        (book.title && book.title.toLowerCase().includes(lower)) ||
        (book.author && book.author.toLowerCase().includes(lower))
      );
    });
    this.filteredWishlistBooks = this.wishlistBooks.filter(lb => {
      const book = lb.book || lb;
      return (
        (book.title && book.title.toLowerCase().includes(lower)) ||
        (book.author && book.author.toLowerCase().includes(lower))
      );
    });
  }

  // Pour la wishlist, on accède toujours à item.book
  getBookObject(item: any): any {
    // Pour la bibliothèque : { book: {...} }, pour la wishlist : { book: {...}, ... }
    return item.book ? item.book : item;
  }

  getBookCover(item: any): string | null {
    // Wishlist : item.book
    const book = this.getBookObject(item);
    const cover = book.coverId || book.cover;
    if (!cover) return null;
    return cover.startsWith('http') ? cover : `${this.apiUrl}${cover}`;
  }

  getBookTitle(item: any): string {
    const book = this.getBookObject(item);
    return book.title || 'Nom';
  }

  getBookAuthor(item: any): string {
    const book = this.getBookObject(item);
    return book.author || 'Auteur';
  }

  onBookSelected(item: any) {
    const book = this.getBookObject(item);
    if (book && (book.googleBooksId || book.id)) {
      this.router.navigate(['/livres/details', book.googleBooksId || book.id]);
    }
  }

  removeFromWishlist(item: any) {
    const bookId = this.getBookObject(item).googleBooksId || this.getBookObject(item).id;
    this.libraryService.removeBookFromWishlist(bookId).subscribe({
      next: () => {
        this.fetchWishlistBooks();
      },
      error: () => {
        // Optionnel : afficher un toast d'erreur
      }
    });
  }

  removeFromLibrary(item: any) {
    const bookId = this.getBookObject(item).googleBooksId || this.getBookObject(item).id;
    this.libraryService.removeBookFromLibrary(bookId).subscribe({
      next: () => {
        this.fetchLibraryBooks();
      },
      error: () => {
        // Optionnel : afficher un toast d'erreur
      }
    });
  }
} 