export interface Book {
  id: string;
  title: string;
  author: string;
  description: string;
  cover: string | null;
  first_publish_year: number | null;
  edition_count: number;
  publisher: string;
  pageCount: number | null;
  language: string;
  categories: string[];
  isbn: string | null;
  publishedDate?: string;
  previewLink?: string;
  infoLink?: string;
  industryIdentifiers?: Array<{
    type: string;
    identifier: string;
  }>;
  libraryBookId?: number;
}

export interface BookSearchResult {
  books: Book[];
  total: number;
  page: number;
  perPage: number;
} 