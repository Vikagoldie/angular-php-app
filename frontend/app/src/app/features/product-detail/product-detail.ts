import { Component, OnInit } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { ProductService } from '../../services/product.service';
import { ProductModel } from '../../models/productModel';
import { CartService } from '../../services/cart.service';

@Component({
  selector: 'app-product-detail',
  imports: [RouterLink, FormsModule, DecimalPipe],
  templateUrl: './product-detail.html',
  styleUrl: './product-detail.scss',
})
export class ProductDetail implements OnInit {
  product: ProductModel | null = null;
  isLoading = true;
  notFound = false;
  quantity = 1;
  addedMessage = '';

  constructor(
    private route: ActivatedRoute,
    private productService: ProductService,
    private cartService: CartService
  ) {}

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));

    this.productService.getById(id).subscribe({
      next: (product) => {
        this.product = product && product.product_id ? product : null;
        this.notFound = !this.product;
        this.isLoading = false;
      },
      error: () => {
        this.notFound = true;
        this.isLoading = false;
      },
    });
  }

  get imageUrl(): string | null {
    return this.product ? this.productService.imageUrl(this.product.image_path) : null;
  }

  addToCart(): void {
    if (!this.product) {
      return;
    }

    this.cartService.add(this.product, this.quantity);
    this.addedMessage = `${this.quantity}x "${this.product.name}" wurde in den Warenkorb gelegt.`;
  }
}
