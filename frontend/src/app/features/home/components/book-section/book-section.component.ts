import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { Book } from '../../../search/models/book.interface';
import { BookCardComponent } from '../book-card/book-card.component';

@Component({
  standalone: true,
  selector: 'app-book-section',
  templateUrl: './book-section.component.html',
  styleUrls: ['./book-section.component.scss'],
  imports: [CommonModule, IonicModule, BookCardComponent]
})
export class BookSectionComponent {
  @Input() title = '';
  @Input() books: Book[] = [];

  get limitedBooks(): Book[] {
    return this.books.slice(0, 10);
  }
} 