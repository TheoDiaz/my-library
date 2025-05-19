import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { NYTService } from '../../core/services/nyt.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-best-sellers',
  templateUrl: './best-sellers.component.html',
  styleUrls: ['./best-sellers.component.scss'],
  standalone: true,
  imports: [CommonModule, IonicModule]
})
export class BestSellersComponent implements OnInit {
  bestSellers: any[] = [];
  loading = true;
  error: string | null = null;

  constructor(private nytService: NYTService, private router: Router) {}

  ngOnInit() {
    this.loadBestSellers();
  }

  loadBestSellers() {
    this.loading = true;
    this.error = null;
    console.log('[BestSellers] Chargement des best-sellers...');
    this.nytService.getBestSellers().subscribe({
      next: (response) => {
        console.log('[BestSellers] Réponse reçue :', response);
        if (response.status === 'success') {
          this.bestSellers = response.data;
          console.log('[BestSellers] Livres reçus :', this.bestSellers);
        } else {
          this.error = 'Erreur lors du chargement des best-sellers';
          console.error('[BestSellers] Erreur API :', response);
        }
        this.loading = false;
      },
      error: (err) => {
        console.error('[BestSellers] Erreur lors du chargement des best-sellers:', err);
        this.error = 'Erreur lors du chargement des best-sellers';
        this.loading = false;
      }
    });
  }

  goToBookDetail(id: string) {
    if (!id) {
      console.error('[BestSellers] Pas d\'identifiant fourni pour la navigation');
      return;
    }
    console.log('[BestSellers] Navigation vers le livre:', id);
    this.router.navigate(['/tabs/livres/details', id]);
  }
} 