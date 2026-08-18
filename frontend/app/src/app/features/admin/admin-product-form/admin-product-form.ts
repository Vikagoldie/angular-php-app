import { Component, OnInit, inject } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ProductService } from '../../../services/product.service';

@Component({
  selector: 'app-admin-product-form',
  imports: [ReactiveFormsModule, RouterLink],
  templateUrl: './admin-product-form.html',
  styleUrl: './admin-product-form.scss',
})
export class AdminProductForm implements OnInit {
  private fb = inject(FormBuilder);

  form = this.fb.group({
    name: ['', Validators.required],
    description: [''],
    price: [0, [Validators.required, Validators.min(0)]],
    stock: [0, [Validators.required, Validators.min(0)]],
  });

  productId: number | null = null;
  isEditMode = false;
  isLoading = false;
  isSubmitting = false;
  serverErrors: string[] = [];

  selectedFile: File | null = null;
  imagePreviewUrl: string | null = null;
  uploadErrors: string[] = [];

  constructor(
    private productService: ProductService,
    private route: ActivatedRoute,
    private router: Router
  ) {}

  get name() {
    return this.form.controls.name;
  }

  get price() {
    return this.form.controls.price;
  }

  get stock() {
    return this.form.controls.stock;
  }

  ngOnInit(): void {
    const idParam = this.route.snapshot.paramMap.get('id');

    if (!idParam) {
      return;
    }

    this.productId = Number(idParam);
    this.isEditMode = true;
    this.isLoading = true;

    this.productService.getById(this.productId).subscribe({
      next: (product) => {
        this.form.patchValue({
          name: product.name,
          description: product.description,
          price: product.price,
          stock: product.stock,
        });
        this.imagePreviewUrl = this.productService.imageUrl(product.image_path);
        this.isLoading = false;
      },
      error: () => (this.isLoading = false),
    });
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    this.selectedFile = input.files?.[0] ?? null;
  }

  onSubmit(): void {
    this.serverErrors = [];
    this.uploadErrors = [];

    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.isSubmitting = true;
    const data = this.form.getRawValue();
    const payload = {
      name: data.name!,
      description: data.description ?? '',
      price: data.price!,
      stock: data.stock!,
    };

    const save$ = this.isEditMode && this.productId
      ? this.productService.update(this.productId, payload)
      : this.productService.create(payload);

    save$.subscribe({
      next: (response) => {
        if (!response.success) {
          this.isSubmitting = false;
          this.serverErrors = response.errors ?? [response.message ?? 'Speichern fehlgeschlagen.'];
          return;
        }

        const id = this.productId ?? response.product_id!;
        this.uploadImageIfSelected(id);
      },
      error: () => {
        this.isSubmitting = false;
        this.serverErrors = ['Der Server ist aktuell nicht erreichbar.'];
      },
    });
  }

  private uploadImageIfSelected(productId: number): void {
    if (!this.selectedFile) {
      this.isSubmitting = false;
      this.router.navigate(['/admin/products']);
      return;
    }

    this.productService.uploadImage(productId, this.selectedFile).subscribe({
      next: (response) => {
        this.isSubmitting = false;

        if (response.success) {
          this.router.navigate(['/admin/products']);
        } else {
          this.uploadErrors = response.errors ?? [response.message ?? 'Bild-Upload fehlgeschlagen.'];
        }
      },
      error: () => {
        this.isSubmitting = false;
        this.uploadErrors = ['Der Bild-Upload ist fehlgeschlagen.'];
      },
    });
  }
}
