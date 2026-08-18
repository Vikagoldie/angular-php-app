import { Component, OnInit } from '@angular/core';
import { UserModel } from '../../../models/userModel';
import { UserService } from '../../../services/user.service';
import { AuthService } from '../../../services/auth.service';

@Component({
  selector: 'app-admin-users',
  imports: [],
  templateUrl: './admin-users.html',
  styleUrl: './admin-users.scss',
})
export class AdminUsers implements OnInit {
  users: UserModel[] = [];
  isLoading = true;
  errorMessage = '';

  constructor(private userService: UserService, protected authService: AuthService) {}

  ngOnInit(): void {
    this.load();
  }

  delete(user: UserModel): void {
    if (user.user_id === this.authService.currentUser()?.user_id) {
      this.errorMessage = 'Du kannst dich nicht selbst löschen.';
      return;
    }

    if (!confirm(`Benutzer "${user.firstname} ${user.lastname}" wirklich löschen?`)) {
      return;
    }

    this.userService.delete(user.user_id).subscribe({
      next: () => this.load(),
      error: () => (this.errorMessage = 'Löschen fehlgeschlagen.'),
    });
  }

  private load(): void {
    this.isLoading = true;
    this.userService.getUsers().subscribe({
      next: (users) => {
        this.users = users;
        this.isLoading = false;
      },
      error: () => {
        this.errorMessage = 'Die Benutzer konnten nicht geladen werden.';
        this.isLoading = false;
      },
    });
  }
}
