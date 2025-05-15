export interface Book {
  id: string;
  title: string;
  author: string;
  cover?: string;
  isbn?: string;
  publishYear?: number;
  description?: string;
  publisher?: string;
  language?: string;
  subjects?: string[];
  pages?: number;
}

export interface BookSearchResult {
  books: Book[];
  total: number;
  page: number;
  perPage: number;
} 