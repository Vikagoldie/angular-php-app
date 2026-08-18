import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { API_BASE_URL } from './api-config';
import { CartItem } from './cart.service';

export interface OrderLine {
  order_id: number;
  order_date: string;
  status: string;
  total_amount: number;
  product_name: string;
  quantity: number;
  unit_price: number;
  firstname?: string;
  lastname?: string;
}

interface CheckoutResponse {
  success: boolean;
  order_id?: number;
  message?: string;
  errors?: string[];
}

@Injectable({ providedIn: 'root' })
export class OrderService {
  constructor(private http: HttpClient) {}

  checkout(items: CartItem[]): Observable<CheckoutResponse> {
    const payload = {
      items: items.map((item) => ({
        product_id: item.product.product_id,
        quantity: item.quantity,
      })),
    };

    return this.http.post<CheckoutResponse>(`${API_BASE_URL}/OrderAPI.php?action=checkout`, payload);
  }

  getMyOrders(): Observable<OrderLine[]> {
    return this.http.get<OrderLine[]>(`${API_BASE_URL}/OrderAPI.php?action=mine`);
  }

  getAllOrders(): Observable<OrderLine[]> {
    return this.http.get<OrderLine[]>(`${API_BASE_URL}/OrderAPI.php?action=all`);
  }
}
