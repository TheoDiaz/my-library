import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { RouterModule, ActivatedRoute } from '@angular/router';
import { ApiService } from '../../../../core/services/api.service';
import { Book } from '../../models/book.interface';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';
import { LibraryService } from '../../../../core/services/library.service';
import { ToastController } from '@ionic/angular';
import { firstValueFrom } from 'rxjs';

@Component({
  standalone: true,
  selector: 'app-book-details',
  templateUrl: './book-details.page.html',
  styleUrls: ['./book-details.page.scss'],
  imports: [
    CommonModule,
    IonicModule,
    RouterModule
  ]
})
export class BookDetailsPage implements OnInit {
  book: Book | null = null;
  loading = true;
  error: string | null = null;
  safeDescription: SafeHtml | null = null;
  addingToLibrary = false;

  constructor(
    private route: ActivatedRoute,
    private apiService: ApiService,
    private libraryService: LibraryService,
    private sanitizer: DomSanitizer,
    private toastController: ToastController
  ) {}

  ngOnInit() {
    const id = this.route.snapshot.paramMap.get('id');
    if (!id) {
      this.error = 'ID du livre non trouvé';
      this.loading = false;
      return;
    }

    this.apiService.getBookDetails(id).subscribe({
      next: (book) => {
        this.book = book;
        if (book.description) {
          this.safeDescription = this.sanitizer.bypassSecurityTrustHtml(book.description);
        }
        this.loading = false;
      },
      error: (error: Error) => {
        this.error = 'Erreur lors du chargement des détails du livre';
        this.loading = false;
        console.error('Erreur de chargement:', error);
      }
    });
  }

  async addToLibrary() {
    if (!this.book) return;

    this.addingToLibrary = true;
    try {
      console.log('Tentative d\'ajout du livre à la bibliothèque:', this.book);
      await firstValueFrom(this.libraryService.addBookToLibrary({
        googleBooksId: this.book.id
      }));

      const toast = await this.toastController.create({
        message: 'Livre ajouté à votre bibliothèque',
        duration: 2000,
        position: 'bottom',
        color: 'success'
      });
      await toast.present();
    } catch (error: any) {
      console.error('Erreur lors de l\'ajout à la bibliothèque:', error);
      const message = error.error?.error || 'Erreur lors de l\'ajout à la bibliothèque';
      const toast = await this.toastController.create({
        message,
        duration: 2000,
        position: 'bottom',
        color: 'danger'
      });
      await toast.present();
    } finally {
      this.addingToLibrary = false;
    }
  }

  openPreview() {
    if (this.book?.previewLink) {
      window.open(this.book.previewLink, '_blank');
    }
  }

  openInfoLink() {
    if (this.book?.infoLink) {
      window.open(this.book.infoLink, '_blank');
    }
  }

  getLanguageName(code: string): string {
    const languages: { [key: string]: string } = {
      'fr': 'Français',
      'en': 'Anglais',
      'es': 'Espagnol',
      'de': 'Allemand',
      'it': 'Italien'
    };
    return languages[code] || code;
  }

  getDescription(book: any): string {
    if (!book || !book.description) return 'Aucun résumé disponible.';
    if (typeof book.description === 'object' && book.description.value) {
      return book.description.value;
    }
    if (typeof book.description === 'string') {
      return book.description;
    }
    return 'Aucun résumé disponible.';
  }
} 