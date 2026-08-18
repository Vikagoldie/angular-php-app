import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ProductService, ProductQuery } from '../../services/product.service';
import { ProductModel } from '../../models/productModel';
import { ProductCard } from '../../shared/components/product-card/product-card';
import { SearchBar } from '../../shared/components/search-bar/search-bar';
import { CartService } from '../../services/cart.service';

@Component({
  selector: 'app-products',
  imports: [ProductCard, SearchBar],
  templateUrl: './products.html',
  styleUrl: './products.scss',
})
export class Products implements OnInit {
  products: ProductModel[] = [];
  isLoading = true;
  errorMessage = '';

  searchTerm = '';
  sort: NonNullable<ProductQuery['sort']> = 'name';
  direction: NonNullable<ProductQuery['direction']> = 'ASC';

  constructor(
    private productService: ProductService,
    private cartService: CartService,
    private route: ActivatedRoute,
    private router: Router
  ) {}

  ngOnInit(): void {
    // Reagiert auf Query-Parameter (z. B. von der Kopfzeilen-Suche gesetzt)
    // und lädt die Produktliste jedes Mal neu, ohne die Seite neu zu laden.
    this.route.queryParamMap.subscribe((params) => {
      this.searchTerm = params.get('search') ?? '';
      this.loadProducts();
    });
  }

  onSearch(term: string): void {
    this.router.navigate([], {
      relativeTo: this.route,
      queryParams: { search: term || null },
      queryParamsHandling: 'merge',
    });
  }

  onSortChange(value: string): void {
    const [sort, direction] = value.split('-') as [typeof this.sort, typeof this.direction];
    this.sort = sort;
    this.direction = direction;
    this.loadProducts();
  }

  addToCart(product: ProductModel): void {
    this.cartService.add(product);
  }

  private loadProducts(): void {
    this.isLoading = true;
    this.errorMessage = '';

    this.productService
      .getAll({ search: this.searchTerm, sort: this.sort, direction: this.direction })
      .subscribe({
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
