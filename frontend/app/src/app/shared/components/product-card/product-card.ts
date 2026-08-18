import { Component, EventEmitter, Input, Output } from '@angular/core';
import { DecimalPipe } from '@angular/common';
import { RouterLink } from '@angular/router';
import { ProductModel } from '../../../models/productModel';
import { ProductService } from '../../../services/product.service';

@Component({
  selector: 'app-product-card',
  imports: [RouterLink, DecimalPipe],
  templateUrl: './product-card.html',
  styleUrl: './product-card.scss',
})
export class ProductCard {
  @Input({ required: true }) product!: ProductModel;
  @Output() addToCart = new EventEmitter<ProductModel>();

  constructor(private productService: ProductService) {}

  get imageUrl(): string | null {
    return this.productService.imageUrl(this.product.image_path);
  }
}
