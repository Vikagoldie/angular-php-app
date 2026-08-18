import { Routes } from '@angular/router';
import { AdminLayout } from './admin-layout/admin-layout';
import { AdminProducts } from './admin-products/admin-products';
import { AdminProductForm } from './admin-product-form/admin-product-form';
import { AdminOrders } from './admin-orders/admin-orders';
import { AdminUsers } from './admin-users/admin-users';

// Diese Routen sind bereits über adminGuard in app.routes.ts abgesichert.
export const ADMIN_ROUTES: Routes = [
  {
    path: '',
    component: AdminLayout,
    children: [
      { path: '', redirectTo: 'products', pathMatch: 'full' },
      { path: 'products', component: AdminProducts },
      { path: 'products/new', component: AdminProductForm },
      { path: 'products/:id/edit', component: AdminProductForm },
      { path: 'orders', component: AdminOrders },
      { path: 'users', component: AdminUsers },
    ],
  },
];
