import { Component, EventEmitter, Input, Output } from '@angular/core';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-search-bar',
  imports: [FormsModule],
  templateUrl: './search-bar.html',
  styleUrl: './search-bar.scss',
})
export class SearchBar {
  @Input() placeholder = 'Geschenk suchen...';
  @Input() value = '';
  @Output() search = new EventEmitter<string>();

  onSubmit(): void {
    this.search.emit(this.value.trim());
  }
}
