import { Component, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { RouterModule } from '@angular/router';
import { HomeSearchBarComponent } from '../../components/home-search-bar/home-search-bar.component';
import { BookSectionComponent } from '../../components/book-section/book-section.component';
import { Book } from '../../../../features/search/models/book.interface';
import { BestSellersComponent } from '../../../../features/best-sellers/best-sellers.component';

@Component({
  selector: 'app-home',
  templateUrl: './home.page.html',
  styleUrls: ['./home.page.scss'],
  standalone: true,
  imports: [
    CommonModule,
    IonicModule,
    RouterModule,
    HomeSearchBarComponent,
    BookSectionComponent,
    BestSellersComponent
  ],
  schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class HomePage {
  recentBooks: Book[] = [];
  toReadBooks: Book[] = [];
  readingBooks: Book[] = [];
  finishedBooks: Book[] = [];

  onBookSelect(book: Book): void {
    // Navigation vers la page de détails du livre
    console.log('Livre sélectionné:', book);
  }
} 