import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent
} from '@ionic/angular/standalone';

@Component({
  standalone: true,
  selector: 'app-librarian',
  template: `
    <ion-header>
      <ion-toolbar>
        <ion-title>Mon Libraire</ion-title>
      </ion-toolbar>
    </ion-header>
    <ion-content class="ion-padding">
      <h2>Page Mon Libraire</h2>
    </ion-content>
  `,
  imports: [
    CommonModule,
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent
  ]
})
export class LibrarianPage {} 