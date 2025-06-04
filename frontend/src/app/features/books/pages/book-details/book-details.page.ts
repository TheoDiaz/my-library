import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { RouterModule } from '@angular/router';
import { LibraryService } from '../../../../core/services/library.service';
import { BookEventsService } from '../../../../core/services/book-events.service';
import { Router } from '@angular/router';
import { firstValueFrom } from 'rxjs';
import { ToastController } from '@ionic/angular';

@Component({
  selector: 'app-book-details',
  templateUrl: './book-details.page.html',
  styleUrls: ['./book-details.page.scss'],
  standalone: true,
  imports: [CommonModule, IonicModule, RouterModule]
})
export class BookDetailsPage implements OnInit {
  book: any;
  isPopoverOpen = false;
  addingToLibrary = false;

  constructor(
    private libraryService: LibraryService,
    private bookEvents: BookEventsService,
    private router: Router,
    private toastController: ToastController
  ) {}

  ngOnInit() {
    // Récupérer l'ID du livre depuis l'URL
    const bookId = this.router.url.split('/').pop();
    if (bookId) {
      // Charger les détails du livre
      this.libraryService.getBookDetails(bookId).subscribe({
        next: (book) => {
          this.book = book;
        },
        error: (error) => {
          console.error('Erreur lors du chargement des détails du livre:', error);
        }
      });
    }
  }

  async removeFromWishlist() {
    if (!this.book) return;

    try {
      await firstValueFrom(this.libraryService.removeBookFromWishlist(this.book.googleBooksId));
      console.log('Livre supprimé de la wishlist, déclenchement de la mise à jour');
      this.bookEvents.triggerWishlistUpdate();
      
      const toast = await this.toastController.create({
        message: 'Livre retiré de votre wishlist',
        duration: 2000,
        position: 'bottom',
        color: 'success'
      });
      await toast.present();
      this.isPopoverOpen = false;
      
      await this.router.navigate(['/tabs/livres/search'], { replaceUrl: true });
    } catch (error: any) {
      console.error('Erreur lors de la suppression de la wishlist:', error);
      const message = error.error?.error || 'Erreur lors de la suppression de la wishlist';
      const toast = await this.toastController.create({
        message,
        duration: 2000,
        position: 'bottom',
        color: 'danger'
      });
      await toast.present();
    }
  }

  async removeFromLibrary() {
    if (!this.book) return;

    try {
      await firstValueFrom(this.libraryService.removeBookFromLibrary(this.book.googleBooksId));
      console.log('Livre supprimé de la bibliothèque, déclenchement de la mise à jour');
      this.bookEvents.triggerLibraryUpdate();
      
      const toast = await this.toastController.create({
        message: 'Livre retiré de votre bibliothèque',
        duration: 2000,
        position: 'bottom',
        color: 'success'
      });
      await toast.present();
      this.isPopoverOpen = false;
      
      await this.router.navigate(['/tabs/livres/search'], { replaceUrl: true });
    } catch (error: any) {
      console.error('Erreur lors de la suppression de la bibliothèque:', error);
      const message = error.error?.error || 'Erreur lors de la suppression de la bibliothèque';
      const toast = await this.toastController.create({
        message,
        duration: 2000,
        position: 'bottom',
        color: 'danger'
      });
      await toast.present();
    }
  }
} 