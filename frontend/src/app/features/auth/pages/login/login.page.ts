import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonButton,
  IonInput,
  IonText,
  IonItem,
  IonLabel
} from '@ionic/angular/standalone';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { AuthService } from '../../../../core/services/auth.service';
import { Router } from '@angular/router';
import { firstValueFrom } from 'rxjs';

@Component({
  standalone: true,
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
  imports: [
    CommonModule,
    ReactiveFormsModule,
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
    IonButton,
    IonInput,
    IonText,
    IonItem,
    IonLabel
  ]
})
export class LoginPage {
  loginForm: FormGroup;
  showPassword = false;
  loading = false;
  error: string | null = null;

  constructor(
    private formBuilder: FormBuilder,
    private auth: AuthService,
    private router: Router
  ) {
    this.loginForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
    });
  }

  toggleShowPassword() {
    this.showPassword = !this.showPassword;
  }

  async onSubmit() {
    if (this.loginForm.valid) {
      this.loading = true;
      this.error = null;
      const { email, password } = this.loginForm.value;
      
      try {
        const response = await firstValueFrom(this.auth.login(email, password));
        await this.auth.setToken(response.token);
        this.router.navigate(['/tabs/home']);
      } catch (error) {
        console.error('Erreur de connexion:', error);
        this.error = 'Email ou mot de passe incorrect';
      } finally {
        this.loading = false;
      }
    }
  }
} 