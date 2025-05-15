import { Component, OnInit, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButtons,
  IonButton,
  IonBackButton,
  IonSpinner,
  IonText
} from '@ionic/angular/standalone';
import { HomeDataService } from '../../services/home-data.service';
import { Book } from '../../../search/models/book.interface';
import { HomeSearchBarComponent } from '../../components/home-search-bar/home-search-bar.component';
import { BookSectionComponent } from '../../components/book-section/book-section.component';

@Component({
  standalone: true,
  selector: 'app-home',
  templateUrl: './home.page.html',
  styleUrls: ['./home.page.scss'],
  imports: [
    CommonModule,
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
    IonButtons,
    IonButton,
    IonBackButton,
    IonSpinner,
    IonText,
    HomeSearchBarComponent,
    BookSectionComponent
  ],
  schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class HomePage implements OnInit {
  latestBooks: Book[] = [];
  recommendedBooks: Book[] = [];
  dealsBooks: Book[] = [];
  loading = true;
  error: string | null = null;

  constructor(private homeData: HomeDataService) {}

  ngOnInit() {
    this.homeData.getHomeData().subscribe({
      next: (data) => {
        this.latestBooks = data.latestBooks;
        this.recommendedBooks = data.recommendedBooks;
        this.dealsBooks = data.dealsBooks;
        this.loading = false;
      },
      error: (err) => {
        this.error = 'Erreur lors du chargement de la page d\'accueil';
        this.loading = false;
      }
    });
  }
} 