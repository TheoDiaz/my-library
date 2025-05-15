import { Component, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonSearchbar, IonList, IonItem, IonLabel, IonSpinner, IonText } from '@ionic/angular/standalone';
import { OpenLibraryService } from 'src/app/core/services/open-library.service';
import { debounceTime, distinctUntilChanged, switchMap, of, Subject } from 'rxjs';

@Component({
  selector: 'app-home-search-bar',
  standalone: true,
  imports: [CommonModule, FormsModule, IonSearchbar, IonList, IonItem, IonLabel, IonSpinner, IonText],
  templateUrl: './home-search-bar.component.html',
  styleUrls: ['./home-search-bar.component.scss']
})
export class HomeSearchBarComponent {
  @Output() bookSelected = new EventEmitter<any>();

  searchTerm = '';
  results: any[] = [];
  loading = false;

  private searchSubject = new Subject<string>();

  constructor(private openLibrary: OpenLibraryService) {
    this.searchSubject.pipe(
      debounceTime(300),
      distinctUntilChanged(),
      switchMap(term => {
        if (!term || term.length < 2) {
          this.results = [];
          return of([]);
        }
        this.loading = true;
        return this.openLibrary.searchBooks(term);
      })
    ).subscribe({
      next: (books) => {
        this.results = books;
        this.loading = false;
      },
      error: () => {
        this.results = [];
        this.loading = false;
      }
    });
  }

  onInput(event: any) {
    const value = event.target.value;
    this.searchSubject.next(value);
  }

  selectBook(book: any) {
    this.bookSelected.emit(book);
    this.results = [];
    this.searchTerm = book.title;
  }
} 