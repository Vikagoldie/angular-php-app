export interface ProductModel {
  product_id: number;
  name: string;
  description: string;
  price: number;
  stock: number;
  image_path: string | null;
}
