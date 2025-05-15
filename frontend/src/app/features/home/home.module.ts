import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { RouterModule, Routes } from '@angular/router';
import { HomePage } from './pages/home/home.page';
import { HomeSearchBarComponent } from './components/home-search-bar/home-search-bar.component';
import { BookSectionComponent } from './components/book-section/book-section.component';
import { BookCardComponent } from './components/book-card/book-card.component';

const routes: Routes = [
  { path: '', component: HomePage }
];

@NgModule({
  declarations: [
    HomePage,
    HomeSearchBarComponent,
    BookSectionComponent,
    BookCardComponent
  ],
  imports: [
    CommonModule,
    IonicModule,
    RouterModule.forChild(routes)
  ]
})
export class HomeModule {} 