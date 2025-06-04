export interface Book {
  id: string;
  title: string;
  authors?: string[];
  description?: string;
  publishedDate?: string;
  publisher?: string;
  imageLinks?: {
    thumbnail?: string;
    smallThumbnail?: string;
  };
  language?: string;
  pageCount?: number;
  categories?: string[];
  averageRating?: number;
  ratingsCount?: number;
  libraryBookId?: number;
}

export interface BookResponse extends Book {
  libraryBookId?: number;
}

export interface LibraryBookInfo {
  inLibrary: boolean;
  libraryBookId: number | null;
}

export interface BookSearchParams {
  query: string;
  lang?: string;
  maxResults?: number;
} 