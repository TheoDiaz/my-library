import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { FormsModule, ReactiveFormsModule, FormControl } from '@angular/forms';
import { RouterModule, Router } from '@angular/router';
import { debounceTime, distinctUntilChanged } from 'rxjs';
import { Book } from '../../models/book.interface';
import { LibraryService } from '../../../../core/services/library.service';
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
export class SearchPage implements OnInit {
  searchControl = new FormControl('');
  libraryBooks: any[] = [];
  filteredBooks: any[] = [];
  loading = false;
  error: string | null = null;
  private apiUrl = environment.apiUrl;

  constructor(private libraryService: LibraryService, private router: Router) {}

  ngOnInit() {
    this.fetchLibraryBooks();
    this.searchControl.valueChanges.pipe(
      debounceTime(200),
      distinctUntilChanged()
    ).subscribe(query => {
      this.filterBooks(query || '');
    });
  }

  fetchLibraryBooks() {
    this.loading = true;
    this.libraryService.getLibraryBooks().subscribe({
      next: (results: any) => {
        this.libraryBooks = results.member || [];
        this.filteredBooks = this.libraryBooks;
        this.loading = false;
      },
      error: (err: any) => {
        this.error = 'Erreur lors du chargement de la bibliothèque';
        this.loading = false;
      }
    });
  }

  filterBooks(query: string) {
    if (!query) {
      this.filteredBooks = this.libraryBooks;
      return;
    }
    const lower = query.toLowerCase();
    this.filteredBooks = this.libraryBooks.filter(lb => {
      const book = lb.book || lb; // selon la structure API Platform
      return (
        (book.title && book.title.toLowerCase().includes(lower)) ||
        (book.author && book.author.toLowerCase().includes(lower))
      );
    });
  }

  getBookCover(lb: any): string | null {
    const book = lb.book || lb;
    const cover = book.coverId || book.cover;
    if (!cover) return null;
    return cover.startsWith('http') ? cover : `${this.apiUrl}${cover}`;
  }

  getBookTitle(lb: any): string {
    const book = lb.book || lb;
    return book.title || 'Nom';
  }

  getBookAuthor(lb: any): string {
    const book = lb.book || lb;
    return book.author || 'Auteur';
  }

  onBookSelected(lb: any) {
    const book = lb.book || lb;
    if (book && book.id) {
      this.router.navigate(['/tabs/livres/details', book.googleBooksId || book.id]);
    }
  }
} 