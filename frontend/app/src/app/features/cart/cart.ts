import { Component } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { RouterLink } from '@angular/router';
import { CartService } from '../../services/cart.service';

@Component({
  selector: 'app-cart',
  imports: [RouterLink, DecimalPipe],
  templateUrl: './cart.html',
  styleUrl: './cart.scss',
})
export class Cart {
  constructor(protected cartService: CartService) {}

  updateQuantity(productId: number, value: string): void {
    this.cartService.updateQuantity(productId, Number(value));
  }

  remove(productId: number): void {
    this.cartService.remove(productId);
  }
}
