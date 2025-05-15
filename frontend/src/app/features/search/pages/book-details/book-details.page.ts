import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule, IonHeader, IonToolbar, IonTitle, IonContent, IonButtons, IonBackButton, IonCard, IonCardHeader, IonCardTitle, IonCardSubtitle, IonCardContent, IonList, IonItem, IonLabel, IonChip, IonButton, IonIcon, IonSpinner, IonText } from '@ionic/angular';
import { RouterModule, ActivatedRoute } from '@angular/router';
import { ApiService } from '../../../../core/services/api.service';
import { Book } from '../../models/book.interface';

@Component({
  standalone: true,
  selector: 'app-book-details',
  templateUrl: './book-details.page.html',
  styleUrls: ['./book-details.page.scss'],
  imports: [
    CommonModule,
    IonicModule,
    RouterModule,
    IonHeader, IonToolbar, IonTitle, IonContent, IonButtons, IonBackButton, IonCard, IonCardHeader, IonCardTitle, IonCardSubtitle, IonCardContent, IonList, IonItem, IonLabel, IonChip, IonButton, IonIcon, IonSpinner, IonText
  ]
})
export class BookDetailsPage implements OnInit {
  book: Book | null = null;
  loading = true;
  error: string | null = null;

  constructor(
    private route: ActivatedRoute,
    private apiService: ApiService
  ) {}

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (!id) {
      this.error = 'ID du livre non trouvé';
      this.loading = false;
      return;
    }

    this.apiService.getBook(id).subscribe({
      next: (book) => {
        this.book = book;
        this.loading = false;
      },
      error: (err) => {
        this.error = 'Erreur lors du chargement des détails du livre';
        this.loading = false;
        console.error('Erreur de chargement:', err);
      }
    });
  }

  addToLibrary() {
    if (!this.book) return;
    
    this.apiService.addBook(this.book).subscribe({
      next: () => {
        // TODO: Afficher un message de succès
        console.log('Livre ajouté à la bibliothèque');
      },
      error: (err) => {
        this.error = 'Erreur lors de l\'ajout du livre à la bibliothèque';
        console.error('Erreur d\'ajout:', err);
      }
    });
  }
} 