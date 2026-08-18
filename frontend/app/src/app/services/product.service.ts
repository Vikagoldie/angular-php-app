import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { API_BASE_URL } from './api-config';
import { toFormBody, formUrlEncodedHeaders } from './http-helpers';
import { ProductModel } from '../models/productModel';

export interface ProductQuery {
  search?: string;
  sort?: 'name' | 'price' | 'stock';
  direction?: 'ASC' | 'DESC';
}

export interface ProductFormData {
  name: string;
  description: string;
  price: number;
  stock: number;
}

interface SaveResponse {
  success: boolean;
  errors?: string[];
  message?: string;
  product_id?: number;
}

interface DeleteResponse {
  success: boolean;
  message?: string;
}

interface UploadResponse {
  success: boolean;
  errors?: string[];
  message?: string;
  image_path?: string;
}

/**
 * Kapselt alle Zugriffe auf ProductAPI.php, damit die Komponenten selbst
 * keine URLs oder HTTP-Details kennen müssen.
 */
@Injectable({ providedIn: 'root' })
export class ProductService {
  constructor(private http: HttpClient) {}

  getAll(query: ProductQuery = {}): Observable<ProductModel[]> {
    const params: Record<string, string> = {};
    if (query.search) params['search'] = query.search;
    if (query.sort) params['sort'] = query.sort;
    if (query.direction) params['direction'] = query.direction;

    return this.http.get<ProductModel[]>(`${API_BASE_URL}/ProductAPI.php`, { params });
  }

  getById(id: number): Observable<ProductModel> {
    return this.http.get<ProductModel>(`${API_BASE_URL}/ProductAPI.php`, { params: { id } });
  }

  create(product: ProductFormData): Observable<SaveResponse> {
    return this.http.post<SaveResponse>(`${API_BASE_URL}/ProductAPI.php`, toFormBody(product), formUrlEncodedHeaders());
  }

  update(id: number, product: ProductFormData): Observable<SaveResponse> {
    return this.http.put<SaveResponse>(
      `${API_BASE_URL}/ProductAPI.php?id=${id}`,
      toFormBody(product),
      formUrlEncodedHeaders()
    );
  }

  delete(id: number): Observable<DeleteResponse> {
    return this.http.delete<DeleteResponse>(`${API_BASE_URL}/ProductAPI.php?id=${id}`);
  }

  uploadImage(productId: number, file: File): Observable<UploadResponse> {
    const formData = new FormData();
    formData.set('product_id', String(productId));
    formData.set('image', file);

    return this.http.post<UploadResponse>(`${API_BASE_URL}/ProductAPI.php?action=upload`, formData);
  }

  imageUrl(imagePath: string | null | undefined): string | null {
    return imagePath ? `${API_BASE_URL}/${imagePath}` : null;
  }
}
