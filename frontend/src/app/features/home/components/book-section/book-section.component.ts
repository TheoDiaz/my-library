import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { Book } from '../../../search/models/book.interface';

@Component({
  standalone: true,
  selector: 'app-book-section',
  templateUrl: './book-section.component.html',
  styleUrls: ['./book-section.component.scss'],
  imports: [CommonModule, IonicModule]
})
export class BookSectionComponent {
  @Input() title = '';
  @Input() books: Book[] = [];
  @Output() bookSelected = new EventEmitter<Book>();

  get limitedBooks(): Book[] {
    return this.books.slice(0, 10);
  }

  onBookClick(book: Book) {
    console.log('BookSection - Clic sur le livre:', book);
    this.bookSelected.emit(book);
  }
} 