import { Component, OnInit } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { AuthService } from './services/auth.service';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet],
  templateUrl: './app.html',
  styleUrl: './app.scss',
})
export class App implements OnInit {
  protected title = 'GiftShop';

  constructor(private authService: AuthService) {}

  ngOnInit(): void {
    // Login-Status einmal beim Start laden, damit Header & Co. sofort wissen,
    // ob bereits eine gültige Session besteht (z. B. nach einem Seiten-Reload).
    this.authService.loadCurrentUser().subscribe();
  }
}
