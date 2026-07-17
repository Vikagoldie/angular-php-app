import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { UserService } from '../services/user.service';

@Component({
  selector: 'app-user',
  templateUrl: './user.html',
  imports: [CommonModule],
  styleUrl: './user.scss',
})
export class User {
  users: any[] = [];

  constructor(private userService: UserService) {}

  ngOnInit(): void {
    this.userService.getUsers().subscribe((data: any) => {
      this.users = data as any[];
    });
  }
}
