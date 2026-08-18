import { Component } from '@angular/core';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { AuthService } from '../../../services/auth.service';
import { CartService } from '../../../services/cart.service';
import { SearchBar } from '../search-bar/search-bar';

@Component({
  selector: 'app-header',
  imports: [RouterLink, RouterLinkActive, SearchBar],
  templateUrl: './header.html',
  styleUrl: './header.scss',
})
export class Header {
  isMenuOpen = false;

  constructor(
    protected authService: AuthService,
    protected cartService: CartService,
    private router: Router
  ) {}

  toggleMenu(): void {
    this.isMenuOpen = !this.isMenuOpen;
  }

  onSearch(term: string): void {
    this.isMenuOpen = false;
    this.router.navigate(['/products'], { queryParams: { search: term || null } });
  }

  logout(): void {
    this.authService.logout().subscribe(() => this.router.navigate(['/']));
  }
}
