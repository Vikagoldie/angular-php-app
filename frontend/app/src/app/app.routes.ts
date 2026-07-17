import { Routes } from '@angular/router';

import { MainLayout } from './layouts/layout/main-layout';

import { Home } from './features/home/home';
import { Products } from './features/products/products';
import { ProductDetail } from './features/product-detail/product-detail';
import { Cart } from './features/cart/cart';
import { Login } from './features/login/login';
import { Register } from './features/register/register';
import { Profile } from './features/profile/profile';
import { Checkout } from './features/checkout/checkout';

export const routes: Routes = [
  {
    path: '',
    component: MainLayout,

    children: [
      {
        path: '',
        component: Home,
      },

      {
        path: 'products',
        component: Products,
      },

      {
        path: 'products/:id',
        component: ProductDetail,
      },

      {
        path: 'cart',
        component: Cart,
      },

      {
        path: 'login',
        component: Login,
      },

      {
        path: 'register',
        component: Register,
      },

      {
        path: 'profile',
        component: Profile,
      },

      {
        path: 'checkout',
        component: Checkout,
      },
    ],
  },
];
