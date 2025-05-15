import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RouterModule, Routes } from '@angular/router';
import { SearchPage } from './pages/search/search.page';
import { BookDetailsPage } from './pages/book-details/book-details.page';
import { BookCardComponent } from './components/book-card/book-card.component';
import { SearchBarComponent } from './components/search-bar/search-bar.component';
import { SharedModule } from '../../shared/shared.module';

const routes: Routes = [
  {
    path: '',
    component: SearchPage
  },
  {
    path: 'details/:id',
    component: BookDetailsPage
  }
];

@NgModule({
  declarations: [
    SearchPage,
    BookDetailsPage,
    BookCardComponent,
    SearchBarComponent
  ],
  imports: [
    CommonModule,
    IonicModule,
    FormsModule,
    ReactiveFormsModule,
    SharedModule,
    RouterModule.forChild(routes)
  ]
})
export class SearchModule { } 