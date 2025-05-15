import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  IonTabs,
  IonTabBar,
  IonTabButton,
  IonIcon,
  IonLabel,
} from '@ionic/angular/standalone';
import { RouterModule } from '@angular/router';

@Component({
  standalone: true,
  selector: 'app-tabs',
  template: `
    <ion-tabs>
      <ion-tab-bar slot="bottom">
        <ion-tab-button tab="livres" [routerLink]="['/tabs/livres']">
          <ion-icon name="book-outline"></ion-icon>
          <ion-label>Livres</ion-label>
        </ion-tab-button>
        <ion-tab-button tab="stats" [routerLink]="['/tabs/stats']">
          <ion-icon name="trending-up-outline"></ion-icon>
          <ion-label>Stats</ion-label>
        </ion-tab-button>
        <ion-tab-button tab="home" [routerLink]="['/tabs/home']">
          <ion-icon name="home-outline"></ion-icon>
          <ion-label>Accueil</ion-label>
        </ion-tab-button>
        <ion-tab-button tab="chat" [routerLink]="['/tabs/chat']">
          <ion-icon name="chatbubble-ellipses-outline"></ion-icon>
          <ion-label>Chat</ion-label>
        </ion-tab-button>
        <ion-tab-button tab="libraire" [routerLink]="['/tabs/libraire']">
          <ion-icon name="person-outline"></ion-icon>
          <ion-label>Mon Libraire</ion-label>
        </ion-tab-button>
      </ion-tab-bar>
    </ion-tabs>
  `,
  imports: [
    CommonModule,
    RouterModule,
    IonTabs,
    IonTabBar,
    IonTabButton,
    IonIcon,
    IonLabel,
  ]
})
export class TabsPage {} 