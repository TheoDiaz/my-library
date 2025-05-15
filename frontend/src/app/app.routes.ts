import { Routes } from '@angular/router';
import { LoginPage } from './features/auth/pages/login/login.page';
import { RegisterPage } from './features/auth/pages/register/register.page';
import { TabsPage } from './features/tabs/tabs.page';
import { AuthGuard } from './core/guards/auth.guard';

export const routes: Routes = [
  {
    path: '',
    redirectTo: 'tabs',
    pathMatch: 'full'
  },
  {
    path: 'auth',
    children: [
      {
        path: 'login',
        component: LoginPage
      },
      {
        path: 'register',
        component: RegisterPage
      }
    ]
  },
  {
    path: 'tabs',
    component: TabsPage,
    canActivate: [AuthGuard],
    children: [
      {
        path: 'home',
        loadComponent: () => import('./features/home/pages/home/home.page').then(m => m.HomePage)
      },
      {
        path: 'livres',
        loadComponent: () => import('./features/search/pages/search/search.page').then(m => m.SearchPage)
      },
      {
        path: 'stats',
        loadComponent: () => import('./features/stats/pages/stats/stats.page').then(m => m.StatsPage)
      },
      {
        path: 'chat',
        loadComponent: () => import('./features/chat/pages/chat/chat.page').then(m => m.ChatPage)
      },
      {
        path: 'libraire',
        loadComponent: () => import('./features/librarian/pages/librarian/librarian.page').then(m => m.LibrarianPage)
      },
      {
        path: '',
        redirectTo: 'home',
        pathMatch: 'full'
      }
    ]
  },
  {
    path: '**',
    redirectTo: 'tabs'
  }
];
