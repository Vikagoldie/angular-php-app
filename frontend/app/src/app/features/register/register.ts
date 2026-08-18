import { Component, inject } from '@angular/core';
import {
  AbstractControl,
  FormBuilder,
  ReactiveFormsModule,
  ValidationErrors,
  Validators,
} from '@angular/forms';
import { RouterLink } from '@angular/router';
import { AuthService } from '../../services/auth.service';

function passwordsMatch(control: AbstractControl): ValidationErrors | null {
  const password = control.get('password')?.value;
  const confirmPassword = control.get('confirmPassword')?.value;
  return password === confirmPassword ? null : { passwordMismatch: true };
}

@Component({
  selector: 'app-register',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './register.html',
  styleUrl: './register.scss',
})
export class Register {
  private fb = inject(FormBuilder);

  form = this.fb.group(
    {
      firstname: ['', Validators.required],
      lastname: ['', Validators.required],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8)]],
      confirmPassword: ['', Validators.required],
    },
    { validators: passwordsMatch }
  );

  serverErrors: string[] = [];
  successMessage = '';
  isSubmitting = false;

  constructor(private authService: AuthService) {}

  get firstname() {
    return this.form.controls.firstname;
  }

  get lastname() {
    return this.form.controls.lastname;
  }

  get email() {
    return this.form.controls.email;
  }

  get password() {
    return this.form.controls.password;
  }

  get confirmPassword() {
    return this.form.controls.confirmPassword;
  }

  onSubmit(): void {
    this.serverErrors = [];
    this.successMessage = '';

    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.isSubmitting = true;
    const { firstname, lastname, email, password } = this.form.getRawValue();

    this.authService
      .register({ firstname: firstname!, lastname: lastname!, email: email!, password: password! })
      .subscribe({
        next: (response) => {
          this.isSubmitting = false;

          if (response.success) {
            this.successMessage = 'Registrierung erfolgreich! Du kannst dich jetzt einloggen.';
            this.form.reset();
          } else {
            this.serverErrors = response.errors ?? ['Registrierung fehlgeschlagen.'];
          }
        },
        error: () => {
          this.isSubmitting = false;
          this.serverErrors = ['Der Server ist aktuell nicht erreichbar.'];
        },
      });
  }
}
