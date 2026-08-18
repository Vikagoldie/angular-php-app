import { Component, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { ProductService } from '../../services/product.service';
import { ProductModel } from '../../models/productModel';
import { ProductCard } from '../../shared/components/product-card/product-card';
import { CartService } from '../../services/cart.service';

@Component({
  selector: 'app-home',
  imports: [RouterLink, ProductCard],
  templateUrl: './home.html',
  styleUrl: './home.scss',
})
export class Home implements OnInit {
  featuredProducts: ProductModel[] = [];
  isLoading = true;

  constructor(private productService: ProductService, private cartService: CartService) {}

  ngOnInit(): void {
    this.productService.getAll({ sort: 'name', direction: 'ASC' }).subscribe({
      next: (products) => {
        this.featuredProducts = products.slice(0, 4);
        this.isLoading = false;
      },
      error: () => (this.isLoading = false),
    });
  }

  addToCart(product: ProductModel): void {
    this.cartService.add(product);
  }
}
