import { Injectable, computed, signal } from '@angular/core';
import { ProductModel } from '../models/productModel';

export interface CartItem {
  product: ProductModel;
  quantity: number;
}

const STORAGE_KEY = 'giftshop_cart';
@Injectable({ providedIn: 'root' })
export class CartService {
  private readonly itemsSignal = signal<CartItem[]>(loadFromStorage());

  readonly items = this.itemsSignal.asReadonly();
  readonly totalCount = computed(() => this.itemsSignal().reduce((sum, item) => sum + item.quantity, 0));
  readonly totalPrice = computed(() =>
    this.itemsSignal().reduce((sum, item) => sum + item.product.price * item.quantity, 0)
  );

  add(product: ProductModel, quantity = 1): void {
    const items = [...this.itemsSignal()];
    const existing = items.find((item) => item.product.product_id === product.product_id);

    if (existing) {
      existing.quantity += quantity;
    } else {
      items.push({ product, quantity });
    }

    this.save(items);
  }

  updateQuantity(productId: number, quantity: number): void {
    if (quantity < 1) {
      this.remove(productId);
      return;
    }

    const items = this.itemsSignal().map((item) =>
      item.product.product_id === productId ? { ...item, quantity } : item
    );
    this.save(items);
  }

  remove(productId: number): void {
    this.save(this.itemsSignal().filter((item) => item.product.product_id !== productId));
  }

  clear(): void {
    this.save([]);
  }

  private save(items: CartItem[]): void {
    this.itemsSignal.set(items);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
  }
}

function loadFromStorage(): CartItem[] {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}
