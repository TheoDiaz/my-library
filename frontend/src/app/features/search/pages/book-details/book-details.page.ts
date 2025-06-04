import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { RouterModule, ActivatedRoute, Router } from '@angular/router';
import { Location } from '@angular/common';
import { ApiService } from '../../../../core/services/api.service';
import { Book } from '../../models/book.interface';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';
import { LibraryService } from '../../../../core/services/library.service';
import { ToastController } from '@ionic/angular';
import { firstValueFrom } from 'rxjs';
import { NavigationService } from '../../../../core/services/navigation.service';

@Component({
  selector: 'app-book-details',
  templateUrl: './book-details.page.html',
  styleUrls: ['./book-details.page.scss'],
  standalone: true,
  imports: [
    CommonModule,
    IonicModule,
    RouterModule
  ]
})
export class BookDetailsPage implements OnInit, OnDestroy {
  book: Book | null = null;
  loading = true;
  error: string | null = null;
  safeDescription: SafeHtml | null = null;
  addingToLibrary = false;
  isPopoverOpen = false;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private location: Location,
    private apiService: ApiService,
    private libraryService: LibraryService,
    private sanitizer: DomSanitizer,
    private toastController: ToastController,
    private navigationService: NavigationService
  ) {
    console.log('BookDetailsPage - Constructor called');
  }

  ngOnInit() {
    console.log('BookDetailsPage - ngOnInit called');
    const id = this.route.snapshot.paramMap.get('id');
    console.log('BookDetailsPage - Book ID from route:', id);
    
    if (!id) {
      this.error = 'ID du livre non trouvé';
      this.loading = false;
      console.error('BookDetailsPage - No book ID found in route');
      return;
    }

    this.apiService.getBookDetails(id).subscribe({
      next: (book) => {
        console.log('BookDetailsPage - Book details loaded:', book);
        this.book = book;
        if (book.description) {
          this.safeDescription = this.sanitizer.bypassSecurityTrustHtml(book.description);
        }
        this.loading = false;
      },
      error: (error: Error) => {
        console.error('BookDetailsPage - Error loading book details:', error);
        this.error = 'Erreur lors du chargement des détails du livre';
        this.loading = false;
      }
    });
  }

  ngOnDestroy() {
    console.log('BookDetailsPage - Component destroyed');
  }

  handleBack() {
    console.log('BookDetailsPage - Back button clicked');
    const previousUrl = this.navigationService.getPreviousUrl();
    console.log('BookDetailsPage - Previous URL:', previousUrl);

    if (previousUrl.includes('/tabs/home')) {
      console.log('BookDetailsPage - Coming from home page, navigating to home');
      this.router.navigate(['/tabs/home']);
    } else if (previousUrl.includes('/tabs/livres')) {
      console.log('BookDetailsPage - Coming from library page, navigating to library');
      this.router.navigate(['/tabs/livres']);
    } else {
      console.log('BookDetailsPage - Using location.back()');
      this.location.back();
    }
  }

  openBookActions(event: Event) {
    this.isPopoverOpen = true;
  }

  async addToLibrary() {
    if (!this.book) return;

    this.addingToLibrary = true;
    try {
      await firstValueFrom(this.libraryService.addBookToLibrary({
        googleBooksId: this.book.id,
        status: 'to_read'
      }));

      const toast = await this.toastController.create({
        message: 'Livre ajouté à votre bibliothèque',
        duration: 2000,
        position: 'bottom',
        color: 'success'
      });
      await toast.present();
      this.isPopoverOpen = false;
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

  async addToWishlist() {
    if (!this.book) return;

    try {
      await firstValueFrom(this.libraryService.addBookToWishlist({
        googleBooksId: this.book.id
      }));

      const toast = await this.toastController.create({
        message: 'Livre ajouté à votre wishlist',
        duration: 2000,
        position: 'bottom',
        color: 'success'
      });
      await toast.present();
      this.isPopoverOpen = false;
    } catch (error: any) {
      console.error('Erreur lors de l\'ajout à la wishlist:', error);
      const message = error.error?.error || 'Erreur lors de l\'ajout à la wishlist';
      const toast = await this.toastController.create({
        message,
        duration: 2000,
        position: 'bottom',
        color: 'danger'
      });
      await toast.present();
    }
  }

  async updateReadingStatus(status: 'reading' | 'to_read' | 'read') {
    if (!this.book || typeof this.book.libraryBookId !== 'number') {
      const toast = await this.toastController.create({
        message: 'Ajoutez d\'abord ce livre à votre bibliothèque.',
        duration: 2000,
        position: 'bottom',
        color: 'danger'
      });
      await toast.present();
      return;
    }

    try {
      await firstValueFrom(this.libraryService.updateBookStatus({
        id: this.book.libraryBookId,
        status
      }));

      const toast = await this.toastController.create({
        message: 'Statut de lecture mis à jour',
        duration: 2000,
        position: 'bottom',
        color: 'success'
      });
      await toast.present();
      this.isPopoverOpen = false;
    } catch (error: any) {
      console.error('Erreur lors de la mise à jour du statut:', error);
      const message = error.error?.error || 'Erreur lors de la mise à jour du statut';
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
      await firstValueFrom(this.libraryService.removeBookFromLibrary(this.book.id));

      const toast = await this.toastController.create({
        message: 'Livre retiré de votre bibliothèque',
        duration: 2000,
        position: 'bottom',
        color: 'success'
      });
      await toast.present();
      this.isPopoverOpen = false;
    } catch (error: any) {
      console.error('Erreur lors du retrait de la bibliothèque:', error);
      const message = error.error?.error || 'Erreur lors du retrait de la bibliothèque';
      const toast = await this.toastController.create({
        message,
        duration: 2000,
        position: 'bottom',
        color: 'danger'
      });
      await toast.present();
    }
  }

  async removeFromWishlist() {
    if (!this.book) return;

    try {
      await firstValueFrom(this.libraryService.removeBookFromWishlist(this.book.id));

      const toast = await this.toastController.create({
        message: 'Livre retiré de votre wishlist',
        duration: 2000,
        position: 'bottom',
        color: 'success'
      });
      await toast.present();
      this.isPopoverOpen = false;
    } catch (error: any) {
      console.error('Erreur lors du retrait de la wishlist:', error);
      const message = error.error?.error || 'Erreur lors du retrait de la wishlist';
      const toast = await this.toastController.create({
        message,
        duration: 2000,
        position: 'bottom',
        color: 'danger'
      });
      await toast.present();
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