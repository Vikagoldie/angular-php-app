import { Component } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { Router, RouterLink } from '@angular/router';
import { CartService } from '../../services/cart.service';
import { OrderService } from '../../services/order.service';

@Component({
  selector: 'app-checkout',
  imports: [RouterLink, DecimalPipe],
  templateUrl: './checkout.html',
  styleUrl: './checkout.scss',
})
export class Checkout {
  isSubmitting = false;
  errorMessage = '';

  constructor(
    protected cartService: CartService,
    private orderService: OrderService,
    private router: Router
  ) {}

  placeOrder(): void {
    this.errorMessage = '';
    this.isSubmitting = true;

    this.orderService.checkout(this.cartService.items()).subscribe({
      next: (response) => {
        this.isSubmitting = false;

        if (response.success) {
          this.cartService.clear();
          this.router.navigate(['/orders']);
        } else {
          this.errorMessage = response.message ?? response.errors?.join(', ') ?? 'Bestellung fehlgeschlagen.';
        }
      },
      error: () => {
        this.isSubmitting = false;
        this.errorMessage = 'Der Server ist aktuell nicht erreichbar.';
      },
    });
  }
}
