export interface OrderModel {
  order_id: number;
  user_id: number;
  order_date: Date;
  status: string;
  total_amount: number;
}
