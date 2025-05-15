import { Component } from '@angular/core';
import { IonApp, IonRouterOutlet } from '@ionic/angular/standalone';
import { AuthService } from './core/services/auth.service';

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  standalone: true,
  imports: [IonApp, IonRouterOutlet]
})
export class AppComponent {
  constructor(private authService: AuthService) {
    // Initialisation de l'état d'authentification
    this.authService.isAuthenticated$.subscribe();
  }
}
