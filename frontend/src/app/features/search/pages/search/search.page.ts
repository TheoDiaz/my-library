import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { FormsModule, ReactiveFormsModule, FormControl } from '@angular/forms';
import { RouterModule, Router } from '@angular/router';
import { debounceTime, distinctUntilChanged, switchMap, of } from 'rxjs';
import { ApiService } from '../../../../core/services/api.service';
import { Book } from '../../models/book.interface';
import { HomeSearchBarComponent } from '../../../home/components/home-search-bar/home-search-bar.component';
import { RouterLink } from '@angular/router';
import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';

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
    HomeSearchBarComponent,
    RouterLink
  ]
})
export class SearchPage implements OnInit {
  searchControl = new FormControl('');
  books: Book[] = [];
  loading = false;
  error: string | null = null;

  constructor(private apiService: ApiService, private router: Router) {}

  ngOnInit() {
    this.searchControl.valueChanges.pipe(
      debounceTime(300),
      distinctUntilChanged(),
      switchMap((query) => {
        query = query || '';
        if (!query) {
          this.books = [];
          return of([]);
        }
        this.loading = true;
        this.error = null;
        return this.apiService.searchBooks(query);
      })
    ).subscribe({
      next: (results: Book[]) => {
        this.books = results;
        this.loading = false;
      },
      error: (err: any) => {
        this.error = 'Une erreur est survenue lors de la recherche';
        this.loading = false;
        console.error('Erreur de recherche:', err);
      }
    });
  }

  onSearch(event: any) {
    const query = event.target.value;
    if (!query) {
      this.books = [];
      return;
    }
    this.loading = true;
    this.error = null;
    this.apiService.searchBooks(query).subscribe({
      next: (results: Book[]) => {
        this.books = results;
        this.loading = false;
      },
      error: (err: any) => {
        this.error = 'Une erreur est survenue lors de la recherche';
        this.loading = false;
        console.error('Erreur de recherche:', err);
      }
    });
  }

  onBookSelected(book: Book) {
    if (book && book.id) {
      this.router.navigate(['/search/details', book.id]);
    }
  }
} 