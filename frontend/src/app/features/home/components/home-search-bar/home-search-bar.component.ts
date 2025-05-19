import { Component, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonSearchbar, IonSpinner, IonText } from '@ionic/angular/standalone';
import { ApiService } from 'src/app/core/services/api.service';
import { debounceTime, distinctUntilChanged, switchMap, of, Subject, tap, catchError } from 'rxjs';
import { Router } from '@angular/router';
import { Book } from '../../../../features/search/models/book.interface';

@Component({
  selector: 'app-home-search-bar',
  standalone: true,
  imports: [CommonModule, FormsModule, IonSearchbar, IonSpinner, IonText],
  templateUrl: './home-search-bar.component.html',
  styleUrls: ['./home-search-bar.component.scss']
})
export class HomeSearchBarComponent {
  @Output() bookSelected = new EventEmitter<Book>();

  searchTerm = '';
  results: Book[] = [];
  loading = false;

  private searchSubject = new Subject<string>();

  constructor(private apiService: ApiService, private router: Router) {
    console.log('HomeSearchBarComponent instancié');
    this.searchSubject.pipe(
      debounceTime(500),
      distinctUntilChanged(),
      switchMap(term => {
        if (!term || term.length < 3) {
          console.log('Terme de recherche trop court:', term);
          this.results = [];
          return of([]);
        }
        console.log('Envoi de la requête de recherche pour:', term);
        this.loading = true;
        return this.apiService.searchBooks(term).pipe(
          tap(response => console.log('Réponse reçue:', response)),
          catchError(error => {
            console.error('Erreur lors de la recherche:', error);
            return of([]);
          })
        );
      })
    ).subscribe({
      next: (books) => {
        console.log('Résultat API recherche livres :', books);
        this.results = books;
        this.loading = false;
      },
      error: (error) => {
        console.error('Erreur dans le flux de recherche:', error);
        this.results = [];
        this.loading = false;
      }
    });
  }

  onInput(event: any) {
    const value = event.target.value;
    console.log('onInput déclenché, value =', value);
    this.searchSubject.next(value);
  }

  selectBook(book: Book) {
    if (!book.id) {
      console.error('Le livre n\'a pas d\'identifiant:', book);
      return;
    }
    this.router.navigate(['/tabs/livres/details', book.id]);
    this.results = [];
    this.searchTerm = book.title;
  }
} 