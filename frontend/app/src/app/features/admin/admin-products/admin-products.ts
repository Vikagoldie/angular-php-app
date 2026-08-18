import { Component, OnInit } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { RouterLink } from '@angular/router';
import { ProductModel } from '../../../models/productModel';
import { ProductService } from '../../../services/product.service';

@Component({
  selector: 'app-admin-products',
  imports: [RouterLink, DecimalPipe],
  templateUrl: './admin-products.html',
  styleUrl: './admin-products.scss',
})
export class AdminProducts implements OnInit {
  products: ProductModel[] = [];
  isLoading = true;
  errorMessage = '';

  constructor(private productService: ProductService) {}

  ngOnInit(): void {
    this.load();
  }

  delete(product: ProductModel): void {
    if (!confirm(`"${product.name}" wirklich löschen?`)) {
      return;
    }

    this.productService.delete(product.product_id).subscribe({
      next: (response) => {
        if (response.success) {
          this.load();
        } else {
          this.errorMessage = response.message ?? 'Löschen fehlgeschlagen.';
        }
      },
      error: () => (this.errorMessage = 'Der Server ist aktuell nicht erreichbar.'),
    });
  }

  private load(): void {
    this.isLoading = true;
    this.productService.getAll().subscribe({
      next: (products) => {
        this.products = products;
        this.isLoading = false;
      },
      error: () => {
        this.errorMessage = 'Die Produkte konnten nicht geladen werden.';
        this.isLoading = false;
      },
    });
  }
}
