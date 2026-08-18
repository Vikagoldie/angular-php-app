import { Component, OnInit, inject } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { AuthService } from '../../services/auth.service';
import { UserService } from '../../services/user.service';

@Component({
  selector: 'app-profile',
  imports: [ReactiveFormsModule],
  templateUrl: './profile.html',
  styleUrl: './profile.scss',
})
export class Profile implements OnInit {
  private fb = inject(FormBuilder);

  form = this.fb.group({
    firstname: ['', Validators.required],
    lastname: ['', Validators.required],
    email: ['', [Validators.required, Validators.email]],
    password: [''],
  });

  isLoading = true;
  isSubmitting = false;
  serverErrors: string[] = [];
  successMessage = '';

  constructor(
    private authService: AuthService,
    private userService: UserService
  ) {}

  get firstname() {
    return this.form.controls.firstname;
  }

  get lastname() {
    return this.form.controls.lastname;
  }

  get email() {
    return this.form.controls.email;
  }

  ngOnInit(): void {
    const userId = this.authService.currentUser()?.user_id;

    if (!userId) {
      this.isLoading = false;
      return;
    }

    this.userService.getById(userId).subscribe({
      next: (user) => {
        this.form.patchValue({ firstname: user.firstname, lastname: user.lastname, email: user.email });
        this.isLoading = false;
      },
      error: () => (this.isLoading = false),
    });
  }

  onSubmit(): void {
    const userId = this.authService.currentUser()?.user_id;
    this.serverErrors = [];
    this.successMessage = '';

    if (!userId || this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.isSubmitting = true;
    const { firstname, lastname, email, password } = this.form.getRawValue();

    this.userService
      .updateProfile(userId, {
        firstname: firstname!,
        lastname: lastname!,
        email: email!,
        password: password || undefined,
      })
      .subscribe({
        next: (response) => {
          this.isSubmitting = false;

          if (response.success) {
            this.successMessage = 'Deine Daten wurden gespeichert.';
            this.form.patchValue({ password: '' });
          } else {
            this.serverErrors = response.errors ?? ['Speichern fehlgeschlagen.'];
          }
        },
        error: () => {
          this.isSubmitting = false;
          this.serverErrors = ['Der Server ist aktuell nicht erreichbar.'];
        },
      });
  }
}
