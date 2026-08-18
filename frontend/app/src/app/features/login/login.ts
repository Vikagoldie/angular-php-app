import { Component, inject } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-login',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './login.html',
  styleUrl: './login.scss',
})
export class Login {
  // inject() statt Konstruktor-Parameter, damit "form" beim Anlegen der
  // Klasseninstanz bereits auf "fb" zugreifen kann (Initialisierungsreihenfolge).
  private fb = inject(FormBuilder);

  form = this.fb.group({
    email: ['', [Validators.required, Validators.email]],
    password: ['', [Validators.required]],
  });

  serverErrors: string[] = [];
  isSubmitting = false;

  constructor(private authService: AuthService, private router: Router) {}

  get email() {
    return this.form.controls.email;
  }

  get password() {
    return this.form.controls.password;
  }

  onSubmit(): void {
    this.serverErrors = [];

    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.isSubmitting = true;
    const { email, password } = this.form.getRawValue();

    this.authService.login(email!, password!).subscribe({
      next: (response) => {
        this.isSubmitting = false;

        if (response.success) {
          this.router.navigate(['/']);
        } else {
          this.serverErrors = response.errors ?? ['Login fehlgeschlagen.'];
        }
      },
      error: () => {
        this.isSubmitting = false;
        this.serverErrors = ['Der Server ist aktuell nicht erreichbar.'];
      },
    });
  }
}
