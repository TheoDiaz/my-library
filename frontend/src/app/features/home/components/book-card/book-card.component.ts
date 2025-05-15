import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonCard, IonCardContent } from '@ionic/angular/standalone';
import { Book } from '../../../search/models/book.interface';

@Component({
  standalone: true,
  selector: 'app-book-card',
  templateUrl: './book-card.component.html',
  styleUrls: ['./book-card.component.scss'],
  imports: [CommonModule, IonCard, IonCardContent]
})
export class BookCardComponent {
  @Input() book!: Book;
} 