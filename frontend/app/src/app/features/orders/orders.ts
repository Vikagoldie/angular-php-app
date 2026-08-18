import { Component, OnInit } from '@angular/core';
import { DatePipe, DecimalPipe } from '@angular/common';
import { OrderLine, OrderService } from '../../services/order.service';

interface GroupedOrder {
  order_id: number;
  order_date: string;
  status: string;
  total_amount: number;
  lines: OrderLine[];
}

@Component({
  selector: 'app-orders',
  imports: [DatePipe, DecimalPipe],
  templateUrl: './orders.html',
  styleUrl: './orders.scss',
})
export class Orders implements OnInit {
  groupedOrders: GroupedOrder[] = [];
  isLoading = true;
  errorMessage = '';

  constructor(private orderService: OrderService) {}

  ngOnInit(): void {
    this.orderService.getMyOrders().subscribe({
      next: (lines) => {
        this.groupedOrders = groupByOrder(lines);
        this.isLoading = false;
      },
      error: () => {
        this.errorMessage = 'Die Bestellungen konnten nicht geladen werden.';
        this.isLoading = false;
      },
    });
  }
}

function groupByOrder(lines: OrderLine[]): GroupedOrder[] {
  const map = new Map<number, GroupedOrder>();

  for (const line of lines) {
    if (!map.has(line.order_id)) {
      map.set(line.order_id, {
        order_id: line.order_id,
        order_date: line.order_date,
        status: line.status,
        total_amount: line.total_amount,
        lines: [],
      });
    }

    map.get(line.order_id)!.lines.push(line);
  }

  return Array.from(map.values());
}
