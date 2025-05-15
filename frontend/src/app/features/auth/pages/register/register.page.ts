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
  IonLabel,
  IonCheckbox
} from '@ionic/angular/standalone';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { AuthService } from '../../../../core/services/auth.service';
import { Router } from '@angular/router';

@Component({
  standalone: true,
  selector: 'app-register',
  templateUrl: './register.page.html',
  styleUrls: ['./register.page.scss'],
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
    IonLabel,
    IonCheckbox
  ]
})
export class RegisterPage {
  registerForm: FormGroup;
  showPassword = false;
  loading = false;
  error: string | null = null;

  constructor(
    private fb: FormBuilder,
    private auth: AuthService,
    private router: Router
  ) {
    this.registerForm = this.fb.group({
      username: ['', [Validators.required]],
      email: ['', [Validators.required, Validators.email]],
      password: ['', Validators.required],
      newsletter: [false]
    });
  }

  toggleShowPassword() {
    this.showPassword = !this.showPassword;
  }

  async onSubmit() {
    if (this.registerForm.invalid) return;
    this.loading = true;
    this.error = null;
    const { username, email, password } = this.registerForm.value;
    this.auth.register({ username, email, password }).subscribe({
      next: () => {
        this.loading = false;
        this.router.navigate(['/login']);
      },
      error: (err) => {
        this.error = err?.error?.message || 'Erreur lors de l\'inscription';
        this.loading = false;
      }
    });
  }
} 