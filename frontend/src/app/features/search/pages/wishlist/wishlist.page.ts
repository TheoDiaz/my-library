import { Component, OnInit } from '@angular/core';
import { FormControl } from '@angular/forms';
import { debounceTime, distinctUntilChanged } from 'rxjs';
import { LibraryService } from 'src/app/core/services/library.service';

@Component({
  selector: 'app-wishlist',
  templateUrl: './wishlist.page.html',
  styleUrls: ['./wishlist.page.scss'],
  standalone: true
})
export class WishlistPage implements OnInit {
  searchControl = new FormControl('');
  wishlistBooks: any[] = [];
  filteredBooks: any[] = [];
  loading = false;
  error: string | null = null;

  constructor(private libraryService: LibraryService) {}

  ngOnInit() {
    this.fetchWishlistBooks();
    this.searchControl.valueChanges.pipe(
      debounceTime(200),
      distinctUntilChanged()
    ).subscribe(query => {
      this.filterBooks(query || '');
    });
  }

  fetchWishlistBooks() {
    this.loading = true;
    this.libraryService.getWishlistBooks().subscribe({
      next: (results: any) => {
        this.wishlistBooks = results || [];
        this.filteredBooks = this.wishlistBooks;
        this.loading = false;
      },
      error: (err: any) => {
        this.error = 'Erreur lors du chargement de la wishlist';
        this.loading = false;
      }
    });
  }

  filterBooks(query: string) {
    if (!query) {
      this.filteredBooks = this.wishlistBooks;
      return;
    }
    const lower = query.toLowerCase();
    this.filteredBooks = this.wishlistBooks.filter(book =>
      (book.title && book.title.toLowerCase().includes(lower)) ||
      (book.author && book.author.toLowerCase().includes(lower))
    );
  }
} 